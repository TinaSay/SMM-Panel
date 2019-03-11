<?php

namespace App\Modules\Bosslike\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Models\SocialUser;

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
        $localUser = SocialUser::where('social_id', '=', 1)
            ->where('user_id', '=', \Auth::id())
            ->first();

        return view('bosslike::profile', [
            'localUser' => $localUser
        ]);
    }
}
