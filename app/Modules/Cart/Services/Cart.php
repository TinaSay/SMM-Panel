<?php

namespace App\Modules\Cart\Services;

use App\Modules\SmmPro\Models\Order;
use App\Modules\SmmPro\Models\Service;
use Session;

class Cart
{
    public function addItem($item)
    {
        $contents = $this->getContents();

        if ($contents->isNotEmpty()) {
            $existing = $contents->where('id', $item['id'])->first();

            if ($existing) {
                $cart = $contents->map(function ($i, $k) use ($item) {
                    if ($i['id'] == $item['id']) {
                        $i['qty']++;

                        return $i;
                    }

                    return $i;
                });

                session(['cart' => $cart]);

                return $cart;
            }
        }

        $item['qty'] = 1;

        $contents->push($item);

        session(['cart' => $contents]);

        return $contents;
    }

    public function getContents()
    {
        $items = Session::get('cart');
        $contents = collect();

        if (!$items) {
            return $contents;
        }

        foreach ($items as $item) {
            $contents->push($item);
        }

        return $contents;
    }

    public function emptyCart()
    {
        Session::forget('cart');
    }

    public function getTotal()
    {
        $total = 0;
        $items = $this->getContents();

        foreach ($items as $item) {
            $total += $item['price'] * $item['qty'];
        }

        return $total;
    }

    public function checkout()
    {
        $cart = $this->getContents();
        $total = $this->getTotal();
        $user = \Auth::user();
        $balance = $user->getUserBalance();

        if ($balance / 100 - $total < 0) {
            return -1;
        }

        foreach ($cart as $item) {
            $balance = $user->getUserBalance();

            if ($balance / 100 - $item['price'] * $item['qty']) {
                return -2;
            }

            $service = Service::find($item['id']);
            $charge = $item['price'] * $item['qty'];

            if (stripos($service->service_api, 'justanotherpanel') !== false) {  // Justanotherpanel
                $URL = str_replace('[QUANTITY]', $service->quantity, $service->service_api);
                $URL = str_replace('[LINK]', $service->link, $URL);

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
            } else if (stripos($service->service_api, 'smmpanel') !== false) { // SMMPanel
                $_serviceId = last(explode('/', $service['service_api']));

                $data = array(
                    'api_key' => '77537a5ecdff62bbc9f438ddc82600ce',
                    'service_id' => $_serviceId,
                    'count' => $service->quantity,
                    'url' => $service->link,
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
                return response()->json('Внутренняя ошибка сервиса', 422);
            }

            $order = new Order();
            $order->user_id = \Auth::user()->id;
            $order->service_id = $service->id;
            $order->quantity = $service->quantity;
            $order->link = $service->link;
            $order->charge = $service->charge;
            $order->order_api_id = $order_id;
            $order->status = 'active';
            $order->type = 'default';
            $order->start_count = 0;
            $order->remains = 0;
            $order->save();

            $client = new \GuzzleHttp\Client(['base_uri' => 'https://billing.smm-pro.uz']);

            $client->request('POST', '/api/charge', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . session('user_token')
                ],
                'form_params' => [
                    'amount' => $charge,
                    'description' => 'Оплата заказа № ' . $order_id . ' пользователя ' . \Auth::user()->billing_id . ' на smm-pro.uz',
                    'client' => \Config::get('services.oauthConfig.keys.id'),
                ]
            ]);

            $this->removeItem($item);
        }

        $this->emptyCart();

        return 0;
    }

    public function removeItem($item)
    {
        $contents = $this->getContents();

        if ($contents->isNotEmpty()) {
            $existing = $contents->where('id', $item['id'])->first();

            if ($existing) {
                $cart = $contents->filter(function ($i, $k) use ($item) {
                    return $i['id'] !== $item['id'];
                });

                session(['cart' => $cart]);
            }
        }
    }
}
