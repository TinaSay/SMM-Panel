<?php

namespace App\Modules\SmmPro\Controllers\Admin;

use App\User;
use App\Http\Controllers\Controller;

/**
 * Class UsersController
 * @package App\Http\Controllers\Admin
 */
class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('smmpro::users', [
            'users' => User::orderBy('created_at', 'desc')->get()
        ]);
    }
}
