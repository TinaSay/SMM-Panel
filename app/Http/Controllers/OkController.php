<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

/**
 * Class OkController
 * @package App\Http\Controllers
 */
class OkController extends Controller
{
    /**
     * @return mixed
     */
    public function redirectToProvider()
    {
        return Socialite::driver('odnoklassniki')->redirect();
    }

    /**
     *
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('odnoklassniki')->user();
        return response()->json([
            'user' => $user,
        ]);
    }
}
