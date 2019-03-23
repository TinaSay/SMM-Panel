<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Modules\Bosslike\Models\SocialUser;
use Laravel\Socialite\Facades\Socialite;
use App\Modules\Bosslike\Models\Social;
use App\Providers\InstagramServiceProvider;

class SocialAuthController extends Controller
{

    public function redirectToProvider($provider)
    {
        if($provider == 'facebook') {
            return Socialite::driver($provider)->scopes(['user_likes, user_posts, manage_pages, public_profile'])->redirect();
        } else {
            return Socialite::driver($provider)->redirect();
        }
    }

    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();

        $this->connectUser($user, $provider);

        return redirect('profile')->with(['success' => 'Соединение с ' . ucfirst($provider) . ' аккаунтом успешно выполнено.']);
    }

    public function connectUser($user, $provider)
    {
        $social = Social::where('name', ucfirst($provider))->first();
        if(!empty($user->nickname)) {
            $username = $user->nickname;
        } else {
            $username = $user->name;
        }
        $localUser = new SocialUser();
        $localUser->social_id = $social->id;
        $localUser->client_id = $user->id;
        $localUser->client_name = $username;
        $localUser->avatar = $user->avatar;
        $localUser->user_id = \Auth::id();
        $localUser->access_token = $user->token;
        $localUser->save();
    }
}