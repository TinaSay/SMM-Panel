<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\FrontController;
use App\Modules\Blog\Models\Blog;

class BlogController extends FrontController
{
    /**
     * @param Blog $blog
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showTopics(Blog $blog)
    {
        $this->title('Темы блога "'.$blog->name.'"');

        $this->view('blog.manage.topics');

        $topics = $blog->topics;

        return $this->render(compact('blog', 'topics'));
    }
}