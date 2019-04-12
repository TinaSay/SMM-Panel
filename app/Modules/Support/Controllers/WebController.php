<?php

namespace App\Modules\Support\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Support\Models\Feedback;
use App\Modules\Support\Requests\SaveFeedbackRequest;
use Help;

class WebController extends Controller
{
    public function saveFeedback(SaveFeedbackRequest $request)
    {
        Help::save($request);

        toast()->success('Спасибо за ваше сообщение, оно поможет нам сделать наш сервис лучше.', 'Успех!');

        return redirect()->back();
    }

    public function showFeedback()
    {
        return view('support.list')->with([
            'feedbacks' => Help::getAll()
        ]);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDetail($id)
    {
        Help::setRead($id);
        return view('support.detail', [
            'screenshot' => Help::getDetail($id)
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSupportForm()
    {
        return view('support.form');
    }

}
