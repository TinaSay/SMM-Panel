<?php

namespace App\Modules\SmmPro\Controllers\Admin;

use App\Modules\SmmPro\Requests\ServiceSaveRequest;
use App\Modules\SmmPro\Models\Category;
use App\Modules\SmmPro\Models\Order;
use App\Modules\SmmPro\Models\Service;
use App\Http\Controllers\Controller;
use Cookie;

/**
 * Class ServicesController
 * @package App\Http\Controllers\Admin
 */
class ServicesController extends Controller
{
    /**
     * Default sorting order.
     *
     * @var array
     */
    protected $defaultSorting = [
        'weight', 'asc'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('smmpro::services.index', [
            'services' => Service::orderBy('created_at', 'desc')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $root = Cookie::get('rootCategory');
        $rootCategory = null;
        $category = Cookie::get('category');
        $childCategory = null;
        $categories = collect();

        if ($root) {
            $rootCategory = Category::find($root);
            $categories = $rootCategory->descendants;
        }

        if ($category) {
            $childCategory = Category::find($category);
        }

        $rootCategories = Category::whereIsRoot()->defaultOrder()->get();

        return view('smmpro::services.create', [
            //            'parentCategories' => Category::with('children')->defaultOrder()->whereNull('parent_id')->get()
            'rootCategories' => $rootCategories,
            'rootCategory' => $rootCategory,
            'childCategory' => $childCategory,
            'categories' => $categories
        ]);
    }

    public function duplicate($id)
    {
        $service = Service::find($id);
        $ancestors = Category::ancestorsOf($service->category_id);
        $rootCategory = null;
        $categories = collect();

        foreach ($ancestors as $node) {
            if (is_null($node->parent_id)) {
                $rootCategory = $node;

                break;
            }
        }

        if ($rootCategory) {
            $categories = $rootCategory->descendants;
        }

        return view('smmpro::services.duplicate', [
            'service' => $service,
            'rootCategory' => $rootCategory,
            'quantity' => $service->quantity()->get(),
            'categories' => $categories,
            'parentCategories' => Category::defaultOrder()->whereNull('parent_id')->get()
        ]);
    }

    /**
     * @param ServiceSaveRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ServiceSaveRequest $request)
    {
        $service = new Service();

        $service->category_id = $request->input('root_category');
        if (filled($request->input('subcategory'))) {
            $service->category_id = $request->input('subcategory');
        }
        if (filled($request->input('category_id'))) {
            $service->category_id = $request->input('category_id');
        }
        $service->name = $request->input('name');
        $service->description = str_replace("\r\n", '<br>', $request->input('description'));
        $service->service_api = $request->input('service_api');
        $service->service_order_api = $request->input('service_order_api');

        if ($request->input('type') == Service::TYPE_DEFAULT) {
            $service->type = 1;
        } else {
            $service->type = $request->input('type');
        }

        $service->active = $request->input('active');
        $service->save();

        $service->quantity()->delete();

        foreach ($request->quantities as $key => $value) {
            $service->quantity()->create([
                'quantity' => $value,
                'price' => $request->prices[$key]
            ]);
        }

        return redirect()->route('services.index')->with('success', 'Сервис добавлен!');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $service = Service::find($id);
        $ancestors = Category::ancestorsOf($service->category_id);
        $rootCategory = null;
        $categories = collect();

        foreach ($ancestors as $node) {
            if (is_null($node->parent_id)) {
                $rootCategory = $node;

                break;
            }
        }

        if ($rootCategory) {
            $categories = $rootCategory->descendants;
        }

        return view('smmpro::services.edit', [
            'service' => $service,
            'quantity' => $service->quantity()->get(),
            'rootCategory' => $rootCategory,
            'categories' => $categories,
            'parentCategories' => Category::defaultOrder()->whereNull('parent_id')->get()
        ]);
    }

    /**
     * @param ServiceSaveRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ServiceSaveRequest $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->category_id = $request->input('root_category');
        if (filled($request->input('category_id'))) {
            $service->category_id = $request->input('category_id');
        }
        $service->name = $request->input('name');
        $service->description = str_replace("\r\n", '<br>', $request->input('description'));
        $service->service_api = $request->input('service_api');
        $service->service_order_api = $request->input('service_order_api');

        if ($request->filled('type')) {
            $service->type = $request->input('type');
        }

        $service->reseller_price = $request->input('reseller_price');
        $service->active = $request->input('active');

        $service->save();

        $service->quantity()->delete();

        foreach ($request->quantities as $key => $value) {
            $service->quantity()->create([
                'quantity' => $value,
                'price' => $request->prices[$key]
            ]);
        }

        return redirect()->route('services.index')->with('success', 'Сервис обновлен!');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $service = Service::find($id);
        $order = Order::where('service_id', '=', $service->id)->first();
        if ($order) {
            return redirect()->route('services.index')->with('fail', 'Нельзя удалить сервис! На него есть заказы');
        }
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Сервис удален');
    }

    public function reorder()
    {
        return view('smmpro::services.reorder');
    }

    public function ajaxGetServices(\Illuminate\Http\Request $request)
    {
        $page = $request->page ?: 1;
        $sorting = $this->parseSortingArgument($request);

        $query = Service::whereNotNull('id');

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%$request->search%");
        }

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('filter') && $request->input('filter')) {
            if ($request->filter['type'] == 'category') {
                $query->whereIn('category_id', $request->filter['value']);

                $total = $query->count('id');
            }
        }

        $total = $query->count('id');

        if ($request->limit) {
            $query->offset(($page - 1) * $request->limit);
            $query->limit($request->limit);
        }

        $result = $query->sort($sorting)
                        ->get();

        $data = collect();

        if ($result->isNotEmpty()) {
            foreach ($result as $row) {
                $render = [];

                $category = Category::find($row->category_id)->name;

                $quantity = $row->quantity()->get()->first()
                    ? ($row->quantity()->get()->count() > 1 ? $row->quantity()->get()->first()->quantity.' - '.$row->quantity()->get()->last()->quantity : $row->quantity()->get()->first()->quantity)
                    : null;

                $price = $row->quantity()->get()->first()
                    ? ($row->quantity()->get()->count() > 1 ? number_format($row->quantity()->get()->first()->price, 0, ',', ' ').' - '.number_format($row->quantity()->get()->last()->price, 0, ',', ' ') : number_format($row->quantity()->get()->first()->price, 0, ',', ' '))
                    : number_format($row->price, 0, ',', ' ');

                $render['id'] = $row->id;
                $render['name'] = '<div>'.$row->name.'</div><small class="text-muted">'.$row->description.'</small>';
                $render['category_id'] = $category;
                $render['quantity'] = $quantity;
                $render['price'] = $price . ' сум';
                $render['created_at'] = $row->created_at->format('Y-m-d H:i:s');
                $render['actions'] = '
                <a href="' . route('service.duplicate', $row->id) . '" class="btn btn-xs btn-outline-secondary pr-2 pl-2">
                    <i class="far fa-clone"></i>
                </a>
                <a href="' . route('service.edit', $row->id) . '" class="btn btn-xs btn-outline-primary pr-2 pl-2">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="' . route('service.destroy', $row->id) . '" class="btn btn-xs btn-outline-danger pr-2 pl-2">
                    <i class="fa fa-trash"></i>
                </a>';

                $data->push($render);
            }
        }

        return [
            'filter' => $request->filter,
            'total' => $total,
            'totalFiltered' => $result->count(),
            'rows' => $data
        ];
    }

    public function ajaxSaveSorting(\Illuminate\Http\Request $request)
    {
        foreach ($request->items as $key => $item) {
            Service::where('id', $item['id'])->update([
                'weight' => $key
            ]);
        }
    }

    public function parseSortingArgument(\Illuminate\Http\Request $request): array
    {
        if ($request->has('sorting') && $request->sorting) {
            return explode('|', $request->sorting);
        }

        return $this->defaultSorting;
    }
}