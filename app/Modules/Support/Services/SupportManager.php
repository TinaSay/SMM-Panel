<?php

namespace App\Modules\Support\Services;

use App\Modules\Support\Models\Feedback;
use App\Modules\Support\Models\Screenshot;
use Illuminate\Http\Request;

class SupportManager
{
    protected $subjects = [
        1 => 'Идея, предложение',
        2 => 'Жалоба',
        3 => 'Сообщение об ошибке',
    ];

    protected $ifRead = [
        1 => 'Прочитано',
        2 => 'Не прочитано'
    ];

    /**
     * Return the array of available subjects.
     *
     * @return array
     */
    public function getSubjects(): array
    {
        return $this->subjects;
    }

    /**
     * @return array
     */
    public function getIfRead(): array
    {
        return $this->ifRead;
    }

    public function getUnread()
    {
        return Feedback::where('is_read', 0)->get();
    }

    public function hasUnread()
    {
        return Feedback::where('is_read', 0)->count();
    }

    public function save(Request $request)
    {
        $feedback = Feedback::create([
            'user_id' => \Auth::user()->id,
            'subject' => $request->subject,
            'message' => $request->message
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $ext = $file->getClientOriginalExtension();
                $filename = 'support_' . \Auth::user()->id . '_' . time() . '.' . $ext;
                $file->storeAs('uploads/support', $filename);

                $feedback->screenshots()->create([
                    'image' => $filename
                ]);
            }
        }
    }

    /**
     * @return Feedback[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Feedback::all();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getDetail($id)
    {
        return Screenshot::where('feedback_id', '=', $id)->first();
    }

    /**
     * @param $id
     */
    public function setRead($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->is_read = 1;
        $feedback->save();
    }
}
