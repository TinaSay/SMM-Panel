<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Modules\Bosslike\Models\SocialUser;
use Laravel\Socialite\Facades\Socialite;
use App\Modules\Bosslike\Models\Social;

class LoginController extends Controller
{

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {

        $user = Socialite::driver($provider)->user();

        $this->connectUser($user, $provider);

        return redirect('profile')->with(['success' => 'Соединение с Instagram аккаунтом успешно выполнено.']);
    }

    public function connectUser($user, $provider)
    {
        $social = Social::where('name', ucfirst($provider))->first();

        $localUser = new SocialUser();
        $localUser->social_id = $social->id;
        $localUser->client_id = $user->id;
        $localUser->client_name = $user->nickname;
        $localUser->user_id = $user->id;
        $localUser->access_token = $user->token;
        $localUser->save();
    }
}