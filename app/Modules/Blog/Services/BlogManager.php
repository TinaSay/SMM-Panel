<?php

namespace App\Modules\Blog\Services;

use App\Modules\Blog\Models\Blog;
use App\Modules\Blog\Models\Topic;
use App\Modules\Blog\Requests\SaveTopicRequest;

class BlogManager
{
    /**
     * @return Blog
     */
    public function blog():Blog
    {
        return new Blog;
    }

    /**
     * @param SaveTopicRequest $request
     *
     * @return void
     */
    protected function createTopic(SaveTopicRequest $request):void
    {
        $blog = Blog::find($request->blog_id);

        $blog->topics()->create([
            'name' => $request->input('name'),
            'description' => $request->has('description') ? $request->description : null
        ]);
    }

    /**
     * @param SaveTopicRequest $request
     *
     * @return void
     */
    public function saveTopic(SaveTopicRequest $request):void
    {
        $request->has('edit') ? $this->updateTopic($request) : $this->createTopic($request);
    }

    /**
     * @return Topic
     */
    public function topic():Topic
    {
        return new Topic;
    }

    /**
     * @param SaveTopicRequest $request
     *
     * @return void
     */
    protected function updateTopic(SaveTopicRequest $request):void
    {
        $blog = Blog::find($request->blog_id);

        $blog->topics()->where('id', $request->input('id'))->update([
            'name' => $request->input('name'),
            'description' => $request->has('description') ? $request->description : null
        ]);
    }
}