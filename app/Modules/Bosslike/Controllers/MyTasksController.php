<?php

namespace App\Modules\Bosslike\Controllers;

use App\Modules\Bosslike\Models\Task;
use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Requests\TaskSaveRequest;
use App\Modules\Bosslike\Requests\TaskUpdateRequest;
use http\Env\Response;
use GuzzleHttp\Client;

/**
 * Class MyTasksController
 * @package App\Modules\Bosslike\Controllers
 */
class MyTasksController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('bosslike::tasks.my-tasks', [
            'tasks' => Task::where('user_id', '=', \Auth::id())
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }

    /**
     * @param $id
     * @param TaskSaveRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAjax($id, TaskSaveRequest $request)
    {
        $task = Task::findOrFail($id);
        $task->points = $request->input('points');
        $task->amount = $request->input('amount');

        if ($task->save()) {
            return response()->json([
                'status' => 1,
                'message' => 'Изменения сохранены'
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Ошибка'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return redirect()->route('tasks.my');
    }


}
