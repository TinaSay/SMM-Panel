<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\FrontController;
use App\Modules\Blog\Models\Blog as BlogModel;
use App\Modules\Blog\Models\Topic;
use App\Modules\Blog\Requests\SaveTopicRequest;
use Blog;

class TopicController extends FrontController
{
    /**
     * @param BlogModel $blog
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addTopic(BlogModel $blog)
    {
        $this->title('Добавление темы');

        $this->view('blog.topics.add');

        return $this->render(compact('blog'));
    }

    /**
     * @param Topic $topic
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editTopic(Topic $topic)
    {
        $this->title('Редактирование темы "'.$topic->name.'"');

        $this->view('blog.topics.edit');

        return $this->render(compact('topic'));
    }

    /**
     * @param SaveTopicRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveTopic(SaveTopicRequest $request)
    {
        Blog::saveTopic($request);

        return redirect()->route('blog.topics', $request->blog_id);
    }
}