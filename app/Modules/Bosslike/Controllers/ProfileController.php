<?php

namespace App\Modules\Bosslike\Controllers;

use App\Http\Controllers\Controller;

/**
 * Class ProfileController
 * @package App\Modules\Bosslike\Controllers
 */
class ProfileController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('bosslike::profile');
    }
}
