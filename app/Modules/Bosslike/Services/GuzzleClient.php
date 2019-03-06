<?php
/**
 * Created by PhpStorm.
 * User: Bahti
 * Date: 3/5/2019
 * Time: 3:43 PM
 */

namespace App\Modules\Bosslike\Services;

use Hybridauth\HttpClient\Guzzle;

class GuzzleClient
{
    /*protected $client;

    public function __construct()
    {
        $client = new Guzzle(['base_uri' => 'http://demo.smm-pro.uz']);
        $this->client = $client;
    }*/



    /*public function getUrlInfo($url)
    {
        $data = array(
            'application_key' => 'CBAFLBBNEBABABABA',
            'format' => 'json',
            'method' => 'url.getInfo',
            'url' => $url,
            'access_token' => '-s-1qUve2HaXl0R2zobXmYrZwH5VkXvby-bXl3v2.j44KRy-XlaXoWP62jdYl3s6zg1brSreXHfViUuZahbbjSpdY5',
            'secret_key' => '5358ce80d7e7006c981e786ac1d69f3a'
        );

        $dataString = 'https://api.ok.ru/fb.do?application_key=CBAFLBBNEBABABABA&format=json&method=url.getInfo&url=https%3A%2F%2Fok.ru%2Fgroup53685699674234&sig=ee8374e249e85488f2f32ae25fcda836&access_token=-s-1qUve2HaXl0R2zobXmYrZwH5VkXvby-bXl3v2.j44KRy-XlaXoWP62jdYl3s6zg1brSreXHfViUuZahbbjSpdY5';

        $data = json_encode($data);
        $ch = curl_init('https://smmpanel.ru/api/add_order/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $content = json_decode(curl_exec($ch));
    }*/


}
