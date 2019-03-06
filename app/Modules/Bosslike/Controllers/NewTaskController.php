<?php

namespace App\Modules\Bosslike\Controllers;

use App\Modules\Bosslike\Models\Service;
use App\Modules\Bosslike\Models\Social;
use App\Modules\Bosslike\Models\Task;
use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Requests\TaskSaveRequest;

/**
 * Class NewTaskController
 * @package App\Modules\Bosslike\Controllers
 */
class NewTaskController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('bosslike::tasks.create', [
            'socials' => Social::all()
        ]);
    }

    /**
     * @param TaskSaveRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(TaskSaveRequest $request)
    {
        $task = new Task();
        $task->user_id = \Auth::id();
        $task->service_id = $request->input('service_id');
        $task->link = $request->input('link');
        
        $task->points = $request->input('points');
        $task->amount = $request->input('amount');
        $task->save();

        return redirect('/tasks/my');
    }

    /**
     * @param $socialId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServicesAjax($socialId)
    {
        $services = Service::where('social_id', '=', $socialId)->get();
        return response()->json($services);
    }

}
