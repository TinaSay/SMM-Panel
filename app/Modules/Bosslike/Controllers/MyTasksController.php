<?php

namespace App\Modules\Bosslike\Controllers;

use App\Modules\Bosslike\Models\Task;
use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Models\TaskComments;
use App\Modules\Bosslike\Models\TaskDone;
use App\Modules\Bosslike\Requests\TaskSaveRequest;
use http\Env\Response;
use App\Helpers\GuzzleClient;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use App\Modules\Bosslike\Models\Social;
use GuzzleHttp\Client;

/**
 * Class MyTasksController
 * @package App\Modules\Bosslike\Controllers
 */
class MyTasksController extends Controller
{
    /**
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * NewTaskController constructor.
     * @param GuzzleClient $client
     */
    public function __construct(GuzzleClient $client)
    {
        $this->guzzle = $client;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Task $task, $social = null, $service = null)
    {

        $task = $task->newQuery();
        $all = true;
        $tasksDone = [];
        $task->where('user_id', '=', \Auth::id())
            ->orderBy('created_at', 'desc');


        if (!empty($social)) {
            $task->whereHas('service', function ($q) use ($social) {
                $q->where('social_id', $social);
            });
        }

        if (!empty($service)) {
            $all = false;
            $task->where('service_id', $service);
        }

        $tasks = $task->paginate(30);

        foreach ($tasks as $tk) {
            if ($tk->bosslike_id != null) {
                $tk->bl = $tk->bosslike();
            }
        }
        $socials = Social::with('services')->where('name', '!=', 'Facebook')
            ->where('name', '!=', 'Twitter')->get();

        foreach ($tasks as $task) {
            $done = TaskDone::where('task_id', '=', $task->id)
                ->where('status', '=', TaskDone::DONE_TASK)
                ->count();
            array_push($tasksDone, $done);

        }

        return view('bosslike::tasks.my-tasks', [
            'tasks' => $tasks,
            'tasksDone' => $tasksDone,
            'socials' => $socials,
            'selected_social' => $social,
            'selected_service' => $service,
            'all' => $all,
            'links' => $tasks->links()
        ]);

//        $tasksDone = [];
//        $tasks = Task::where('user_id', '=', \Auth::id())
//            ->orderBy('created_at', 'desc')
//            ->get();
//
//        foreach ($tasks as $task) {
//            $done = TaskDone::where('task_id', '=', $task->id)
//                ->where('status', '=', TaskDone::DONE_TASK)
//                ->count();
//            array_push($tasksDone, $done);
//
//        }
//
//        return view('bosslike::tasks.my-tasks', [
//            'tasks' => $tasks,
//            'tasksDone' => $tasksDone,
//        ]);
    }

    /**
     * @param $id
     * @param TaskSaveRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateAjax($id, TaskSaveRequest $request)
    {
        $task = Task::findOrFail($id);

        $currentCost = ($task->points * 2) * $task->amount;

        $done = TaskDone::where('task_id', '=', $task->id)
            ->where('status', '=', TaskDone::DONE_TASK)
            ->count();

        $done_points = $done * $task->points * 2;

        $task->points = $request->input('points');
        $task->amount = $request->input('amount');
        $left_amount = $task->amount - $done;

        $newCost = ($task->points * 2) * $left_amount;

        if (filled($request->input('sng_points')) and filled($request->input('sng_amounts'))) {
            $task->sng_points = $request->input('sng_points');
            $task->sng_amounts = $request->input('sng_amounts');
            $newCost = $newCost + ($task->sng_points * $task->sng_amounts);
        }

        $count_cost = $currentCost - $done_points;

        $balanceDiff = abs($count_cost - $newCost);

        $affordable = $this->guzzle->getUserBalance() / 100 - $balanceDiff;
        if ($affordable < 0) {
            return response()->json([
                'status' => 0,
                'message' => 'У вас не хватает средств!',
            ]);
        } else {

            if ($task->priority == 'sng' || $task->priority == 'uzbsng') {

                if ($task->priority == 'sng') {
                    $points = $request->input('points');
                    $amount = $request->input('amount');
                } elseif ($task->priority == 'uzbsng') {
                    $points = $request->input('sng_points');
                    $amount = $request->input('sng_amount');
                    $task->sng_points = $request->input('sng_points');
                    $task->sng_amount = $request->input('sng_amount');
                }

                $client = new Client();
                $url = 'https://api-public.bosslike.ru/v1/';
                $action = 'tasks/' . $task->bosslike_id . '/update/';
                $data = $client->request('PUT', $url . $action,
                    [
                        'headers' => [
                            'X-Api-Key' => env('BOSSLIKE_PUBLIC'),
                            'Accept' => 'application/json',
                        ],
                        'form_params' => [
                            'price' => $points,
                            'count' => $amount,
                        ],
                        'http_errors' => false
                    ]);
                $result = json_decode($data->getBody()->getContents());
            }

            if ($task->save()) {
                if ($newCost > $count_cost) {
                    $this->guzzle->chargeClient($balanceDiff);
                } elseif ($newCost < $count_cost) {
                    $this->guzzle->depositClient($balanceDiff);
                }

                if (filled($request->input('comment_text'))) {
                    $commentsArray = $request->input('comment_text');

                    $taskComments = TaskComments::where('task_id', '=', $task->id)->get();
                    foreach ($taskComments as $key => $taskComment) {
                        $taskComment->task_id = $task->id;
                        $taskComment->text = $commentsArray[$key];
                        $taskComment->save();
                    }
                }
                return response()->json([
                    'status' => 1,
                    'message' => 'Изменения сохранены',
                    'balanceDiff' => $balanceDiff
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Не сохранено!',
                ]);
            }
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($id)
    {
        $task = Task::findOrFail($id);

        if ($task->priority == 'sng' || $task->priority == 'uzbsng') {

            $task_data = $task->bosslike();

            $client = new Client();
            $url = 'https://api-public.bosslike.ru/v1/';
            $action = 'tasks/' . $task->bosslike_id . '/trash/';
            $data = $client->request('DELETE', $url . $action,
                [
                    'headers' => [
                        'X-Api-Key' => env('BOSSLIKE_PUBLIC'),
                        'Accept' => 'application/json',
                    ],
                    'http_errors' => false
                ]);
            $result = json_decode($data->getBody()->getContents());

            $notDone = $task_data->count - $task_data->count_complete;
            if ($task->priority == 'sng') {
                $points = $task->points;
            } elseif ($task->priority == 'uzbsng') {
                $points = $task->sng_points;
            }
            $refund = $notDone * ($points * 2);

        } else {

            $done = TaskDone::where('task_id', '=', $task->id)
                ->where('status', '=', TaskDone::DONE_TASK)
                ->count();

            $notDone = $task->amount - $done;
            $refund = $notDone * ($task->points * 2);

        }
        if ($task->delete()) {
            $task->tasks_done()->delete();
            $task->transactions()->delete();
            $task->comments()->delete();

            if ($notDone > 0) {
                $this->guzzle->refundBalance($refund, \Auth::user()->billing_id);
            }
        }


        return redirect()->route('tasks.my')->with('success', 'Задание удалено. Средства за невыполненные задания возмещены');
    }


}
