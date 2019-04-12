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
use DB;
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

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://billing.smm-pro.uz']);
    }

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
        $user = Auth::user();
        $social = Social::where('name', 'Telegram')->first();
        $token = SocialUser::where('user_id', $user->id)->where('social_id', $social->id)->first();
        if ($token) {
            $token->client_id = $request->id;
            $token->access_token = $request->hash;
            $token->client_name = $request->username;
            $token->user_info_1 = $request->first_name;
            $token->user_info_2 = $request->auth_date;
            $token->avatar = $request->photo_url;
            $token->social_id = $social->id;
            $token->save();
        } else {
            $token = new SocialUser;
            $token->client_id = $request->id;
            $token->access_token = $request->hash;
            $token->client_name = $request->username;
            $token->social_id = $social->id;
            $token->user_info_1 = $request->first_name;
            $token->user_info_2 = $request->auth_date;
            $token->avatar = $request->photo_url;
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


            $client = new Client();
            $url = 'https://www.googleapis.com/youtube/v3/channels';
            $channel_admin = $client->request('GET', $url,
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token->access_token,
                        'Content-Type' => 'application/json'
                    ],
                    'query' => [
                        'part' => 'statistics',
                        'mine' => 'true',
                    ],
                    'http_errors' => false
                ]);
            $result = json_decode($channel_admin->getBody()->getContents());

            $token->client_name = $soc_user->nickname;
            $token->social_id = $social->id;
            $token->avatar = $soc_user->avatar;
            $token->user_info_1 = $result->items[0]->statistics->viewCount;
            $token->user_info_2 = $result->items[0]->statistics->subscriberCount;
            $token->user_info_3 = $result->items[0]->statistics->videoCount;
            $token->save();
        } else {
            $token = new SocialUser;
            $token->client_id = $soc_user->id;
            $token->access_token = $soc_user->token;
            $token->avatar = $soc_user->avatar;
            $token->client_name = $soc_user->nickname;
            $token->social_id = $social->id;
            $user->socialUsers()->save($token);

            $client = new Client();
            $url = 'https://www.googleapis.com/youtube/v3/channels';
            $channel_admin = $client->request('GET', $url,
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token->access_token,
                        'Content-Type' => 'application/json'
                    ],
                    'query' => [
                        'part' => 'statistics',
                        'mine' => 'true',
                    ],
                    'http_errors' => false
                ]);
            $result = json_decode($channel_admin->getBody()->getContents());
            $token->user_info_1 = $result->items[0]->statistics->viewCount;
            $token->user_info_2 = $result->items[0]->statistics->subscriberCount;
            $token->user_info_3 = $result->items[0]->statistics->videoCount;
            $token->save();
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

//        $req['service_id'] = 12;
        $user = Auth::user();
        $req['user_id'] = $user->billing_id;
        $response = $this->client->request('POST', '/api/get-service-history', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . session('user_token')
            ],
            'form_params' => $req
        ]);

        $res = json_decode((string)$response->getBody()->getContents());

        if (!$res == null) {
            $trans_action = 'Пополнение баланса';
            foreach ($res as $item) {
                $old_trans = Transactions::where('payment_id', $item->id)->first();
                if(empty($old_trans)) {
                    $trans = new Transactions;
                    $trans->create(\Auth::id(), 0, Task::MONEY_IN, $trans_action, $item->amount / 100, $item->description, $item->created_at, $item->id);
                }
            }
        }

        $actions = DB::table('transactions')
            ->select('action')
            ->distinct()->get();


//        $trans = Transactions::where('user_id', Auth::id())->orderBy('created_at', 'DESC')->paginate(30);
//        , ['data' => $trans]
        return view('bosslike::profile.history', ['actions' => $actions]);
    }

    public function getHistoryData(Request $request, Transactions $transactions)
    {
        $transactions = $transactions->newQuery();
        $transactions->where('user_id', Auth::id());
        if($request->has('start') && $request->has('end')) {
            $transactions->where('created_at', '>=', $request->start)
                ->where('created_at', '<=', $request->end);
        }
        if($request->has('type') && $request->type != '0') {
            $type = $request->type;
            $transactions->where('type', $type);
        }
        if($request->has('action') && $request->action != '0') {
            $action = $request->action;
            $transactions->where('action', $action);
        }

        $count = $transactions->count();
        $result = $transactions->orderBy('created_at', 'ASC')->paginate(30);

        $output = '';
        if($count > 0)
        {
            foreach($result as $row)
            {
                $output .= '
                    <tr>
                        <td>' . \Carbon\Carbon::parse($row->created_at)->format('d.m.Y') . '</td>';
                        if($row->type == 'in') {
                            $output .= '<td><span class="text-success"><i class="fa fa-plus-circle"></i></span></td>';
                        } else {
                            $output .= '<td><span class="text-danger"><i class="fa fa-minus-circle"></i></span></td>';
                        }
                $output .=  '
                        <td>' . $row->action  . '</td>
                        <td>' . $row->description  . '</td>';
                        if($row->type == 'in') {
                            $output .= '<td><span class="text-success">+' . $row->points . '</span></td>';
                        } else {
                            $output .= '<td><span class="text-danger">-' . $row->points . '</span></td>';
                        }
                $output .= '</tr>';
            }
            $data = ['output' => $output, 'pagination' => (string)$result->links()];
        }
        else {
            $output = '<tr class="nothing_found"><td colspan="5">Ничего не найдено!</td></tr>';
            $data = ['output' => $output, 'pagination' => ''];
        }
        return $data;


//        $trans = Transactions::where('user_id', Auth::id())->orderBy('created_at', 'DESC')->paginate(30);

//        return view('bosslike::profile.history', ['data' => $trans]);
    }

    public function socialUpdate($id)
    {
        $social = SocialUser::findOrFail($id);

        if($social) {
            switch ($social->social->name) {
                case 'Instagram':
                    $account = (new \InstagramScraper\Instagram())->getAccountById($social->client_id);
                    $social->client_name = $account->getUsername();
                    $social->avatar = $account->getProfilePicUrl();
                    $social->user_info_2 = $account->getFollowsCount();
                    $social->user_info_3 = $account->getFollowedByCount();
                    $social->user_info_1 = $account->getMediaCount();
                    break;
                case 'Facebook':
                    $config = \Config::get('services.facebook');

                    $fb = new \Facebook\Facebook([
                        'app_id' => $config['client_id'],
                        'app_secret' => $config['client_secret'],
                        'default_graph_version' => 'v3.2',
                    ]);

                    try {
                        $response = $fb->get('/' . $social->client_id . '/posts?fields=id&limit=250', $config['client_id'] . '|' . $config['client_secret']);
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
                    $social->user_info_2 = $total;
                    try {
                        $response = $fb->get('/' . $social->client_id . '/likes?fields=id&limit=250', $config['client_id'] . '|' . $config['client_secret']);
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
                    $social->user_info_1 = $total;
                    try {
//                        ?fields=picture&height=500
                        $response = $fb->get('/' . $social->client_id . '?fields=picture',$config['client_id'] . '|' . $config['client_secret']);
                    } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                        toast()->error('Что то пошло не так.', 'Попробуйте ещё раз.');
                        return back();
                    } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                        toast()->error('Что то пошло не так.', 'Попробуйте ещё раз.');
                        return back();
                    }
                    $data = json_decode($response->getBody());
                    $social->avatar = $data->picture->data->url;
                    break;
                case 'Telegram':
                    $user = Auth::user();
                    $social = Social::where('name', 'Telegram')->first();
                    $token = SocialUser::where('user_id', $user->id)->where('social_id', $social->id)->first();

                    break;
                case 'Youtube':

                    $client = new Client();
                    $url = 'https://www.googleapis.com/youtube/v3/channels';
                    $channel_admin = $client->request('GET', $url,
                        [
                            'headers' => [
                                'Authorization' => 'Bearer '.$social->access_token,
                                'Content-Type' => 'application/json'
                            ],
                            'query' => [
                                'part' => 'statistics',
                                'mine' => 'true',
                            ],
                            'http_errors' => false
                        ]);
                    $result = json_decode($channel_admin->getBody()->getContents());

                    $url = 'https://www.googleapis.com/youtube/v3/channels';
                    $user_data = $client->request('GET', $url,
                        [
                            'headers' => [
                                'Authorization' => 'Bearer '.$social->access_token,
                                'Content-Type' => 'application/json'
                            ],
                            'query' => [
                                'part' => 'snippet',
                                'mine' => 'true',
                            ],
                            'http_errors' => false
                        ]);
                    $result_2 = json_decode($user_data->getBody()->getContents());

                    $social->avatar = $result_2->items[0]->snippet->thumbnails->default->url;
                    $social->user_info_1 = $result->items[0]->statistics->viewCount;
                    $social->user_info_2 = $result->items[0]->statistics->subscriberCount;
                    $social->user_info_3 = $result->items[0]->statistics->videoCount;

                    break;
                default:
                    break;
            }

            if($social->save()) {
                return response()->json(['status' => 'success', 'title' => 'Успех!', 'message' => 'Данные ' . $social->social->name . ' обновлены', 'social' => strtolower($social->social->name)]);
            } else {
                return response()->json(['status' => 'error', 'title' => 'Неудача!', 'message' => 'Что-то пошло не так, попробуйте ещё раз.', 'social' => strtolower($social->social->name)]);
            }
        }
    }
}
