<?php
/**
 * Created by PhpStorm.
 * User: Bahti
 * Date: 2/13/2019
 * Time: 4:22 PM
 */

namespace App\Modules\SmmPro\Services;

use App\User;

class SmmPro
{
    /**
     * @param $role
     * @return string
     */
    public function getRole($role)
    {
        if ($role == User::ROLE_ADMIN) {
            return 'admin';
        }
        return 'user';
    }

    /**
     * @param \DateTime $date
     * @return string
     */
    public function formatDate(\DateTime $date)
    {
        return $date->format('d-m-Y');
        //TODO: localize date
    }
}
