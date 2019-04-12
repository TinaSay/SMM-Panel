<?php

namespace App\Modules\Bosslike\Controllers;

use App\Helpers\GuzzleClient;
use GuzzleHttp\Client;
use App\Modules\Bosslike\Models\Service;
use App\Modules\Bosslike\Models\Social;
use App\Modules\Bosslike\Models\SocialUser;
use App\Modules\Bosslike\Models\Task;
use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Models\TaskComments;
use App\Modules\Bosslike\Requests\TaskSaveRequest;
use App\Modules\Bosslike\Services\BosslikeService;
Use Exception;
use Illuminate\Support\Facades\Input;
use App\Modules\Bosslike\Models\Transactions;
use Bosslike;

/**
 * Class NewTaskController
 * @package App\Modules\Bosslike\Controllers
 */
class NewTaskController extends Controller
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

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('bosslike::tasks.create', [
            'socials' => Social::where('name', '!=', 'Facebook')
                ->where('name', '!=', 'Twitter')->get()->sortBy('name'),
            'intro' => $intro = \DB::table('intros')->where('id', 2)->first()
        ]);
    }

    /**
     * @param $social_id
     * @return bool
     */
    public function checkUserSocial($social_id)
    {
        $user = \Auth::user();
        $socialUser = SocialUser::where('social_id', $social_id)
            ->where('user_id', $user->id)->first();

        if ($socialUser) {
            return true;
        } else {
            $social = Social::find($social_id);
            toast()->warning('Подключите аккаунт ' . $social->name . ', чтобы продолжить.', 'Нет аккаунта ' . $social->name);
            return false;
        }
    }

    /**
     * @param TaskSaveRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Facebook\Exceptions\FacebookSDKException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function store(TaskSaveRequest $request)
    {
        $data = $request->only('link', 'service_id', 'social');

        $checkUserSocial = $this->checkUserSocial($data['social']);

        if (!$checkUserSocial) {
            return redirect('/profile');
        }

        $validator = $this->validatePost($data);
        if ($validator['status']) {
//            dd($validator);
            $task = new Task();
            $task->user_id = \Auth::id();
            $task->service_id = $request->input('service_id');
            $task->link = (isset($validator['newLink'])) ? $validator['newLink'] : $request->input('link');
            $task->picture = $validator['picture'];
            $task->type = $validator['type'];
            $task->post_id = $validator['postId'];
            $task->post_name = (!empty($validator['postName'])) ? $validator['postName'] : '';

            $task->points = $request->input('points');
            $task->amount = $request->input('amount');

            $cost = ($task->points * 2) * $task->amount;

            $affordable = $this->guzzle->getUserBalance() / 100 - $cost;
            if ($affordable < 0) {
                toast()->error('У вас не хватает средств!');
                return back()->with(['socials' => Social::all()])->withInput(Input::all());
            } else {
                $task->save();

                $this->guzzle->chargeClient($cost);

                $trans_action = 'Создание задания';
                $trans_desc = 'Создание задания "' . BosslikeService::setServiceName($task->service->name);
                if ($task->service->name == 'Subscribe') {
                    $trans_desc .= BosslikeService::setTypeName($task->type);
                } else {
                    $trans_desc .= BosslikeService::setTypeName($task->type);
                }

                $trans_desc .= $task->post_name . '"';

                $trans = new Transactions;
                $trans->create(\Auth::id(), $task->id, Task::MONEY_OUT, $trans_action, $task->points, $trans_desc);

                if (filled($request->input('comment_text'))) {
                    $commentsArray = $request->input('comment_text');

                    foreach ($commentsArray as $comment) {

                        $taskComment = new TaskComments();
                        $taskComment->task_id = $task->id;
                        $taskComment->text = $comment;
                        $taskComment->save();
                    }
                }

            }

            if ($request->priority != 'uzb') {
                $result = $this->toBosslike($task, $request->all());
                if (!$result) {
                    toast()->error('error', 'Выбранная вами социальная сеть не обслуживается в международном формате');
                    return back()->with(['socials' => Social::all()])->withInput(Input::all());
                }
            }

            toast()->success('Задание создано.', 'Успех');
            return redirect('/tasks/my');
        } else {
            toast()->error($validator['message'], 'Неудача');
            return back()->with(['socials' => Social::all()])->withInput(Input::all());
        }

    }

    /**
     * @param $task
     * @param $request
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function toBosslike($task, $request)
    {

        switch ($task->service->social->name) {
            case 'Instagram':
                $social_id = 3;
                break;
            case 'Facebook':
                $social_id = 2;
                break;
            case 'Youtube':
                $social_id = 4;
                break;
            default:
                return false;
                break;
        }

        switch ($task->service->name) {
            case 'Like':
                $service_id = 1;
                break;
            case 'Subscribe':
                $service_id = 3;
                break;
            case 'Comment':
                $service_id = 4;
                break;
            case 'Share':
                $service_id = 2;
                break;
            case 'Retweet':
                $service_id = 5;
                break;
            default:
                return false;
                break;
        }

        if ($request['priority'] == 'sng') {
            $points = $request['points'];
            $amount = $request['amount'];
        } elseif ($request['priority'] == 'uzbsng') {
            $points = $request['sng_points'];
            $amount = $request['sng_amount'];
            $task->sng_points = $points;
            $task->sng_amounts = $amount;
        }
        $task->priority = $request['priority'];

        $comments = [];

        if ($service_id == 4) {
            foreach ($request->input('comment_text') as $comment) {
                $comments[] = $comment;
            }
        }

        $client = new Client();
        $url = 'https://api-public.bosslike.ru/v1/';
        $action = 'tasks/create';
        $data = $client->request('POST', $url . $action,
            [
                'headers' => [
                    'X-Api-Key' => env('BOSSLIKE_PUBLIC'),
                    'Accept' => 'application/json',
                ],
                'form_params' => [
                    'service_type' => $social_id,
                    'task_type' => $service_id,
                    'service_url' => $task->link,
                    'price' => $points,
                    'count' => $amount,
                    'comments' => $comments
                ],
                'http_errors' => false
            ]);
        $result = json_decode($data->getBody()->getContents());
//        dd($result);

        $task->bosslike_id = $result->data->task->id;
        $task->save();

        return true;

    }

    /**
     * @param $socialId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServicesAjax($socialId)
    {
        $services = Service::where('social_id', '=', $socialId)->get();
        return response()->json($services);
    }

    /**
     * @param $data
     * @return array
     * @throws \Facebook\Exceptions\FacebookSDKException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validatePost($data)
    {
        $service = Service::find($data['service_id']);
        $social = Social::find($data['social']);
        $socialUsers = SocialUser::where('user_id', \Auth::id())->where('social_id', $data['social'])->first();

        switch ($social->name) {
            case 'Instagram':
                $result = $this->validateInstagram($data, $service->name);
                break;
            case 'Facebook':
                $result = $this->validateFacebook($data, $service->name, $socialUsers->access_token, $socialUsers->client_id, $socialUsers->avatar);
                break;
            case 'Telegram':
                $result = $this->validateChannel($data);
                break;
            case 'Youtube':
                $result = $this->validateYoutube($data, $service->name, $socialUsers);
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * @param $data
     * @param $service
     * @param $token
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateYoutube($data, $service, $token)
    {
        $true = false;
        $type = 'video';
        $arr = explode('https://www.youtube.com/', $data['link']);
        if (count($arr) < 2) {
            return ['status' => false, 'message' => 'Неверная ссылка или не удалось получить данные из социальной сети или ссылка доступна только вам.'];
        }
        switch ($service) {
            case 'Subscribe':
                $arr = explode('/channel/', $data['link']);
                if (count($arr) > 1) {
                    $client = new Client();
                    $url = 'https://www.googleapis.com/youtube/v3/channels';
                    $channel_admin = $client->request('GET', $url,
                        [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $token->access_token,
                                'Content-Type' => 'application/json'
                            ],
                            'query' => [
                                'part' => 'snippet',
                                'id' => $arr[1],
                            ],
                            'http_errors' => false
                        ]);
                    $result = json_decode($channel_admin->getBody()->getContents());
                    if (isset($result->error)) {
                        if ($result->error->code == 401) {
                            return ['status' => false, 'message' => 'Cрок привязки истек. Попробуйте перепривязать ваш аккаунт в настройках.'];
                        }
                    }
                    foreach ($result->items as $item) {
                        $img_url = $item->snippet->thumbnails->default->url;
                    }
                    $true = true;
                    $type = 'channel';
                }
                break;
            case 'Like':
            case 'Comment':
                $arr = explode('/watch?v=', $data['link']);
                if (count($arr) > 1) {
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
                                'id' => $arr[1],
                            ],
                            'http_errors' => false
                        ]);
                    $result = json_decode($channel_admin->getBody()->getContents());
                    if (isset($result->error)) {
                        if ($result->error->code == 401) {
                            return ['status' => false, 'message' => 'Cрок привязки истек. Попробуйте перепривязать ваш аккаунт в настройках.'];
                        }
                    }
                    foreach ($result->items as $item) {
                        $img_url = $item->snippet->thumbnails->default->url;
                    }
                    $true = true;

                }
                break;
            case 'Watch':
                $arr = explode('/watch?v=', $data['link']);
                if (count($arr) > 1) {
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
                                'id' => $arr[1],
                            ],
                            'http_errors' => false
                        ]);
                    $result = json_decode($channel_admin->getBody()->getContents());
                    if (isset($result->error)) {
                        if ($result->error->code == 401) {
                            return ['status' => false, 'message' => 'Cрок привязки истек. Попробуйте перепривязать ваш аккаунт в настройках.'];
                        }
                    }
                    foreach ($result->items as $item) {
                        $img_url = $item->snippet->thumbnails->default->url;
                    }
                    $true = true;
                }
                break;
            default:
                break;
        }
        if (!$true) {
            return ['status' => false, 'message' => 'Неверная ссылка или не удалось получить данные из социальной сети или ссылка доступна только вам.'];
        } else {
            return ['status' => true, 'picture' => $img_url, 'type' => $type, 'postId' => $arr[1]];
        }
    }


    public function validateInstagram($data, $service)
    {
        $instagram = new \InstagramScraper\Instagram();
        try {
            $link = explode("?", $data['link'], 2);
            $link = $link[0];
            switch ($service) {
                case 'Subscribe':
                    $username = str_replace('/', '', str_replace('https://www.instagram.com/', '', $link));
                    $media = $instagram->getAccount($username);
                    $type = 'page';
                    break;
                case 'Like':
                case 'Comment':
                    $media = $instagram->getMediaByUrl($link);
                    $type = 'post';
                    break;
                default:
                    break;
            }
        } catch (Exception $e) {
            $media = $e->getCode();
        }

        if ($media['id'] > 0) {
            (isset($media['imageThumbnailUrl'])) ? $pic = $media['imageThumbnailUrl'] : $pic = $media['profilePicUrl'];
            (isset($media['username'])) ? $postName = $media['username'] : $postName = '';
            return ['status' => true, 'picture' => $pic, 'type' => $type, 'postId' => $media['id'], 'postName' => $postName];
        } else {
            return ['status' => false, 'message' => 'Неверная ссылка или не удалось получить данные из социальной сети или ссылка доступна только Вам.'];
        }
    }

    /**
     * @param $data
     * @param $service
     * @param $token
     * @param $client_id
     * @param $avatar
     * @return array
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function validateFacebook($data, $service, $token, $client_id, $avatar)
    {
        $config = \Config::get('services.facebook');

        $fb = new \Facebook\Facebook([
            'app_id' => $config['client_id'],
            'app_secret' => $config['client_secret'],
            'default_graph_version' => 'v3.2',
        ]);
        switch ($service) {
            case 'Subscribe':
                try {
                    $url = explode("?", $data['link'], 2);
                    $response = $fb->get('/' . $url[0] . '?fields=picture,name', $config['client_id'] . '|' . $config['client_secret']);
                } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                    return ['status' => false, 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()];
                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    return ['status' => false, 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()];
                }
                $page = json_decode($response->getBody());
                if (isset($page->name) && !empty($page->id)) {
                    return ['status' => true, 'picture' => $page->picture->data->url, 'type' => 'page', 'postId' => $page->id];
                }
                break;
            case 'Like':
            case 'Share':
            case 'Comment':
                if ((strpos($data['link'], 'photo') !== false) && strpos($data['link'], 'fbid') !== false) {
                    $pos = explode("?", $data['link'], 2);
                    $pos = explode("&", $pos[1], 2);
                    $postId = substr($pos[0], strpos($pos[0], 'fbid=') + 5);
                    $requestUrl = '/' . $client_id . '_' . $postId . '?fields=picture,type';
                } elseif (strpos($data['link'], 'videos') !== false) {
                    $postId = str_replace('/', '', substr($data['link'], strpos($data['link'], 'videos/') + 7));
                    $requestUrl = '/' . $postId . '?fields=picture';
                } elseif (strpos($data['link'], 'posts') !== false) {
                    $postId = str_replace('/', '', substr($data['link'], strpos($data['link'], 'posts/') + 6));
                    $requestUrl = '/' . $client_id . '_' . $postId;
                } elseif (strpos($data['link'], 'photos') !== false) {
                    $postType = 'photo';
                    $part = substr($data['link'], strpos($data['link'], 'photos/'));
                    $pos = explode("/", $part, 4);
                    $postId = $pos[2];
                    $requestUrl = '/' . $postId . '?fields=picture';
                }

                $newLink = str_replace('www', 'm', $data['link']);

                try {
                    $response = $fb->get($requestUrl, $config['client_id'] . '|' . $config['client_secret']);
                } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                    return ['status' => false, 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()];
                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    return ['status' => false, 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()];
                }
                $post = json_decode($response->getBody());
                (isset($post->type)) ? $postType = $post->type : ($postType == '') ? $postType = 'video' : $postType = 'photo';
                (isset($post->picture)) ? $pic = $post->picture : $pic = $avatar;

                if (isset($post->id)) {
                    return ['status' => true, 'picture' => $pic, 'type' => $postType, 'postId' => $postId, 'newLink' => $newLink];
                }
                break;
            default:
                break;
        }

        return ['status' => false, 'message' => 'Неверная ссылка или не удалось получить данные из социальной сети или ссылка доступна только вам.'];
    }

    /**
     * @param $data
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateChannel($data)
    {
        $path = explode('t.me/', $data['link']);
        $client = new Client();
        $url = 'https://api.telegram.org/bot';
        $channel_admin = $client->request('POST', $url . env('TG_TOKEN') . '/getChatAdministrators',
            [
                'form_params' => [
                    'chat_id' => '@' . $path[1],
                ],
                'http_errors' => false
            ]);
        $result = json_decode($channel_admin->getBody()->getContents());
        if ($result->ok == true) {
            $return = false;
            foreach ($result->result as $admin) {
//                dd($admin->status.$admin->user->username);
                if ($admin->status == 'administrator' && $admin->user->username == 'PicStarBot') {
                    $return = true;
                }
            }

            if (!$return) {
                return ['status' => false, 'message' => 'В указанном канале не добавлен наш бот или не даны администраторские права.'];
            }

            $channel = $client->request('POST', $url . env('TG_TOKEN') . '/getChat',
                [
                    'form_params' => [
                        'chat_id' => '@' . $path[1],
                    ],
                    'http_errors' => false
                ]);
            $result = json_decode($channel->getBody()->getContents());

            $chat_id = $result->result->id;
            $username = $result->result->title;
            if (!isset($result->result->photo)) {
                return ['postName' => $username, 'type' => 'channel', 'postId' => $chat_id, 'status' => true, 'picture' => ''];
            }
            $channel_photo = $client->request('POST', $url . env('TG_TOKEN') . '/getFile',
                [
                    'form_params' => [
                        'file_id' => $result->result->photo->big_file_id,
                    ],
                    'http_errors' => false
                ]);
            $result = json_decode($channel_photo->getBody()->getContents());
            $img_url = 'https://api.telegram.org/file/bot' . env('TG_TOKEN') . '/' . $result->result->file_path;
            return ['postName' => $username, 'type' => 'channel', 'postId' => $chat_id, 'status' => true, 'picture' => $img_url];
        } else {
            return ['status' => false, 'message' => 'В указанном канале не добавлен наш бот или не даны администраторские права.'];
        }
    }
}
