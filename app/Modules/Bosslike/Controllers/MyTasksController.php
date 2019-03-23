<?php

namespace App\Modules\Bosslike\Controllers;

use App\Modules\Bosslike\Models\Task;
use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Models\TaskDone;
use App\Modules\Bosslike\Requests\TaskSaveRequest;
use http\Env\Response;

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
        $tasksDone = [];
        $tasksSlaves = [];
        $tasks = Task::where('user_id', '=', \Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($tasks as $task) {
            $done = TaskDone::where('user_id', '=', \Auth::id())
                ->where('task_id', '=', $task->id)
                ->where('status', '=', TaskDone::DONE_TASK)
                ->count();

            array_push($tasksDone, $done);
        }


        return view('bosslike::tasks.my-tasks', [
            'tasks' => $tasks,
            'tasksDone' => $tasksDone,
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
        $task->tasks_done()->delete();
        $task->delete();
        return redirect()->route('tasks.my')->with('success', 'Удалено');
    }


}
