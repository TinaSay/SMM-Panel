<?php

namespace App\Modules\SmmPro\Controllers;

use App\Http\Controllers\Controller;

/**
 * Class DepositController
 * @package App\Http\Controllers\Dashboard
 */
class DepositController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('smmpro::deposit');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        $uid = (int)$_GET['uid'];
        $amount = (int)$_GET['amount'];
        date_default_timezone_set('Asia/Tashkent');
        $sign = new \StdClass;

        $sign->time = date('Y-m-d H:i:s');
        $sign->string = md5($sign->time . 'u5rI8DN8HZV' . '12564' . $uid . $amount);

        return response()->json($sign);
    }
}
