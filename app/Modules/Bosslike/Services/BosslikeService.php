<?php

namespace App\Modules\Bosslike\Services;

use GuzzleHttp;

/**
 * Class BosslikeService
 * @package App\Modules\Bosslike\Services
 */
class BosslikeService
{
    /**
     * @param $serviceName
     * @return string
     */
    public static function setServiceName($serviceName)
    {
        if ($serviceName == 'Subscribe') {
            return 'Подписаться';
        } elseif ($serviceName == 'Like') {
            return 'Лайкнуть';
        } elseif ($serviceName == 'Comment') {
            return 'Комментировать';
        }
        return $serviceName;
    }

    /**
     * @param $type
     * @return string
     */
    public static function setTypeName($type)
    {
        if ($type == 'post') {
            return ' запись на стене';
        } elseif ($type == 'photo') {
            return ' фотографию';
        } elseif ($type == 'video') {
            return ' видео';
        } elseif ($type == 'page') {
            return ' страницу';
        }
        return $type;

    }

    /**
     * @return array
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    public static function getUserInfo()
    {
        $client = new GuzzleHttp\Client([
            'base_uri' => 'https://api.ok.ru/fb.do?'
        ]);

        $params = 'application_key=' . \Config::get('services.odnoklassniki.client_public') . 'fields=name,pic50x50format=jsonmethod=users.getCurrentUser' . '01f393323dff5c95f28a63109a129d3d';

        $sig = md5($params);

        $url = 'application_key=' . \Config::get('services.odnoklassniki.client_public') . '&fields=NAME%2Cpic50x50&format=json&method=users.getCurrentUser&sig=' . $sig . '&access_token=' . '-s-7lUvcZjd4MWubzI0VkXRa4ibXhXQ.4j1Yo0O0zo7eLR-b.l84oXw8yj4Xl.q22H0akWs7xL79KSr21-97qQS.2mc';

        $response = $client->request('POST', $url, [
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
