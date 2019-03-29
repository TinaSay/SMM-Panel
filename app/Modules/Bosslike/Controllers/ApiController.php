<?php

namespace App\Modules\Bosslike\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\GuzzleClient;

/**
 * Class ApiController
 * @package App\Modules\Bosslike\Controllers
 */
class ApiController extends Controller
{
    /**
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * ApiController constructor.
     * @param GuzzleClient $client
     */
    public function __construct(GuzzleClient $client)
    {
        $this->guzzle = $client;
    }

    /**
     * @param $currentUser
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToSession($currentUser)
    {
        session(['usertype' => $currentUser]);
        return response()->json(session('usertype'));
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBalance()
    {
        return $this->guzzle->getFormattedBalance();
    }

}
