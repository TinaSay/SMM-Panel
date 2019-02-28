<?php

namespace App\Modules\SmmPro\Controllers;

use App\Modules\SmmPro\Models\Category;
use App\Modules\SmmPro\Models\Service;
use App\Http\Controllers\Controller;

/**
 * Class QuickOrderController
 * @package App\Http\Controllers\Frontend
 */
class QuickOrderController extends Controller
{
    /**
     * @param null $service
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function quickOrder($service = null)
    {

        if ($service == null) {
            $category = Category::where('alias', '=', 'instagram')->firstOrFail();
        } else {
            $category = Category::where('alias', '=', $service)->firstOrFail();
        }

        $catDescendants = Category::descendantsOf($category->id);

        return view('frontend.pages.quickOrder', [
            'services' => Service::whereIn('category_id', $catDescendants)->get()
        ]);
    }
}
