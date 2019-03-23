<?php

namespace App\Modules\Bosslike\Controllers;

use App\Http\Controllers\Controller;
use Session;
/**
 * Class SessionController
 * @package App\Modules\Bosslike\Controllers
 */
class SessionController extends Controller
{
    /**
     * @param $currentUser
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToSession($currentUser)
    {
        session(['usertype' => $currentUser]);
//        session(['current_user' =>$currentUser]);
//        $curUser = session('current_user');
//
        return response()->json(session('usertype'));
    }
}
