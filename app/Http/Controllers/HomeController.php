<?php

namespace App\Http\Controllers;

use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userInfo = User::where('id', '=', \Auth::id())
            ->where('first_name', '<>', null)
            ->first();

        if ($userInfo) {
            return redirect()->route('task.create');
        }
        return redirect('info/');
    }
}
