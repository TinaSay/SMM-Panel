<?php

namespace App\Modules\SmmPro\Controllers;

use App\Helpers\GuzzleClient;
use App\Modules\SmmPro\Models\Service;
use App\Modules\SmmPro\Requests\OrderRequest;
use Illuminate\Http\Request;
use App\Modules\SmmPro\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Class MyOrdersController
 * @package App\Modules\SmmPro\Controllers
 */
class MyOrdersController extends Controller
{
    /**
     * @var GuzzleClient
     */
    protected $guzzle;

    /**
     * NewTaskController constructor.
     * @param GuzzleClient $client
     */
    public function __construct(GuzzleClient $client)
    {
        $this->guzzle = $client;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        /**
         * History of the User's orders
         */
        return view('smmpro::orders.my-orders', [
            'orders' => Order::where([
                'user_id' => Auth::user()->id
            ])->orderBy('created_at', 'desc')->paginate(),
        ]);
    }

    /**
     * @param OrderRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function api(OrderRequest $request)
    {
        $serviceId = $request->input('servid');
        $service = Service::findOrFail($serviceId);
        $link = $request->input('link');
        $order_id = 0;
        $start_count = 0;
        $quantity = $request->qty;
        $price = $service->quantity()->where('quantity', $quantity)->first();
        $balance = $this->guzzle->getUserBalance() / 100;
        $charge = $price->price;

        if ($balance < $charge) {
            toast()->error('У вас недостаточно средств на балансе', 'Неудача');
            return back();
        } else {
            if (stripos($service['service_api'], 'justanotherpanel') !== false) {  // Justanotherpanel
                $URL = str_replace('[QUANTITY]', $quantity, $service['service_api']);
                $URL = str_replace('[LINK]', $link, $URL);

                if (isset($additional) && !empty($additional)) {
                    $URL = str_replace('[ADDON]', $additional, $URL);
                }

                $client = new \GuzzleHttp\Client();
                $return = $client->post($URL);
                $resp = json_decode((string)$return->getBody()->getContents());

                if (property_exists($resp, 'error')) {
                    return response()->json($resp->error, 422);
                }

                if (isset($resp) && property_exists($resp, 'order')) {
                    $order_id = $resp->order;
                }
            } else if (stripos($service['service_api'], 'smmpanel') !== false) { // SMMPanel
                $_serviceId = last(explode('/', $service['service_api']));

                $data = array(
                    'api_key' => '77537a5ecdff62bbc9f438ddc82600ce',
                    'service_id' => $_serviceId,
                    'count' => $quantity,
                    'url' => $link,
                    'options' => []
                );

                $data = json_encode($data);
                $ch = curl_init('https://smmpanel.ru/api/add_order/');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 25);
                curl_setopt($ch, CURLOPT_FAILONERROR, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $content = json_decode(curl_exec($ch));

                if ($content->type == 'error') {
                    return response()->json($content->desc, 422);
                }
                $order_id = $content->data->order_id;
            } else { // в любом другом случае
                toast()->error('Внутренняя ошибка сервиса', 'Неудача');
                return back();
            }

            $order = new Order();
            $order->user_id = Auth::user()->id;
            $order->service_id = $serviceId;
            $order->quantity = $quantity;
            $order->link = $link;
            $order->charge = $charge;
            $order->order_api_id = $order_id;
            $order->status = 'active';
            $order->type = 'default';
            $order->start_count = 0;
            $order->remains = 0;
            $order->save();

            $this->guzzle->chargeClient($charge);
            return back()->withOrder($order);
//            return response()->json($order);
        }
    }
}
