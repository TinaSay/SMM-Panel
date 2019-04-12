<?php

namespace App\Modules\Instashop\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Instashop\Models\Instashop;
use App\Modules\Instashop\Models\InstashopImages;
use App\User;
use InstagramScraper\Instagram;
use Config;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
//use Mockery\Exception;
use InstagramScraper\Exception;

/**
 * Class ProfileController
 * @package App\Modules\Bosslike\Controllers
 */
class InstaShopController extends Controller
{

    public function __construct()
    {
        $this->instagram = new Instagram();
    }

    public function home()
    {
        return view('instashop::index');
    }

    public function tags(Request $request)
    {
        if($request->has('tag')) {
            $tag = $request->input('tag');
        }

        $result = $this->instagram->searchTagsByTagName($tag);
        $output = '';
        $select = '';
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $output .= '
                    <a href="javascript:void(0)" class="hashtags-item" data-name="' . $row->getName() . '">
                        <div class="text">
                            <p>#' . $row->getName() . '</p>
                        </div>
                        <div class="posts">
                            <span>' . $row->getMediaCount() . ' posts <i class="fa fa-play"></i></span>
                        </div>
                    </a>
                ';
                $select .= '
                    <option value="' . $row->getName() . '">' . $row->getName() . '</option>
                ';
            }
        }
        else
        {
            $output = '<h3>По данному запросу ничего не найдено.</h3>';
        }
        $all = ['output' => $output, 'hashtags' => $select];
        return $all;
    }

    public function posts(Request $request)
    {
        if($request->has('search')) {
            $search = $request->input('search');
        }
        $result = $this->instagram->getPaginateMediasByTag($search);
//        $medias = $instagram->getMediasByTag($search);

        $medias = $result['medias'];

//        if ($result['hasNextPage'] === true) {
//            $result = $instagram->getPaginateMediasByTag($search, $result['maxId']);
//            $medias = array_merge($medias, $result['medias']);
//        }
//        <div class="insta-ava" style="background-image: url(img/ins_ava1.JPG);"></div>
//        <span class="media_created">' . \Carbon\Carbon::createFromFormat('U', $media["createdTime"])->format('d.m.Y') . '</span>' .
//                                        '<span class="likes_count">' . $media['likesCount'] . '</span>
//                                        <span class="comments_count">' . $media['commentsCount'] . '</span>
        $output = '';
        if(count($medias) > 0)
        {
            foreach($medias as $media)
            {
                $output .= '
                    <div class="col-6 col-sm-6 col-md-3 col-lg-3">
                        <div class="gallery-item">
                            <a data-code="' . $media['shortcode'] . '" class="fancybox-gallery get_more_info" data-fancybox="gallery" href="#"  data-toggle="modal" data-target="#showPost">
                                <img src="' . $media['squareImages'][1] . '" alt="' . $media["caption"] . '">
                                <div class="hvrbox-top">
                                    <div class="hvrbox-text">
                                        ' . str_limit($media["caption"], 60) . '
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                ';
            }
        }
        else
        {
            $output = '<h3>По данному запросу ничего не найдено.</h3>';
        }
        print $output;
    }

    public function create(Request $request)
    {
        $code = $request->input('code');
        $cat = $request->input('category_id');

        try {
            $media = $this->instagram->getMediaByCode($code);
        } catch (Exception\InstagramException $e) {
            print 'Ничего не найдено!';
            exit;
        }

        $instashop = new Instashop;
        $instashop->post_id = $media['id'];
        $instashop->owner_id = $media['ownerId'];
        $instashop->shortcode = $media['shortCode'];
        $instashop->caption = $media['caption'];
        $instashop->post_created = $media['createdTime'];
        $instashop->link = $media['link'];
        $instashop->thumbnail = $media['imageStandardResolutionUrl'];
        $instashop->category_id = $cat;
        $instashop->type = $media['type'];
        $instashop->location_name = $media['locationName'];
        $instashop->location_id = $media['locationId'];
        $instashop->likes_count = $media['likesCount'];
        $instashop->comments_count = $media['commentsCount'];
        $instashop->username = $media['owner']['username'];
        $instashop->profile_pic = $media['owner']['profilePicUrl'];
        $instashop->full_name = $media['owner']['fullName'];
        if($media['type'] == 'video') {
            $instashop->video = $media['videoStandardResolutionUrl'];
            $instashop->video_views = $media['videoViews'];
        }

        $post = $instashop->save();

        if($media['type'] == 'sidecar') {
            foreach ($media['sidecarMedias'] as $image) {
                $sidecar = new InstashopImages;
                $sidecar->instashop_id = $instashop->id;
                $sidecar->thumbnail = $image['imageStandardResolutionUrl'];
                $sidecar->save();
            }
        }

        if($post) {
            return response()->json(['status' => 'success', 'title' => 'Успех!', 'message' => 'Публикация сохранена']);
        }
        return response()->json(['status' => 'error', 'title' => 'Неудача!', 'message' => 'Что-то пошло не так! Публикация не сохранена']);

    }


    public function full(Request $request)
    {
        if($request->has('code')) {
            $code = $request->input('code');
        }

        try {
            $media = $this->instagram->getMediaByCode($code);
        } catch (Exception\InstagramException $e) {
            print 'Ничего не найдено!';
            exit;
        }
        $output = '';
        if(!empty($media['id']) > 0)
        {
            $output = '
                <div class="modal-img" data-code="' . $media['shortCode'] . '">
                    <div class="modal-img-body">
                        <div class="top"><img src="' . $media['imageStandardResolutionUrl'] . '" alt="' . substr($media['caption'], 0, 50) . '"></div>
                        <div class="bottom">
                            <div class="ava"><img src="' . $media['owner']['profilePicUrl'] . '" alt="' . $media['username'] . '"></div>
                            <div class="tags">
                                <a href="' . $media['link'] . '" target="_blank">' . $media['caption'] . '</a>
                            </div>
                        </div>
                        <ul class="info">
                            <li>' . \Carbon\Carbon::createFromFormat('U', $media["createdTime"])->format('d.m.Y H:i') . '</li>
                            <span class="likes_count">' . $media['likesCount'] . ' лайков</span> 
                            <span class="comments_count">' . $media['commentsCount'] . ' комментарий</span>
                        </ul>
                        <select name="category_id" id="category_id">
                            <option value="1">Cosmetics</option>
                            <option value="2">Car accessories</option>
                            <option value="3">Phones</option>
                            <option value="4">Caps</option>
                        </select>
                    </div>
                </div>
            ';
//            <li>' . (!empty($media['locationName'])) ? $media['locationName'] : "Неизвестно" . '</li>
        }
        else
        {
            $output = '<h3>По данному запросу ничего не найдено.</h3>';
        }
        print $output;
    }
}