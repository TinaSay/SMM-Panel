<?php

namespace App\Modules\Bosslike\Controllers;

use App\Modules\Bosslike\Models\Service;
use App\Modules\Bosslike\Models\Social;
use App\Modules\Bosslike\Models\SocialUser;
use App\Modules\Bosslike\Models\Task;
use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Requests\TaskSaveRequest;
//use PHPUnit\Framework\Exception;
Use Exception;
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Client;

/**
 * Class NewTaskController
 * @package App\Modules\Bosslike\Controllers
 */
class NewTaskController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('bosslike::tasks.create', [
            'socials' => Social::all()
        ]);
    }

    /**
     * @param TaskSaveRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(TaskSaveRequest $request)
    {

        $data = $request->only('link', 'service_id', 'social');
        $validator = $this->validatePost($data);
        if($validator['status']) {
            $task = new Task();
            $task->user_id = \Auth::id();
            $task->service_id = $request->input('service_id');
            $task->link = $request->input('link');

            $task->points = $request->input('points');
            $task->amount = $request->input('amount');
            $task->save();

            toast()->success('Задание создано.', 'Успех');
            return redirect('/tasks/my');
        } else {
            toast()->error($validator['message'], 'Неудача');
//            return view('bosslike::tasks.create')->with(['socials' => Social::all()]);
            return back()->with(['socials' => Social::all()])->withInput(Input::all());
        }

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

    // public function validateChannel($data)
    // {
        
    // }

    public function validatePost($data)
    {
        $service = Service::find($data['service_id']);
        $social = Social::find($data['social']);
        $socialUsers = SocialUser::where('user_id', \Auth::id())->where('social_id', $data['social']);
        switch ($social->name) {
            case 'Instagram':
                $result = $this->validateInstagram($data, $service->name);
                break;
            case 'Facebook':
                $result = $this->validateFacebook($data, $service->name, $socialUsers->access_token);
                break;
            case 'Telegram' :
                $result = $this->validateChannel($data);
                break;
            default:
                break;
        }

        return $result;
    }

    public function validateChannel($data)
    {
        $path = explode('t.me/', $data['link']);
        $client = new Client();
        $url = 'https://api.telegram.org/bot';
        $channel_admin = $client->request('POST', $url.env('TG_TOKEN').'/getChatAdministrators', 
            [
                'form_params' => [
                    'chat_id' => '@'.$path[1],
            ],
            'http_errors' => false
        ]);
        $result = json_decode($channel_admin->getBody()->getContents());
        if ($result->ok == true) {
            $return = false;
            foreach ($result->result as $admin) {
                if ($admin->status == 'administrator' && $admin->user->username == env('TG_NAME')) {
                    $return = true;
                }
            }

            if ($return) {
                return ['status' => false, 'message' => 'В указанном канале не добавлен наш бот или не даны администраторские права'];
            }

            $channel = $client->request('POST', $url.env('TG_TOKEN').'/getChat', 
                [
                    'form_params' => [
                        'chat_id' => '@'.$path[1],
                ],
                'http_errors' => false
            ]);
            $result = json_decode($channel->getBody()->getContents());
            if (!isset($result->result->photo)) {
                return ['status' => true, 'picture' => ''];
            }
            $channel_photo = $client->request('POST', $url.env('TG_TOKEN').'/getFile', 
                [
                    'form_params' => [
                        'file_id' => $result->result->photo->big_file_id,
                ],
                'http_errors' => false
            ]);
            $result = json_decode($channel_photo->getBody()->getContents());
            $img_url = 'https://api.telegram.org/file/bot'.env('TG_TOKEN').'/'.$result->result->file_path;
            return ['status' => true, 'picture' => $img_url];
        } else {
            return ['status' => false, 'message' => 'В указанном канале не добавлен наш бот или не даны администраторские права'];
        }
    }

    public function validateInstagram($data, $service)
    {
        $instagram = new \InstagramScraper\Instagram();
        try {
            switch ($service) {
                case 'Subscribe':
                    $username = str_replace('/', '',str_replace('https://www.instagram.com/', '', $data['link']));
                    $media = $instagram->getAccount($username);
                    break;
                case 'Like':
                case 'Comment':
                    $media = $instagram->getMediaByUrl($data['link']);
                    break;
                default:
                    break;
            }
        } catch (Exception $e) {
            $media = $e->getCode();
        }

        if($media['id'] != 0) {
            return ['status' => true];
        } else {
            return ['status' => false, 'message' => 'Неверная ссылка или не удалось получить данные из социальной сети или ссылка доступна только вам.'];
        }
    }

    public function validateFacebook($data, $service, $token)
    {
        $config = \Config::get('services.facebook');

        $fb = new \Facebook\Facebook([
            'app_id' => $config['client_id'],
            'app_secret' => $config['client_secret'],
            'default_graph_version' => 'v3.2',
        ]);

        try {
            switch ($service) {
                case 'Subscribe':
                    try {
                        $response = $fb->get('/' . $data['link'], $token);
                    } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                        echo 'Graph returned an error: ' . $e->getMessage();
                        exit;
                    } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        exit;
                    }
                    $page = json_decode($response->getBody());

                    if(isset($page->name) && is_numeric($page->id)) {
                        return ['status' => true];
                    }
                    break;
                case 'Like':
                case 'Comment':
                    if((strpos($data['link'], 'photo') !== false) && strpos($data['link'], 'fbid') !== false) {

                    } elseif (strpos($data['link'], 'videos') !== false) {

                    } elseif (strpos($data['link'], 'posts') !== false) {

                    }
                    break;
                default:
                    break;
            }
        } catch (Exception $e) {
            $media = $e->getCode();
        }

//        if($media['id'] != 0) {
//            return ['status' => true];
//        } else {
            return ['status' => false, 'message' => 'Неверная ссылка или не удалось получить данные из социальной сети или ссылка доступна только вам.'];
//        }
    }
}
