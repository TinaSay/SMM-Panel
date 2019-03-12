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
            $task->picture = $validator['picture'];
            $task->type = $validator['type'];

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
                $result = $this->validateFacebook($data, $service->name, $socialUsers->access_token, $socialUsers->client_id);
                break;
            default:
                break;
        }

        return $result;
    }

    public function validateInstagram($data, $service)
    {
        $instagram = new \InstagramScraper\Instagram();
        try {
            switch ($service) {
                case 'Subscribe':
                    $username = str_replace('/', '',str_replace('https://www.instagram.com/', '', $data['link']));
                    $media = $instagram->getAccount($username);
                    $type = 'page';
                    break;
                case 'Like':
                case 'Comment':
                    $media = $instagram->getMediaByUrl($data['link']);
                    $type = 'post';
                break;
                default:
                    break;
            }
        } catch (Exception $e) {
            $media = $e->getCode();
        }

        if($media['id'] != 0) {
            (isset($media['imageThumbnailUrl'])) ? $pic = $media['imageThumbnailUrl'] : $pic = $media['profilePicUrl'];
            return ['status' => true, 'picture' => $pic, 'type' => $type];
        } else {
            return ['status' => false, 'message' => 'Неверная ссылка или не удалось получить данные из социальной сети или ссылка доступна только вам.'];
        }
    }

    public function validateFacebook($data, $service, $token, $client_id)
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
                    $response = $fb->get('/' . $url[0] . '?fields=picture', $token);
                } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                    return ['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()];
                } catch(\Facebook\Exceptions\FacebookSDKException $e) {
//                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    return ['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()];
                }
                $page = json_decode($response->getBody());

                if(isset($page->name) && is_numeric($page->id)) {
                    return ['status' => true, 'picture' => $page->picture->data->url, 'type' => 'page'];
                }
                break;
            case 'Like':
            case 'Share':
            case 'Comment':
                if((strpos($data['link'], 'photo') !== false) && strpos($data['link'], 'fbid') !== false) {
                    $pos = explode("?", $data['link'], 2);
                    $pos = explode("&", $pos[1], 2);
                    $postId = substr($pos[0], strpos($pos[0], 'fbid=') + 5);
                } elseif (strpos($data['link'], 'videos') !== false) {
                    $postId = str_replace('/', '', substr($data['link'], strpos($data['link'], 'videos/') + 7));
                } elseif (strpos($data['link'], 'posts') !== false) {
                    $postId = str_replace('/', '', substr($data['link'], strpos($data['link'], 'posts/') + 6));
                }

                try {
                    $response = $fb->get('/' . $client_id . '_' . $postId . '?fields=picture,type', $token);
                } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                    return ['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()];
                } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                    return ['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.', 'error' => $e->getMessage()];
                }
                $post = json_decode($response->getBody());

                if(isset($post->picture) && isset($post->id)) {
                    return ['status' => true, 'picture' => $post->picture, 'type' => $post->type];
                }
                break;
            default:
                break;
        }

        return ['status' => false, 'message' => 'Неверная ссылка или не удалось получить данные из социальной сети или ссылка доступна только вам.'];
    }
}
