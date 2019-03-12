<?php

namespace App\Modules\Bosslike\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Models\SocialUser;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Auth;
use App\Modules\Bosslike\Models\Social;
use Twitter;
use Session;
use Redirect;
use Illuminate\Support\Facades\Input;

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

        $twitter = Social::where('name', 'Twitter')->first();
        $twitter_token = SocialUser::where('social_id', $twitter->id)
            ->where('user_id', '=', \Auth::id())
            ->first();

        return view('bosslike::profile', [
            'localUser' => $localUser,
            'twitter' => $twitter_token
        ]);
    }

    public function telegram(Request $request)
    {
        $user = Auth::user();
        $social = Social::where('name', 'Telegram')->first();
        $token = SocialUser::where('user_id', $user->id)->where('social_id', $social->id)->first();
        if ($token) {
            $token->client_id = $request->id;
            $token->access_token = $request->hash;
            $token->client_name = $request->username;
            $token->social_id = $social->id;
        } else {
            $token = new SocialUser;
            $token->client_id = $request->id;
            $token->access_token = $request->hash;
            $token->client_name = $request->username;
            $token->social_id = $social->id;
        }
        $user->socialUsers()->save($token);

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
                // $credentials contains the Twitter user object with all the info about the user.
                // Add here your own user logic, store profiles, create new users on your tables...you name it!
                // Typically you'll want to store at least, user id, name and access tokens
                // if you want to be able to call the API on behalf of your users.

                // This is also the moment to log in your users if you're using Laravel's Auth class
                // Auth::login($user) should do the trick.

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
}
