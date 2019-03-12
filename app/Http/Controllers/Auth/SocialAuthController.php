<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Models\SocialUser;
use Laravel\Socialite\Facades\Socialite;
use App\Modules\Bosslike\Models\Social;

/**
 * Class SocialAuthController
 * @package App\Http\Controllers\Auth
 */
class SocialAuthController extends Controller
{
    /**
     * @param $provider
     * @return mixed
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {

        $user = Socialite::driver($provider)->user();

        $this->connectUser($user, $provider);

        return redirect('profile')->with(['success' => 'Соединение с Instagram аккаунтом успешно выполнено.']);
    }

    /**
     * @param $user
     * @param $provider
     */
    public function connectUser($user, $provider)
    {
        $social = Social::where('name', ucfirst($provider))->first();

        $localUser = new SocialUser();
        $localUser->social_id = $social->id;
        $localUser->client_id = $user->id;
        $localUser->client_name = $user->name;
        $localUser->user_id = $user->id;
        $localUser->access_token = $user->token;
        $localUser->avatar = $user->avatar;
        $localUser->save();
    }
}
