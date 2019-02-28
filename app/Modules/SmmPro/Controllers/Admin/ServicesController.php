<?php

namespace App\Modules\SmmPro\Controllers\Admin;

use App\Modules\SmmPro\Requests\ServiceSaveRequest;
use App\Modules\SmmPro\Models\Category;
use App\Modules\SmmPro\Models\Order;
use App\Modules\SmmPro\Models\Service;
use App\Http\Controllers\Controller;

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
        'id', 'asc'
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
        return view('smmpro::services.create', [
            'parentCategories' => Category::defaultOrder()->whereNull('parent_id')->get()
        ]);
    }

    public function duplicate($id)
    {
        return view('smmpro::services.duplicate', [
            'service' => Service::findOrFail($id),
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
        $service->name = $request->input('name');
        $service->description = $request->input('description');
        $service->quantity = $request->input('quantity');
        $service->service_api = $request->input('service_api');
        $service->service_order_api = $request->input('service_order_api');

        if ($request->input('type') == Service::TYPE_DEFAULT) {
            $service->type = 1;
        } else {
            $service->type = $request->input('type');
        }

        $service->price = $request->input('price');
        $service->reseller_price = $request->input('reseller_price');
        $service->active = $request->input('active');
        $service->save();

        return redirect()->route('services.index')->with('success', 'Сервис добавлен!');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        return view('smmpro::services.edit', [
            'service' => Service::findOrFail($id),
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
        $service->description = $request->input('description');
        $service->quantity = $request->input('quantity');
        $service->service_api = $request->input('service_api');
        $service->service_order_api = $request->input('service_order_api');

        if ($request->filled('type')) {
            $service->type = $request->input('type');
        }

        $service->price = $request->input('price');
        $service->reseller_price = $request->input('reseller_price');
        $service->active = $request->input('active');

        $service->save();

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

    public function ajaxGetServices(\Illuminate\Http\Request $request)
    {
        $page = $request->page ?: 1;
        $sorting = $this->parseSortingArgument($request);

        $query = Service::whereNotNull('id');

        $total = $query->count('id');

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

        $result = $query->offset(($page - 1) * $request->limit)
            ->limit($request->limit)
            ->sort($sorting)
            ->get();

        $data = collect();

        if ($result->isNotEmpty()) {
            foreach ($result as $row) {
                $render = [];

                $category = Category::find($row->category_id)->name;

                $render['id'] = $row->id;
                $render['name'] = $row->name;
                $render['description'] = $row->description;
                $render['category_id'] = $category;
                $render['quantity'] = $row->quantity;
                $render['price'] = number_format($row->price, 0, ',', ' ') . ' сум';
                $render['created_at'] = $row->created_at->format('Y-m-d H:i:s');
                $render['actions'] = '
                <a href="' . route('service.duplicate', $row->id) . '" class="btn btn-sm btn-outline-secondary pr-3 pl-3">
                    <i class="far fa-clone"></i>
                </a>
                <a href="' . route('service.edit', $row->id) . '" class="btn btn-sm btn-outline-primary pr-3 pl-3">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="' . route('service.destroy', $row->id) . '" class="btn btn-sm btn-outline-danger">
                    <i class="fa fa-trash"></i>
                </a>';

                $data->push($render);
            }
        }

        return [
            'total' => $total,
            'totalFiltered' => $result->count(),
            'rows' => $data
        ];
    }

    public function parseSortingArgument(\Illuminate\Http\Request $request): array
    {
        if ($request->has('sorting') && $request->sorting) {
            return explode('|', $request->sorting);
        }

        return $this->defaultSorting;
    }
}

