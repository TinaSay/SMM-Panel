<?php

namespace App\Modules\Bosslike\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Requests\InfoSaveRequest;
use App\User;

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
        $user->gender = $request->input('gender');
        $file = $request->file('avatar');

        if ($file) {
            $avatar = self::storeAvatar($file);
            $user->avatar = $avatar;

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
        $user->gender = $request->input('gender');

        $file = $request->file('avatar');

        if (filled($file)) {
            $avatar = self::storeAvatar($file);
            $user->avatar = $avatar;
        }

        $user->save();
        return redirect()->route('task.create')->with('success', 'Изменения сохранены');
    }

    /**
     * @param $file
     * @return string
     */
    public static function storeAvatar($file)
    {
        $ext = $file->getClientOriginalExtension();
        $filename = 'avatar' . time() . '.' . $ext;
        $file->storeAs('uploads', $filename);
        return $filename;
    }

}
