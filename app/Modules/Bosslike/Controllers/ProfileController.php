<?php

namespace App\Modules\Bosslike\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Models\SocialUser;
use App\Modules\Bosslike\Models\Transactions;
use Laravel\Socialite\Facades\Socialite;
use App\Modules\Bosslike\Models\Social;
use Illuminate\Http\Request;
use Auth;
use Twitter;
use Session;
use Redirect;
use App\Modules\Bosslike\Models\Task;
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Client;

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

        $ok = SocialUser::where('social_id', '=', 1)
            ->where('user_id', '=', \Auth::id())
            ->first();

        $twitter = Social::where('name', 'Twitter')->first();
        $twitter_token = SocialUser::where('social_id', $twitter->id)
            ->where('user_id', '=', \Auth::id())
            ->first();

        $instagram = Social::where('name', 'Instagram')->first();
        $instagram_token = SocialUser::where('social_id', $instagram->id)
            ->where('user_id', '=', \Auth::id())
            ->first();
        $facebook = Social::where('name', 'Facebook')->first();
        $facebook_token = SocialUser::where('social_id', $facebook->id)
            ->where('user_id', '=', \Auth::id())
            ->first();

        $tg = Social::where('name', 'Telegram')->first();
        $tg_token = SocialUser::where('social_id', $tg->id)
            ->where('user_id', '=', \Auth::id())
            ->first();

        $youtube = Social::where('name', 'Youtube')->first();
        $youtube_token = SocialUser::where('social_id', $youtube->id)
            ->where('user_id', '=', \Auth::id())
            ->first();

        return view('bosslike::profile', [
            'ok' => $ok,
            'twitter' => $twitter_token,
            'instagram' => $instagram_token,
            'facebook' => $facebook_token,
            'tg' => $tg_token,
            'youtube' => $youtube_token
        ]);
    }

    public function telegram(Request $request)
    {
//        dd($request->all());
        $user = Auth::user();
        $social = Social::where('name', 'Telegram')->first();
        $token = SocialUser::where('user_id', $user->id)->where('social_id', $social->id)->first();
        if ($token) {
            $token->client_id = $request->id;
            $token->access_token = $request->hash;
            $token->client_name = $request->username;
            $token->social_id = $social->id;
            $token->save();
        } else {
            $token = new SocialUser;
            $token->client_id = $request->id;
            $token->access_token = $request->hash;
            $token->client_name = $request->username;
            $token->social_id = $social->id;
            $user->socialUsers()->save($token);
        }

        return redirect()->route('home');
    }

    public function youtube_login(){
        return Socialite::with('youtube')->redirect();
    }
    public function youtube_callback(){
        $user = Auth::user();
        $soc_user = Socialite::driver('youtube')->user();
        $social = Social::where('name', 'Youtube')->first();
        $token = SocialUser::where('user_id', $user->id)->where('social_id', $social->id)->first();
        if ($token) {
            $token->client_id = $soc_user->id;
            $token->access_token = $soc_user->token;
            $token->client_name = $soc_user->nickname;
            $token->social_id = $social->id;
            $token->save();
        } else {
            $token = new SocialUser;
            $token->client_id = $soc_user->id;
            $token->access_token = $soc_user->token;
            $token->client_name = $soc_user->nickname;
            $token->social_id = $social->id;
            $user->socialUsers()->save($token);
        }
        if($soc_user->avatar != 'null' && strlen($user->avatar) < 5){
            $user->avatar = $soc_user->avatar;
            $user->save();
        }
        return redirect()->route('home');
    }

    public function twitter_login(){
        $sign_in_twitter = true;
        $force_login = false;

        // Make sure we make this request w/o tokens, overwrite the default values in case of login.
        Twitter::reconfig(['token' => '', 'secret' => '']);
        $token = Twitter::getRequestToken(route('twitter.callback'));

        if (isset($token['oauth_token_secret']))
        {
            $url = Twitter::getAuthorizeURL($token, $sign_in_twitter, $force_login);

            Session::put('oauth_state', 'start');
            Session::put('oauth_request_token', $token['oauth_token']);
            Session::put('oauth_request_token_secret', $token['oauth_token_secret']);

            return Redirect::to($url);
        }

        return Redirect::route('twitter.error');
    }
    public function twitter_callback(){
        if (Session::has('oauth_request_token'))
        {
            $request_token = [
                'token'  => Session::get('oauth_request_token'),
                'secret' => Session::get('oauth_request_token_secret'),
            ];

            Twitter::reconfig($request_token);

            $oauth_verifier = false;

            if (Input::has('oauth_verifier'))
            {
                $oauth_verifier = Input::get('oauth_verifier');
                // getAccessToken() will reset the token for you
                $tokenn = Twitter::getAccessToken($oauth_verifier);
            }

            if (!isset($tokenn['oauth_token_secret']))
            {
                return Redirect::route('twitter.error')->with('flash_error', 'We could not log you in on Twitter.');
            }

            $credentials = Twitter::getCredentials();

            if (is_object($credentials) && !isset($credentials->error))
            {

                Session::put('access_token', $tokenn);
                $cred = Twitter::getCredentials();

                $user = Auth::user();
                $social = Social::where('name', 'Telegram')->first();
                $token = SocialUser::where('user_id', $user->id)->where('social_id', $social->id)->first();
                if ($token) {
                    $token->client_id = $tokenn['user_id'];
                    $token->access_token = $tokenn['oauth_token_secret'];
                    $token->client_name = $tokenn['screen_name'];
                    $token->social_id = $social->id;
                    $token->avatar = $cred->profile_image_url;
                } else {
                    $token = new SocialUser;
                    $token->client_id = $tokenn['user_id'];
                    $token->access_token = $tokenn['oauth_token_secret'];
                    $token->client_name = $tokenn['screen_name'];
                    $token->social_id = $social->id;
                    $token->avatar = $cred->profile_image_url;
                }
                $user->socialUsers()->save($token);

                return Redirect::to('/')->with('message', 'Congrats! You\'ve successfully signed in!');
            }

            return Redirect::route('home')->with('error', 'Crab! Something went wrong while signing you up!');
        }
    }

    public function deAuth($id)
    {
        $socUser = SocialUser::where('social_id', $id)->where('user_id', \Auth::id())->delete();
        return redirect()->route('profile');
    }

    public function checkProfile($id)
    {
        $user = \Auth::user();
        $task = Task::find($id);
        $socialUser = SocialUser::where('social_id', $task->service->social->id)
            ->where('user_id', $user->id)->first();

        if($socialUser) {
            return response()->json(['status' => true]);
        } else {
            toast()->warning('Подключите аккаунт ' . $task->service->social->name . ' чтобы продолжить', 'Нет аккаунта' . $task->service->social->name);
            return response()->json(['status' => false, 'social' => $task->service->social->name]);
        }
    }

    public function history()
    {
        $trans = Transactions::where('user_id', Auth::id())->paginate(30);

        return view('bosslike::profile.history', ['data' => $trans]);
    }
}
