<?php
/**
 * Created by PhpStorm.
 * User: Bahti
 * Date: 3/15/2019
 * Time: 12:17 PM
 */

namespace App\Modules\Bosslike\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Requests\InfoSaveRequest;
use App\User;
use Illuminate\Support\Facades\Storage;

/**
 * Class MyInfoController
 * @package App\Modules\Bosslike\Controllers
 */
class MyInfoController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('bosslike::my-info');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addInfo()
    {
        return view('bosslike::my-info');
    }

    /**
     * @param InfoSaveRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InfoSaveRequest $request)
    {
        $user = User::findOrFail(\Auth::id());
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->gender = $request->input('gender');
        $file = $request->file('avatar');

        if ($file) {
            $ext = $file->getClientOriginalExtension();
            $filename = 'avatar' . time() . '.' . $ext;
            Storage::putFileAs('public/uploads/', $file, $filename);
            $user->avatar = $filename;

        } else {
            $filename = null;
        }


        $user->save();
        return redirect()->route('task.create')->with('success', 'Изменения сохранены');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
        return view('bosslike::info-edit', [
            'user' => User::find(\Auth::id()),
        ]);
    }

    /**
     * @param InfoSaveRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(InfoSaveRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->gender = $request->input('gender');

        $file = $request->file('avatar');

        if (filled($file)) {

            $ext = $file->getClientOriginalExtension();
            $filename = 'avatar' . time() . '.' . $ext;
            Storage::putFileAs('public/uploads/', $file, $filename);
            $user->avatar = $filename;
        }

        $user->save();
        return redirect()->route('task.create')->with('success', 'Изменения сохранены');
    }

}
