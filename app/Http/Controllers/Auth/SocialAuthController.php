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
        } else if($provider == 'instagram') {
            return Socialite::driver($provider)->with(['hl' => 'en'])->redirect();
        }
        else {
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
        switch ($provider) {
            case 'instagram':
                $localUser->user_info_1 = $user->user['counts']['media'];
                $localUser->user_info_2 = $user->user['counts']['follows'];
                $localUser->user_info_3 = $user->user['counts']['followed_by'];
                break;
            case 'facebook':
                $config = \Config::get('services.facebook');

                $fb = new \Facebook\Facebook([
                    'app_id' => $config['client_id'],
                    'app_secret' => $config['client_secret'],
                    'default_graph_version' => 'v3.2',
                ]);
                try {
                    $response = $fb->get('/' . $user->id . '/posts?fields=id&limit=250', $user->token);
                } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                    toast()->error('Что то пошло не так.', 'Попробуйте ещё раз.');
                    return back();
                } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                    toast()->error('Что то пошло не так.', 'Попробуйте ещё раз.');
                    return back();
                }
                $data = json_decode($response->getBody());
                $total = count($data->data);
                if($total > 230) {
                    $total = 250;
                }
                $localUser->user_info_2 = $total;
                try {
                    $response = $fb->get('/' . $user->id . '/likes?fields=id&limit=250', $config['client_id'] . '|' . $config['client_secret']);
                } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                    toast()->error('Что то пошло не так.', 'Попробуйте ещё раз.');
                    return back();
                } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                    toast()->error('Что то пошло не так.', 'Попробуйте ещё раз.');
                    return back();
                }
                $data = json_decode($response->getBody());
                $total = count($data->data);
                if($total > 230) {
                    $total = 250;
                }
                $localUser->user_info_1 = $total;
                try {
                    $response = $fb->get('/' . $user->id . '?fields=picture',$config['client_id'] . '|' . $config['client_secret']);
                } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                    toast()->error('Что то пошло не так.', 'Попробуйте ещё раз.');
                    return back();
                } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                    toast()->error('Что то пошло не так.', 'Попробуйте ещё раз.');
                    return back();
                }
                $data = json_decode($response->getBody());
                $localUser->avatar = $data->picture->data->url;
                break;
            default:
                break;
        }

        $localUser->save();
    }
}