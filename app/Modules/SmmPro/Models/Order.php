<?php

namespace App\Modules\SmmPro\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $user_id
 * @property int $service_id
 * @property int $quantity
 * @property string $link
 * @property string $charge
 * @property string $order_api_id
 * @property string $status
 * @property string $type
 * @property int $start_count
 * @property int $remains
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\SmmPro\Models\Service $service
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order query()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereCharge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereOrderApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereRemains($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereStartCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\SmmPro\Models\Order whereUserId($value)
 */
class Order extends Model
{
    protected $table = 'smmpro_orders';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @param $order
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function checkOrderStatus($order)
    {
        if ($order->status !== 'cancelled') { //no actions if the order already has status "cancelled"

            if (stripos($order->service->service_api, 'smmpanel') !== false) { //smmpanel
                $data = array(
                    'api_key' => '77537a5ecdff62bbc9f438ddc82600ce',
                );

                $data = json_encode($data);
                $ch = curl_init('https://smmpanel.ru/api/check_status/' . $order->order_api_id);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 25);
                curl_setopt($ch, CURLOPT_FAILONERROR, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $content = json_decode(curl_exec($ch));


                if ($content->data->status == 9) { //change local status if order status is cancelled
                    $order->status = 'cancelled';
                    $order->save();
                    User::refundUserBalance($order);
                }

            } elseif (strpos($order->service->service_api, 'justanotherpanel') !== false) {  // Justanotherpanel
                $URL = str_replace('[OrderID]', $order->order_api_id, $order->service->service_order_api);
                $client = new \GuzzleHttp\Client();
                $return = $client->post($URL);
                $resp = json_decode((string)$return->getBody()->getContents());

                if (!isset($resp->status)) {
                    return;
                }
                if ($resp->status == "Canceled") {
                    $order->status = 'cancelled';
                    $order->save();
                    User::refundUserBalance($order);
                }

            } //endif Justanotherpanel

        } //endif status="cancelled"

    }//end CheckOrderStatus
}
