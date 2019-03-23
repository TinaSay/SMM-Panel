<?php

namespace App\Modules\Bosslike\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp;

/**
 * Class ApiController
 * @package App\Modules\Bosslike\Controllers
 */
class ApiController extends Controller
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

    /**
     * @return int|mixed
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    public function getUserBalance()
    {
        $client = new GuzzleHttp\Client([
            'base_uri' => 'https://billing.smm-pro.uz'
        ]);
        $response = $client->request('POST', '/api/get-balance', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . session('user_token')
            ]
        ]);

        $res = json_decode((string)$response->getBody()->getContents());

        if (!$res == null) {
            return $res;
        } else {
            return 0;
        }
    }

    /**
     * @return string
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    public function getFormattedBalance()
    {
        $balance = $this->getUserBalance();
        return number_format($balance / 100, 0, '', ' ');
    }

    public function checkBalance($cost)
    {
        if ($this->getUserBalance() <> 0) {
            $rawBalance = $this->getUserBalance();
            $balance = $rawBalance / 100;
            $result= $balance - $cost;
            return response()->json($result);

        }

    }


}
