<?php

namespace App\Http\Controllers;

use App\Modules\Bosslike\Models\SocialUser;
use App\Modules\Bosslike\Requests\SocialUserRequest;
use Laravel\Socialite\Facades\Socialite;

/**
 * Class OkController
 * @package App\Http\Controllers
 */
class OkController extends Controller
{
    /**
     * @return mixed
     */
    public function redirectToProvider()
    {
        return Socialite::driver('odnoklassniki')->redirect();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function handleProviderCallback()
    {
        $okUser = Socialite::driver('odnoklassniki')->user();

        $localUser = new SocialUser();
        $localUser->social_id = 1;
        $localUser->user_id = \Auth::id();
        $localUser->access_token = $okUser->token;
        $localUser->save();

        return view('bosslike::profile')
            ->with(['success', 'Аккаунт одноклассников привязан!'])->withLocalUser($localUser);
    }

}
