<?php

namespace App\Modules\Bosslike\Controllers;

use App\Modules\Bosslike\Models\Service;
use App\Modules\Bosslike\Models\Social;
use App\Modules\Bosslike\Models\SocialUser;
use App\Modules\Bosslike\Models\Task;
use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Models\TaskComments;
use App\Modules\Bosslike\Services\BosslikeService;
use App\User;
use Composer\DependencyResolver\Transaction;
use InstagramScraper\Instagram;
use Config;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Mockery\Exception;
use App\Modules\Bosslike\Models\TaskDone;
use App\Modules\Bosslike\Models\Transactions;


/**
 * Class NewTaskController
 * @package App\Modules\Bosslike\Controllers
 */
class TasksController extends Controller
{

    public function index()
    {
        return view('bosslike::tasks.all', [
            'tasks' => Task::whereHas('tasks_done', function ($q) {
                $q->where('tasks_done.user_id', \Auth::id());
            }, '=', 0)->where('user_id', '!=', \Auth::id())->get()
        ]);
    }

    public function show($id)
    {
        $user = \Auth::user();
        $task = Task::find($id);
        $socialUser = SocialUser::where('social_id', $task->service->social->id)
            ->where('user_id', $user->id)->first();

        if ($socialUser) {
            return view('bosslike::tasks.show', ['link' => $task->link]);
        } else {
            return redirect('/profile');
        }
    }

    public function hide($id)
    {
        $user = \Auth::user();

        $done = new TaskDone;
        $done->user_id = $user->id;
        $done->task_id = $id;
        $done->status = TaskDone::HIDDEN_TASK;
        $done->save();
    }

    public function check($id, Request $request)
    {
        $user = \Auth::user();
        $task = Task::find($id);
        $check = $request->input('check');
        if ($request->has('comment')) {
            $comment = TaskComments::find($request->input('comment'));
            $comment = $comment->text;
        } else {
            $comment = null;
        }
        $socialUser = SocialUser::where('social_id', $task->service->social->id)
            ->where('user_id', $user->id)->first();

        switch ($task->service->social->name) {
            case 'Instagram':
                $resp = $this->instagram($socialUser->client_name, $socialUser->client_id, $task->link, $task->service->name, $check, $comment);
                break;
            case 'Facebook':
                $resp = $this->facebook($socialUser->client_name, $socialUser->client_id, $task->link, $task->post_id, $task->service->name, $socialUser->access_token, $check, $comment);
                break;
            case 'Telegram':
                $resp = $this->telegram($task->link, $socialUser);
                break;
            case 'OK':

                break;
            default:
                break;
        }

        if ($resp->original['status'] == 'success') {
            $this->successTask($id, $task->points, $task->service->name, $task->type, $task->post_name);
        }

        return response()->json($resp);
    }

    public function successTask($id, $points, $service, $type, $post_name)
    {
        $user = \Auth::user();
        $done = new TaskDone;
        $done->user_id = $user->id;
        $done->task_id = $id;
        $done->status = TaskDone::DONE_TASK;
        $done->save();

        $client = new Client([
            'base_uri' => 'https://billing.smm-pro.uz'
        ]);

        $client->request('POST', '/api/deposit', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . session('user_token')
            ],
            'form_params' => [
                'amount' => $points,
                'description' => 'Начисление денег пользователю ' . \Auth::user()->billing_id,
                'client' => \Config::get('services.oauthConfig.keys.id'),
            ]
        ]);
        User::getUserBalance();

        $trans_action = 'Выполнил задание';
        $trans_desc = BosslikeService::setServiceName($service);
        if ($service == 'Subscribe') {
            $trans_desc .= BosslikeService::setTypeName($type);
        } else {
            $trans_desc .= BosslikeService::setTypeName($type);
        }

        $trans_desc .= $post_name;

        $trans = new Transactions;
        $trans->create($user->id, $id, Task::MONEY_IN, $trans_action, $points, $trans_desc);
    }



    public function instagram($client_name, $client_id, $post, $service, $check, $randComment = null)
    {
        if($check == "false") {
            $quantity = 50;
            $title = 'Нажмите проверить.';
            $status = 'warning';
        } else {
            $quantity = 100;
            $title = 'Проверьте соответствие подключенного профиля.';
            $status = 'error';
        }

        switch ($service) {
            case 'Like':
                $code = str_replace('/', '',str_replace('https://www.instagram.com/p/', '', $post));
//                $ip = "fe80::9def:4e76:6600:" . rand( 500, 9999 );
//                Instagram::curlOpts( [ CURLOPT_INTERFACE => $ip, CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V6 ] );
                $instagram = Instagram::withCredentials(Task::INSTAGRAM_USERNAME, Task::INSTAGRAM_PASSWORD, __DIR__ .'/cache');
//                Instagram::setProxy([
//                    'address' => '68.15.42.194',
//                    'port'    => '46682',
//                    'tunnel'  => true,
//                    'timeout' => 30,
//                ]);
                $instagram->login();

                sleep(1);

                $likes = $instagram->getMediaLikesByCode($code, $quantity);

                foreach ($likes as $like) {
                    if($like->getUsername() == $client_name) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'username' => $client_name]);
                    }
                }

                break;
            case 'Subscribe':
                $code = str_replace('/', '',str_replace('https://www.instagram.com/', '', $post));
                $instagram = Instagram::withCredentials(Task::INSTAGRAM_USERNAME, Task::INSTAGRAM_PASSWORD, __DIR__ .'/cache');
                $instagram->login();

                sleep(1);

                $followers = $instagram->getFollowing($client_id, $quantity, 30, true);

                foreach($followers as $follower) {
                    if($follower['username'] == $code) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'username' => $code]);
                    }
                }

                break;
            case 'Comment':
                $code = str_replace('/', '',str_replace('https://www.instagram.com/p/', '', $post));
                $instagram = new Instagram();
                $comments = $instagram->getMediaCommentsByCode($code, $quantity);

                foreach ($comments as $comment) {
                    if($comment->getOwner()->getUsername() == $client_name) {
                        if($randComment != null) {
                            if($comment['text'] == $randComment) {
                                return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'post' => $code, 'username' => $client_name]);
                            }
                        } else {
                            return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'post' => $code, 'username' => $client_name]);
                        }
                    }
                }

                break;
            default:
                break;
        }
        return response()->json([
            'status' => $status,
            'title' => $title,
            'message' => 'Выполнение не подтверждено, проверьте ещё раз.'
        ]);
    }

    public function facebook($client_name, $client_id, $post, $postId, $service, $token, $check, $randComment = null)
    {
        $config = Config::get('services.facebook');

        if($check == "false") {
            $title = 'Нажмите проверить.';
            $status = 'warning';
        } else {
            $title = 'Проверьте соответствие подключенного профиля.';
            $status = 'error';
        }

        $fb = new \Facebook\Facebook([
            'app_id' => $config['client_id'],
            'app_secret' => $config['client_secret'],
            'default_graph_version' => 'v3.2',
        ]);
        switch ($service) {
//        $twoMonthToken = $client->getLongLivedAccessToken($twoHourToken);
            case 'Like':
                try {
                    $response = $fb->get('/' . $client_id . '_' . $postId . '/likes/', $token);
                } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                }
                $likes = json_decode($response->getBody());
                foreach($likes->data as $like) {
                    if($like->id == $client_id) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'post' => $post, 'username' => $client_name]);
                    }
                }
                break;
            case 'Subscribe':
                try {
                    $response = $fb->get('/' . $client_id . '/likes/', $token);
                } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                }
                $follows = json_decode($response->getBody());

                foreach($follows->data as $follow) {
                    if($follow->id == $postId) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'post' => $post, 'username' => $client_name]);
                    }
                }
                break;
            case 'Comment':
                try {
                    $response = $fb->get('/' . $client_id . '_' . $postId . '/comments/', $token);
                } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                }
                $comments = json_decode($response->getBody());
                foreach($comments->data as $comment) {
                    if($comment->from->id == $client_id) {
                        if($randComment != null) {
                            if($comment->message == $randComment) {
                                return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'post' => $post, 'username' => $client_name]);
                            }
                        } else {
                            return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'post' => $post, 'username' => $client_name]);
                        }
                    }
                }
                break;
            case 'Share':
                try {
                    $response = $fb->get('/' . $client_id . '/posts/', $token);
                } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                }
                $posts = json_decode($response->getBody());
                foreach($posts->data as $item) {
                    if($item->id == $client_id . '_' . $post) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'post' => $post, 'username' => $client_name]);
                    }
                }
                break;
            default:
                break;
        }
        return response()->json([
            'status' => $status,
            'title' => $title,
            'message' => 'Выполнение не подтверждено, проверьте ещё раз.'
        ]);
    }

    public function telegram($link, $token)
    {
        $path = explode('t.me/', $link);
        $client = new Client();
        $url = 'https://api.telegram.org/bot';
        $channel_admin = $client->request('POST', $url.env('TG_TOKEN').'/getChatMember',
            [
                'form_params' => [
                    'chat_id' => '@'.$path[1],
                    'user_id' => $token->client_id
                ],
                'http_errors' => false
            ]);
        $result = json_decode($channel_admin->getBody()->getContents());

        if ($result->ok == true && $result->result->status == 'member') {
            return response()->json(['status' => 'success', 'message' => 'Задание выполнено']);
        } else {
            return response()->json(['status' => 'error', 'title' => 'Нажмите проверить.', 'message' => 'Выполнение не подтверждено, проверьте ещё раз.']);
        }
    }
}
