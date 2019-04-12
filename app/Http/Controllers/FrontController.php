<?php

namespace App\Http\Controllers;

use Auth;
use Config;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Session;

/**
 * Class Controller
 *
 * @package App\Http\Controllers
 */
class FrontController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $agent;

    protected $breadcrumbs;

    protected $classes = [];

    protected $description;

    protected $image = [
        'src' => null,
        'width' => null,
        'height' => null
    ];

    protected $metaDescription;

    protected $metaTitle;

    protected $pageTitle;

    protected $request;

    protected $title;

    protected $user;

    protected $view;

    protected $viewport;

    /**
     * Controller constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });

        $this->user = Auth::user();
    }

    /**
     * Set the body tag classes.
     *
     * @param string|array $classes
     * @return void
     */
    protected function classes($classes):void
    {
        is_array($classes)
            ? $this->classes = array_merge($this->classes, $classes)
            : $this->classes[] = $classes;
    }

    /**
     * Set the page description.
     *
     * @param $description
     * @return void
     */
    protected function description(string $description):void
    {
        $this->description = $description;

        //Meta::setDescription($description);
    }

    /**
     * Set the error message.
     *
     * @param string $message
     * @return void
     */
    protected function error(string $message):void
    {
        $this->message($message, 'error');
    }

    /**
     * Set the OG image.
     *
     * @param string $src
     * @param null   $width
     * @param null   $height
     * @return void
     */
    protected function image(string $src, $width = null, $height = null):void
    {
        $this->image = [
            'image' => $src,
            'width' => $width,
            'height' => $height
        ];

        /*Meta::opengraph()->addProperty('image', \Config::get('app.url').$src);

        if ($width && $height) {
            Meta::opengraph()->addProperty('image:width', $width);
            Meta::opengraph()->addProperty('image:height', $height);
        }*/
    }

    /**
     * Set the info message.
     *
     * @param string $message
     * @return void
     */
    protected function info(string $message):void
    {
        $this->message($message, 'info');
    }

    /**
     * Add a message to the current status messages stack.
     *
     * @param string $message
     * @param string $class
     * @return void
     */
    protected function message(string $message, string $class):void
    {
        $message = ['class' => $class, 'message' => $message];

        if (Session::has('messages')) {
            $messages = Session::get('messages');
            $messages[] = $message;

            Session::flash('messages', $messages);
        } else {
            Session::flash('messages', [$message]);
        }
    }

    /**
     * Register middleware hack.
     * @see https://laravel.com/docs/5.3/upgrade#5.3-session-in-constructors
     *
     * @return void
     */
    protected function registerMiddleware():void
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });
    }

    /**
     * Renders the view.
     *
     * @param array $data
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    protected function render(array $data = [])
    {
        $data['title'] = $this->title;
        $data['pageTitle'] = $this->pageTitle;

        asort($this->classes);
        $data['bodyClasses'] = implode(' ', $this->classes);
        $data['breadcrumbs'] = $this->breadcrumbs;

        $data['user'] = $this->user;

        $data['messages'] = collect();

        if (Session::has('messages')) {
            $data['messages'] = collect(Session::get('messages'));

            Session::forget('messages');
            Session::save();
        }

        return view($this->view)
            ->with($data);
    }

    /**
     * Set the success message.
     *
     * @param $message
     */
    protected function success(string $message):void
    {
        $this->message($message, 'success');
    }

    /**
     * Set the page title and meta title.
     *
     * @param $title
     * @return void
     */
    protected function title(string $title):void
    {
        $this->title = $title.' | '.Config::get('app.name');
        $this->pageTitle = $title;

        //Meta::setTitle($title);
    }

    /**
     * Set the page type, e.g. "article", "video", etc.
     *
     * @param $type
     * @return void
     */
    protected function type(string $type):void
    {
        //Meta::opengraph()->addProperty('type', $type);
    }

    /**
     * Set the page url form OpenGraph.
     *
     * @param string $url
     * @return void
     */
    protected function url(string $url):void
    {
        //Meta::opengraph()->setUrl($url);
    }

    /**
     * Set the active view for the page.
     *
     * @param string $view
     * @return void
     */
    protected function view(string $view):void
    {
        $this->view = $view;
    }

    /**
     * Set the warning message.
     *
     * @param string $message
     * @return void
     */
    protected function warning(string $message):void
    {
        $this->message($message, 'warning');
    }
}
