<?php

namespace App;

use App\Modules\SmmPro\Models\Order;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use GuzzleHttp;

/**
 * App\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $billing_id
 * @property string $login
 * @property string $email
 * @property string $password
 * @property int $role_id
 * @property string|null $ip
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereBillingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 */
class User extends Authenticatable
{
    use Notifiable;
    /**
     * user admin role
     */
    const ROLE_ADMIN = 1;

    const RETURN_URL = 'https://smm-pro.uz';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'billing_id', 'login', 'email', 'password', 'role_id', 'ip'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return mixed
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    public static function getUserBalance()
    {
        $client = new GuzzleHttp\Client([
            'base_uri' => 'https://billing.smm-pro.uz'
        ]);
        $response = $client->request('POST', '/api/get-balance', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . session('user_token')
            ]
        ]);

        $res = json_decode((string)$response->getBody()->getContents());
        if (!$res == null) {
            return $res;
        } else {
            return 0;
        }
    }

    /**
     * @param $order
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    public static function refundUserBalance($order)
    {
        $client = new GuzzleHttp\Client([
            'base_uri' => 'https://billing.smm-pro.uz'
        ]);

        $client->request('POST', '/api/deposit', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . session('user_token')
            ],
            'form_params' => [
                'amount' => $order->charge,
                'description' => 'Возврат денег за заказ № ' . $order->order_api_id . ' пользователя ' . \Auth::user()->billing_id,
                'client' => \Config::get('services.oauthConfig.keys.id'),
            ]
        ]);
    }


}
