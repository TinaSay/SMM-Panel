<?php

namespace App\Modules\Bosslike\Controllers;

use App\Modules\Bosslike\Models\SocialUser;
use App\Http\Controllers\Controller;

/**
 * Class OkController
 * @package App\Modules\Bosslike\Controllers
 */
class OkController extends Controller
{
    public function delete($id)
    {
        $okUser = SocialUser::findOrFail($id);
        $okUser->delete();
        return redirect()->route('profile');
    }

}
