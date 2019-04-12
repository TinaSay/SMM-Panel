<?php

namespace App\Modules\SmmPro\Controllers\Admin;

use App\Helpers\GuzzleClient;
use App\Modules\SmmPro\Requests\OrderRequest;
use App\Modules\SmmPro\Models\Order;
use App\Modules\SmmPro\Models\Service;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class OrdersController
 * @package App\Http\Controllers\Admin
 */
class OrdersController extends Controller
{
    /**
     * @param $status
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();

        $resp = \response()->json($orders);

        return view('smmpro::orders.index', [
            'orders' => $orders,
            'resp' => $resp,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('smmpro::orders.edit', [
            'order' => Order::findOrFail($id),
            'services' => Service::all()
        ]);
    }

    /**
     * @param OrderRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(OrderRequest $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->service_id = $request->input('service_id');
        $order->quantity = $request->input('quantity');
        $order->link = $request->input('link');
        $order->charge = $request->input('charge');
        $order->order_api_id = $request->input('order_api_id');
        $order->status = $request->input('status');
        $order->type = $request->input('type');
        $order->start_count = $request->input('start_count');
        $order->remains = $request->input('remains');
        $order->save();

        return redirect()->route('orders.index', 'all')->with('success', 'Все изменения сохранены');
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cancel($id)
    {
        $guzzle = new GuzzleClient();

        $order = Order::findOrFail($id);
        $order->status = 'cancelled';
        $order->save();

        $guzzle->refundBalance($order->charge, $order->user->billing_id);

        return redirect()->route('orders.index', 'all')->with('success', 'Заказ отменен. Средства возмещены');
    }
}
