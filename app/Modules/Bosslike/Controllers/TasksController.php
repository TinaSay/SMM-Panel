<?php

namespace App\Modules\Bosslike\Controllers;

use App\Modules\Bosslike\Models\SocialUser;
use App\Modules\Bosslike\Models\Task;
use App\Http\Controllers\Controller;
use InstagramScraper\Instagram;
use Config;
use GuzzleHttp\Client;
/**
 * Class NewTaskController
 * @package App\Modules\Bosslike\Controllers
 */
class TasksController extends Controller
{

    public function index()
    {
        return view('bosslike::tasks.all', [
            'tasks' => Task::all()
        ]);
    }

    public function check($id)
    {
        $user = \Auth::user();
        $task = Task::find($id);
        $socialUser = SocialUser::where('social_id', $task->service->social->id)
            ->where('user_id', $user->id)->first();

        switch ($task->service->social->name) {
            case 'Instagram':
                $resp = $this->instagram($socialUser->client_name, $socialUser->client_id, $task->link, $task->service->name);
                break;
            case 'Facebook':
                $resp = $this->facebook($socialUser->client_name, $socialUser->client_id, $task->link, $task->service->name, $socialUser->access_token);
                break;
            case 'Telegram':
                $resp = $this->telegram($task->link, $socialUser);
                break;
            case 'OK':

                break;
            default:
                break;
        }

        return response()->json($resp);
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
        // dd($result);
        if ($result->ok == true && $result->result->status == 'member') {
            return response()->json(['status' => 'success', 'message' => 'Задание выполнено']);
        } else {
            return response()->json(['status' => 'error', 'title' => 'Нажмите проверить.', 'message' => 'Выполнение не подтверждено, проверьте ещё раз.']);
        }
    }

    public function instagram($client_name, $client_id, $post, $service)
    {
        switch ($service) {
            case 'Like':
                $code = str_replace('/', '', str_replace('https://www.instagram.com/p/', '', $post));

                $instagram = Instagram::withCredentials(Task::INSTAGRAM_USERNAME, Task::INSTAGRAM_PASSWORD, '');
                $instagram->login();

                sleep(1);

                $likes = $instagram->getMediaLikesByCode($code, 50);

                foreach ($likes as $like) {
                    if ($like->getUsername() == $client_name) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'username' => $client_name]);
                    }
                }

                break;
            case 'Subscribe':
                $code = str_replace('/', '', str_replace('https://www.instagram.com/', '', $post));
                $instagram = Instagram::withCredentials(Task::INSTAGRAM_USERNAME, Task::INSTAGRAM_PASSWORD, '');
                $instagram->login();

                sleep(1);

                $followers = $instagram->getFollowing($client_id, 30, 30, true);

                foreach ($followers as $follower) {
                    if ($follower['username'] == $code) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'username' => $code]);
                    }
                }

                break;
            case 'Comment':
                $code = str_replace('/', '', str_replace('https://www.instagram.com/p/', '', $post));
                $instagram = new Instagram();
                $comments = $instagram->getMediaCommentsByCode($code, 50);

                foreach ($comments as $comment) {
                    if ($comment->getOwner()->getUsername() == $client_name) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'post' => $code, 'username' => $client_name]);
                    }
                }

                break;
            default:
                break;
        }
//        toast('example', 'title goes here');
//        toast()->error('message', 'title');
        return response()->json(['status' => 'error', 'title' => 'Нажмите проверить.', 'message' => 'Выполнение не подтверждено, проверьте ещё раз.']);
    }

    public function facebook($client_name, $client_id, $post, $service, $token)
    {
        $config = Config::get('services.facebook');

        $fb = new \Facebook\Facebook([
            'app_id' => $config['client_id'],
            'app_secret' => $config['client_secret'],
            'default_graph_version' => 'v3.2',
        ]);

        switch ($service) {
            case 'Like':
                try {
                    $response = $fb->get('/' . $client_id . '_' . $post . '/likes/', $token);
                } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                    echo 'Graph returned an error: ' . $e->getMessage();
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.']);
                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.']);
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
                    echo 'Graph returned an error: ' . $e->getMessage();
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.']);
//                    exit;
                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.']);
//                    exit;
                }
                $follows = json_decode($response->getBody());
                foreach ($follows->data as $follow) {
                    if ($follow->name == $post) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'post' => $post, 'username' => $client_name]);
                    }
                }
                break;
            case 'Comment':
                try {
                    $response = $fb->get('/' . $client_id . '_' . $post . '/comments/', $token);
                } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                    echo 'Graph returned an error: ' . $e->getMessage();
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.']);
                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.']);
                }
                $comments = json_decode($response->getBody());
                foreach ($comments->data as $comment) {
                    if ($comment->from->id == $client_id) {
                        return response()->json(['status' => 'success', 'message' => 'Задание выполнено', 'post' => $post, 'username' => $client_name]);
                    }
                }
                break;
            case 'Share':
                try {
                    $response = $fb->get('/' . $client_id . '/posts/', $token);
                } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                    echo 'Graph returned an error: ' . $e->getMessage();
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.']);
                } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    return response()->json(['status' => 'error', 'title' => 'Что то пошло не так.', 'message' => 'Попробуйте ещё раз.']);
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
        return response()->json(['status' => 'error', 'title' => 'Нажмите проверить.', 'message' => 'Выполнение не подтверждено, проверьте ещё раз.']);
    }
}
