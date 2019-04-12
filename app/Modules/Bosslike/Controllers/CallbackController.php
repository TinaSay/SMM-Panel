<?php

namespace App\Modules\Bosslike\Controllers;

use App\Helpers\GuzzleClient;
use App\Http\Controllers\Controller;
use App\Modules\Bosslike\Models\Fund;
use App\Modules\Bosslike\Models\Task;
use Illuminate\Http\Request;
use Auth;
use App\Modules\Bosslike\Models\Complains;
use Illuminate\Support\Facades\Mail;
use App\Modules\Bosslike\Models\Transactions;
use Illuminate\Support\Facades\Input;
use Validator;

/**
 * Class ProfileController
 * @package App\Modules\Bosslike\Controllers
 */
class CallbackController extends Controller
{

    public function __construct()
    {
        $config = \Config::get('mail');
        $this->from_name = $config['from']['name'];
        $this->from_address = $config['from']['address'];
    }

    public function index()
    {

    }

    public function store(Request $request)
    {
        $data = $request->only('task_id', 'type', 'comment');
        $user = Auth::user();
        $data['user_id'] = $user->id;
        $username = $user->first_name;
        $email = $user->email;
        $subject = $data['type'];
        $this->html_email($email, $username, $data['comment'], $subject, $data['task_id'], 'mail.complain-mail');

        Complains::create($data);
        toast()->success('Ваша жалоба отправлена!', 'Успех!');
        return redirect()->back();
    }

    public function html_email($email, $name, $text, $subject, $task_id = null, $template) {

        $data = ['name'=>$name, 'text'=>$text, 'subject'=>$subject, 'email'=>$email, 'task'=>$task_id];
        $from_name = $this->from_name;
        $from_address = $this->from_address;
        Mail::send($template, $data, function($message) use ($email, $name, $from_address, $from_name, $subject) {
            $message->to($from_address, $from_name)->subject
            ($subject);
            $message->from($from_address,$from_name);
        });
    }

    public function getFundCreate()
    {
        $allowed = $this->getUserAllowed();
        $balance = new GuzzleClient();
        $user_balance = $balance->getFormattedBalance();

        return view('callback.fund_create', ['balance' => $user_balance, 'allowed' => $allowed]);
    }

    public function postFundCreate(Request $request)
    {
        $min_sum = 100000;
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'cardnumber' => 'required|max:20',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            toast()->error('Ошибка!', 'Пожалуйста, заполните все поля.');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput(Input::all());
        }

        $data = $request->only('name', 'last_name', 'cardnumber', 'amount');

        $user = Auth::user();
        $data['user_id'] = $user->id;
        $data['billing_id'] = $user->billing_id;
        $data['amount'] = intval(str_replace(' ', '', $data['amount']));
        $allowedSum = $this->getUserAllowed();
        $subject = 'Вывод средств';
        $message = '<br/>Номер карты: <b>' . $data['cardnumber'] . '</b><br/> Сумма: <b>' . $data['amount'] . '</b>';

        if($data['amount'] < $min_sum) {
            toast()->error('Ошибка!', 'Минимальная сумма для вывода 100 000 сум.');
            return back()->withInput(Input::all());
        }

        if($data['amount'] > $allowedSum) {
            toast()->error('Ошибка!', 'Указанная сумма превышает ваш заработанный лимит.');
            return back()->withInput(Input::all());
        }


        $this->html_email($user->email, $data['name'] . ' ' . $data['last_name'], $message, $subject, null, 'mail.fund-mail');

        if(Fund::create($data)) {
            toast()->success('Ваша заявка принята, деньги зарезервированы.', 'Успех!');
            return redirect()->back();
        } else {
            toast()->error('Что-то пошло не так. Попробуйте ещё раз.', 'Ошибка!');
            return redirect()->back();
        }
    }

    public function getUserAllowed()
    {
        $user_id = Auth::id();
        $trans = Transactions::where('user_id', $user_id)->where('type', 'in')->where('action', 'Выполнил задание')->sum('points');
        $funds = Transactions::where('user_id', $user_id)->where('type', 'out')->where('action', 'Вывод средств')->sum('points');

        return $trans - $funds;
    }
}