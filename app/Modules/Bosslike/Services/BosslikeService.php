<?php

namespace App\Modules\Bosslike\Services;


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
    public static function setServiceName($serviceName, $category = null)
    {
        if ($serviceName == 'Subscribe') {
            if($category != null) {
                return 'Подписаться';
            }
            return 'Подписаться на ';
        } elseif ($serviceName == 'Like') {
            return 'Лайкнуть';
        } elseif ($serviceName == 'Comment') {
            return 'Комментировать';
        } elseif ($serviceName == 'Watch') {
            return 'Просмотреть';
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
        } elseif ($type == 'channel') {
            return ' канал';
        }
        return $type;

    }

    /**
     * @return mixed
     */
    public static function randomImage()
    {
        $a = glob(public_path('images/avatars') . '/*.{jpeg,gif,png}', GLOB_BRACE);
        $imageNames = [];
        foreach ($a as $path) {
            $imageNames[] = basename($path);
        }
        /*$directory = public_path('images/avatars');
        $images = array_diff(scandir($directory), array('..', '.'));*/

        return $imageNames[array_rand($imageNames)];
    }


}
