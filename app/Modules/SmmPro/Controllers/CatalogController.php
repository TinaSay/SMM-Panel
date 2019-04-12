<?php

namespace App\Modules\SmmPro\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SmmPro\Models\Category;
use App\Modules\SmmPro\Models\Service;

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

    public function page($id = 0)
    {
        $intro = \DB::table('intros')->where('id', 1)->first();
        $socials = Category::where('parent_id', null)->defaultOrder()->with([
            'children' => function($query){
                $query->defaultOrder();
            }
        ])->get();

        if ($id == 0){

            return view('smmpro::empty_catalog')->withSocials($socials)->with([
                'intro' => $intro
            ]);

        } else {

            $services = Service::where('category_id', $id)->orderBy('weight', 'asc')->get();

            if ($services->isNotEmpty()) {
                foreach ($services as $service) {
                    $qtx = $service->quantity()->orderBy('quantity')->get();

                    if ($qtx->isNotEmpty()) {
                        $service->quantities = $qtx->pluck('quantity');
                        $service->prices = $qtx->pluck('price');
                    }
                }
            }

            return view('smmpro::catalog')->with([
                'services' => $services,
                'socials' => $socials
            ]);

        }
    }


}
