<?php

namespace App\Modules\Bosslike\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\GuzzleClient;
use App\Modules\Bosslike\Models\Social;
use App\Modules\Bosslike\Models\Task;
use App\Modules\Bosslike\Models\TaskDone;
use Illuminate\Http\Request;

/**
 * Class AllTasksController
 * @package App\Modules\Bosslike\Controllers\Admin
 */
class AllTasksController extends Controller
{
    /**
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * ApiController constructor.
     * @param GuzzleClient $client
     */
    public function __construct(GuzzleClient $client)
    {
        $this->guzzle = $client;
    }

    /**
     * @param Request $request
     * @param Task $task
     * @param null $social
     * @param null $service
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Task $task, $social = null, $service = null)
    {
        $all = true;
        if (!empty($social)) {
            $task->whereHas('service', function ($q) use ($social) {
                $q->where('social_id', $social);
            });
        }

        if (!empty($service)) {
            $all = false;
            $task->where('service_id', $service);
        }

        return view('bosslike::admin.all-tasks', [
            'tasks' => Task::limit(30)->get()->sortByDesc('created_at'),
            'socials' => Social::with('services')->where('name', '!=', 'Facebook')
                ->where('name', '!=', 'Twitter')->get(),
            'selected_social' => $social,
            'selected_service' => $service,
            'all' => $all
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($id)
    {
        $task = Task::findOrFail($id);

        $done = TaskDone::where('task_id', '=', $task->id)
            ->where('status', '=', TaskDone::DONE_TASK)
            ->count();

        $notDone = $task->amount - $done;
        $refund = $notDone * ($task->points * 2);

        if ($task->delete()) {
            $task->tasks_done()->delete();
            $task->transactions()->delete();
            $task->comments()->delete();

            if ($notDone > 0) {
                $this->guzzle->refundBalance($refund, $task->user->billing_id);
            }
        }

        return redirect()->route('tasks.list')->with('success', 'Задание удалено. Средства за невыполненные задания возмещены');
    }


}
