<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\Providers\BillingOauthProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Bosslike;

/**
 * Class OauthController
 * @package App\Http\Controllers\Auth
 */
class OauthController extends Controller
{
    protected $request;

    /**
     * OauthController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(Request $request)
    {
        try {
            $config = Config::get('services.oauthConfig');
            $adapter = new BillingOauthProvider($config);

            $adapter->authenticate($request->all());

            $isConnected = $adapter->isConnected();

            $userProfile = $adapter->getUserProfile();

            if ($userProfile->status == 0) {
                throw new \Exception('Your account is banned.');
            }

            $user = User::where('billing_id', $userProfile->identifier)
                ->first();

            if ($user) { // Log in
                $request->session()->put('user_token', $userProfile->token);
                if ($user->billing_id == 0) {
                    $user->update([
                        'billing_id' => $userProfile->identifier,
                    ]);
                }

                Auth::login($user);
            } else { // Register and log in
                $request->session()->put('user_token', $userProfile->token);
                $user = [
                    'billing_id' => $userProfile->identifier,
                    'login' => $userProfile->username,
                    'email' => $userProfile->email,
                    'password' => Hash::make(str_random(8)),
                    'role_id' => 2,
                    'ip' => request()->ip(),
                ];

                $user = User::create($user);

                Auth::login($user);
            }

            $adapter->disconnect();
        } catch (\Exception $e) {
            echo 'Oops, we ran into an issue! ' . $e->getMessage().' in '.$e->getFile().' line '.$e->getLine();
            exit();
        }

        return redirect('/');
    }




}
