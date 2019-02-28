<?php

namespace App\Modules\SmmPro\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SmmPro\Models\Category;

/**
 * Class CatalogController
 * @package App\Http\Controllers\Dashboard
 */
class CatalogController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function api()
    {
        $cats = Category::defaultOrder()->with('services')->get()->toTree();
        return response()->json($cats);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxCatalog()
    {
        $socials = Category::defaultOrder()->with('services')->get()->toTree();
        return view('smmpro::my-catalog', [
            'socials' => $socials
        ]);
    }
}
