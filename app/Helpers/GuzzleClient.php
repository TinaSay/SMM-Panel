<?php

namespace App\Helpers;

use GuzzleHttp\Client;

/**
 * Class GuzzleClient
 * @package App\Helpers
 */
class GuzzleClient
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * GuzzleClient constructor.
     */
    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://billing.smm-pro.uz']);
    }

    /**
     * @return int|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserBalance()
    {
        $response = $this->client->request('POST', '/api/get-balance', [
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFormattedBalance()
    {
        $balance = $this->getUserBalance();
        return number_format($balance / 100, 0, '', ' ');
    }

    /**
     * @param $cost
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function chargeClient($cost)
    {
        $this->client->request('POST', '/api/charge', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . session('user_token')
            ],
            'form_params' => [
                'amount' => $cost,
                'description' => 'Оплата от пользователя ' . \Auth::user()->billing_id,
                'client' => \Config::get('services.oauthConfig.keys.id'),
            ]
        ]);
    }

    /**
     * @param $points
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function depositClient($points)
    {
        $this->client->request('POST', '/api/deposit', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . session('user_token')
            ],
            'form_params' => [
                'amount' => $points,
                'description' => 'Начисление денег пользователю ' . \Auth::user()->billing_id,
                'client' => \Config::get('services.oauthConfig.keys.id'),
            ]
        ]);
    }


    /**
     * @param $order
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refundUserBalance($order)
    {
        $this->client->request('POST', '/api/deposit', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . session('user_token')
            ],
            'form_params' => [
                'amount' => $order->charge,
                'description' => 'Возврат денег за заказ № ' . $order->order_api_id . ' пользователя ' . \Auth::user()->billing_id,
                'client' => \Config::get('services.oauthConfig.keys.id'),
            ]
        ]);
    }

    /**
     * @param $refund
     * @param $userId
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refundBalance($refund, $userId)
    {
        $this->client->request('POST', '/api/deposit', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . session('user_token')
            ],
            'form_params' => [
                'amount' => $refund,
                'description' => 'Возврат денег пользователя ' . \Auth::user()->billing_id,
                'client' => \Config::get('services.oauthConfig.keys.id'),
                'user_id' => $userId
            ]
        ]);
    }

    /**
     ***** Method might be used for Odnoklassniki
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserInfo()
    {
        $params = 'application_key=' . \Config::get('services.odnoklassniki.client_public') . 'fields=name,pic50x50format=jsonmethod=users.getCurrentUser' . '01f393323dff5c95f28a63109a129d3d';

        $sig = md5($params);

        $url = 'application_key=' . \Config::get('services.odnoklassniki.client_public') . '&fields=NAME%2Cpic50x50&format=json&method=users.getCurrentUser&sig=' . $sig . '&access_token=' . '-s-7lUvcZjd4MWubzI0VkXRa4ibXhXQ.4j1Yo0O0zo7eLR-b.l84oXw8yj4Xl.q22H0akWs7xL79KSr21-97qQS.2mc';

        $response = $this->client->request('POST', $url, [
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
        $res = json_decode((string)$response->getBody()->getContents());
        return $resArray = [
            'name' => $res->name,
            'avatar' => $res->pic50x50
        ];
    }

}
