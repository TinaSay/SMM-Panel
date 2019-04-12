<?php

namespace App\Modules\Bosslike\Controllers;

use App\Helpers\GuzzleClient;
use App\Modules\Bosslike\Models\Social;
use App\Modules\Bosslike\Models\SocialUser;
use App\Modules\Bosslike\Models\Task;
use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Models\TaskComments;
use App\Modules\Bosslike\Services\BosslikeService;
use App\Modules\Instashop\Models\Instashop;
use App\Modules\SmmPro\Models\Service;
use App\User;
use Hybridauth\HttpClient\Guzzle;
use InstagramScraper\Instagram;
use Config;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Modules\Bosslike\Models\TaskDone;
use App\Modules\Bosslike\Models\Transactions;
use Bosslike;


/**
 * Class NewTaskController
 * @package App\Modules\Bosslike\Controllers
 */
class TasksController extends Controller
{
    /**
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * NewTaskController constructor.
     * @param GuzzleClient $client
     */
    public function __construct(GuzzleClient $client)
    {
        $this->guzzle = $client;
    }

    public function index(Request $request, Task $task, $social = null, $service = null)
    {
        $task = $task->newQuery();
        $all = true;
        $task->whereHas('tasks_done', function ($q) {
            $q->where('tasks_done.user_id', \Auth::id());
        }, '=', 0)->where('user_id', '!=', \Auth::id())
//            ->whereNull('priority')->orWhere('priority', 'uzbsng')
//                ->where('priority', '!=', 'sng')->orWhereNull('priority')
            ->where(function($query) {
                $query->where('priority', '!=', 'sng')->orWhereNull('priority');
            })
            ->where('done', 0)
            ->orderBy('points', 'DESC')
            ->orderBy('created_at', 'DESC');

        if (!empty($social)) {
            $task->whereHas('service', function ($q) use ($social) {
                $q->where('social_id', $social);
            });
        }

        if (!empty($service)) {
            $all = false;
            $task->where('service_id', $service);
        }

//        $task->where('priority', '!=', 'sng');

        $tasks = $task->take(30)->get();
        $socials = Social::with('services')->where('name', '!=', 'Facebook')
            ->where('name', '!=', 'Twitter')->get();

        return view('bosslike::tasks.all', [
            'tasks' => $tasks,
            'socials' => $socials,
            'selected_social' => $social,
            'selected_service' => $service,
            'all' => $all
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
            toast()->error('Подключите аккаунт ' . $task->service->social->name . ', чтобы продолжить.', 'Нет аккаунта ' . $task->service->social->name);
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
        $watch = $request->input('watch');
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
            case 'Youtube':
                $resp = $this->youtube($task, $socialUser, $comment, $watch);
                break;
            default:
                break;
        }

        if ($resp->original['status'] == 'success') {
            $this->successTask($id, $task->points, $task->service->name, $task->type, $task->post_name);
        }

        return response()->json($resp);
    }

    /**
     * @param $id
     * @param $points
     * @param $service
     * @param $type
     * @param $post_name
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function successTask($id, $points, $service, $type, $post_name)
    {
        $user = \Auth::user();
        $done = new TaskDone;
        $done->user_id = $user->id;
        $done->task_id = $id;
        $done->status = TaskDone::DONE_TASK;
        $done->save();

        $this->ifAllDone($id);

        $this->guzzle->depositClient($points);
        $this->guzzle->getUserBalance();

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

    /**
     * @param $taskId
     */
    public function ifAllDone($taskId)
    {
        $tasksDoneCounter = TaskDone::where('task_id', '=', $taskId)
            ->where('status', '=', TaskDone::DONE_TASK)
            ->count();

        $currentTask = Task::findOrFail($taskId);
        if ($currentTask->amount == $tasksDoneCounter) {
            $currentTask->done = 1;
            $currentTask->save();
        }
    }


    public function youtube($task, $token, $randComment = null, $watch)
    {
        switch ($task->service->name) {
            case 'Comment':
                $path = explode('?v=', $task->link);
                $client = new Client();
                $url = 'https://www.googleapis.com/youtube/v3/commentThreads';
                $channel_admin = $client->request('GET', $url,
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token->access_token,
                            'Content-Type' => 'application/json'
                        ],
                        'query' => [
                            'part' => 'snippet',
                            'videoId' => $path[1],
                            'maxResults' => '50',
                            'moderationStatus' => 'published'
                        ],
                        'http_errors' => false
                    ]);
                $result = json_decode($channel_admin->getBody()->getContents());
                if (isset($result->error)) {
                    if ($result->error->code == 401) {
                        return response()->json(['status' => 'error', 'title' => 'Cрок привязки истек.', 'message' => 'Попробуйте перепривязать ваш аккаунт в настройках.']);
                    }
                }
                $true = false;
                foreach ($result->items as $item) {
                    $author_id = $item->snippet->topLevelComment->snippet->authorChannelId->value;
                    if ($author_id == $token->client_id) {
                        $comment_text = $item->snippet->topLevelComment->snippet->textOriginal;
                        if ($randComment != null) {
                            if ($comment_text == $randComment) {
                                $true = true;
                                return response()->json(['status' => 'success', 'message' => 'Задание выполнено']);
                            }
                        }
                    }
                }
                if ($true) {
                    return response()->json(['status' => 'success', 'message' => 'Задание выполнено']);
                } else {
                    return response()->json(['status' => 'error', 'title' => 'Нажмите проверить.', 'message' => 'Выполнение не подтверждено, проверьте ещё раз.']);
                }
                break;
            case 'Subscribe':
                $path = explode('/channel/', $task->link);
                $client = new Client();
                $url = 'https://www.googleapis.com/youtube/v3/subscriptions';
                $channel_admin = $client->request('GET', $url,
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token->access_token,
                            'Content-Type' => 'application/json'
                        ],
                        'query' => [
                            'part' => 'snippet',
                            'channelId' => $token->client_id,
                            'forChannelId' => $path[1],
                        ],
                        'http_errors' => false
                    ]);
                $result = json_decode($channel_admin->getBody()->getContents());
                if (isset($result->error)) {
                    if ($result->error->code == 401) {
                        return response()->json(['status' => 'error', 'title' => 'Cрок привязки истек.', 'message' => 'Попробуйте перепривязать ваш аккаунт в настройках.']);
                    }
                }
                if (count($result->items) > 0) {
                    if ($result->items[0]->snippet->channelId == $token->client_id) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено']);
                    } else {
                        return response()->json(['status' => 'error', 'title' => 'Нажмите проверить.', 'message' => 'Выполнение не подтверждено, проверьте ещё раз.']);
                    }
                } else {
                    return response()->json(['status' => 'error', 'title' => 'Нажмите проверить.', 'message' => 'Выполнение не подтверждено, проверьте ещё раз.']);
                }
                break;
            case 'Like':
                $path = explode('?v=', $task->link);
                $client = new Client();
                $url = 'https://www.googleapis.com/youtube/v3/videos';
                $channel_admin = $client->request('GET', $url,
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token->access_token,
                            'Content-Type' => 'application/json'
                        ],
                        'query' => [
                            'part' => 'snippet',
                            'myRating' => 'like',
                            'maxResults' => '50',
                        ],
                        'http_errors' => false
                    ]);
                $result = json_decode($channel_admin->getBody()->getContents());
                if (isset($result->error)) {
                    if ($result->error->code == 401) {
                        return response()->json(['status' => 'error', 'title' => 'Cрок привязки истек.', 'message' => 'Попробуйте перепривязать ваш аккаунт в настройках.']);
                    }
                }
                $true = false;
                foreach ($result->items as $item) {
                    if ($path[1] == $item->id) {
                        $true = true;
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено']);
                    }
                }
                if ($true) {
                    return response()->json(['status' => 'success', 'message' => 'Задание выполнено']);
                } else {
                    return response()->json(['status' => 'error', 'title' => 'Нажмите проверить.', 'message' => 'Выполнение не подтверждено, проверьте ещё раз.']);
                }
                break;
            case 'Watch':
                if ($watch == 'true') {
                    return response()->json(['status' => 'success', 'message' => 'Задание выполнено']);
                } else {
                    return response()->json(['status' => 'error', 'title' => 'Нажмите проверить.', 'message' => 'Выполнение не подтверждено, проверьте ещё раз.']);
                }
                break;
        }
    }

    public function instagram($client_name, $client_id, $post, $service, $check, $randComment = null)
    {
        if ($check == "false") {
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
                $code = str_replace('/', '', str_replace('https://www.instagram.com/p/', '', $post));
//            try {
                $instagram = Instagram::withCredentials('walle_017', 'akiakiaki17', __DIR__ . '/cache');
//                $instagram = Instagram::withCredentials(Task::INSTAGRAM_USERNAME, Task::INSTAGRAM_PASSWORD,  __DIR__ . '/cache');
                Instagram::setProxy([
                    'address' => '128.199.168.132',
                    'port'    => '31330',
                    'tunnel'  => true,
                    'timeout' => 30,
                ]);
                $instagram->login();
//                $ip = "fe80::9def:4e76:6600:" . rand( 500, 9999 );
//                Instagram::curlOpts( [ CURLOPT_INTERFACE => $ip, CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V6 ] );
//            dd($_SERVER['HTTP_X_REAL_IP']);
//            $instagram = new Instagram();
//            dd(2);
//                $ip = "xa02:c207:2x16:1262::" . rand( 500, 9999 ); // my block and random entry between 500, and 9999
//                Instagram::curlOpts( [ CURLOPT_INTERFACE => $ip, CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V6 ] );
//                $instagram = Instagram::withCredentials('picstar.uz', 'secretsecret1234', __DIR__ . '/cache');
//                $instagram->setProxy("5.63.**.**", 9999, "****", "****");
//                $instagram->login();

                sleep(2);
                $likes = $instagram->getMediaLikesByCode($code, $quantity);

//            } catch (\Exception $e) {
//                return response()->json([
//                    'status' => $status,
//                    'title' => $title,
//                    'message' => 'Сервис временно недоступен.'
//                ]);
//            }

                foreach ($likes as $like) {
                    if ($like->getUsername() == $client_name) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'username' => $client_name]);
                    }
                }

                break;
            case 'Subscribe':
                $code = str_replace('/', '', str_replace('https://www.instagram.com/', '', $post));
                try {
                    $instagram = Instagram::withCredentials(Task::INSTAGRAM_USERNAME, Task::INSTAGRAM_PASSWORD, __DIR__ . '/cache');
                    $instagram->login();

                    sleep(2);

                    $followers = $instagram->getFollowing($client_id, $quantity, 30, true);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => $status,
                        'title' => $title,
                        'message' => 'Сервис временно недоступен.'
                    ]);
                }
                    foreach ($followers as $follower) {
                        if ($follower['username'] == $code) {
                            return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'username' => $code]);
                        }
                    }

                break;
            case 'Comment':
                $code = str_replace('/', '', str_replace('https://www.instagram.com/p/', '', $post));

                try {
                    $instagram = new Instagram();
                    $comments = $instagram->getMediaCommentsByCode($code, $quantity);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => $status,
                        'title' => $title,
                        'message' => 'Выполнение не подтверждено, проверьте ещё раз.'
                    ]);
                }
                    foreach ($comments as $comment) {
                        if ($comment->getOwner()->getUsername() == $client_name) {
                            if ($randComment != null) {
                                if ($comment['text'] == $randComment) {
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

        if ($check == "false") {
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
//                if((strpos($post, 'photo') !== false) && strpos($post, 'fbid') !== false) {
//                    $requestUrl = '/' . $client_id . '_' . $postId;
//                } elseif (strpos($post, 'videos') !== false) {
//                    $requestUrl = '/' . $postId;
//                } elseif (strpos($post, 'posts') !== false) {
//                    $requestUrl = '/' . $client_id . '_' . $postId;
//                }
                try {
                    $response = $fb->get('/' . $client_id . '_' . $postId . '/likes/', $token);
                } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                }
                $likes = json_decode($response->getBody());
                foreach ($likes->data as $like) {
                    if ($like->id == $client_id) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'post' => $post, 'username' => $client_name]);
                    }
                }
                break;
            case 'Subscribe':
                try {
                    $response = $fb->get('/' . $client_id . '/likes/', $token);
                } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                }
                $follows = json_decode($response->getBody());

                foreach ($follows->data as $follow) {
                    if ($follow->id == $postId) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'post' => $post, 'username' => $client_name]);
                    }
                }
                break;
            case 'Comment':
                try {
                    $response = $fb->get('/' . $client_id . '_' . $postId . '/comments/', $token);
                } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                }
                $comments = json_decode($response->getBody());
                foreach ($comments->data as $comment) {
                    if ($comment->from->id == $client_id) {
                        if ($randComment != null) {
                            if ($comment->message == $randComment) {
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
                } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()]);
                }
                $posts = json_decode($response->getBody());
                foreach ($posts->data as $item) {
                    if ($item->id == $client_id . '_' . $post) {
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
        $channel_admin = $client->request('POST', $url . env('TG_TOKEN') . '/getChatMember',
            [
                'form_params' => [
                    'chat_id' => '@' . $path[1],
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
