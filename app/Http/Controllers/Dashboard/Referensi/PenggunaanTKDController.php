<?php

namespace App\Http\Controllers\Dashboard\Referensi;

use App\Http\Controllers\Controller;
use App\Models\PenggunaanTKD;
use App\Traits\AuthorizationTrait;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenggunaanTKDController extends Controller
{

    use ControllerTrait;
    use AuthorizationTrait;

    public function __construct()
    {
        // $this->authorizeResource(PenggunaanTKD::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : View
    {
        $models = PenggunaanTKD::filter($request->get('filters'))
                          ->search($request->get('search'))
                          ->orderBy('id')
                          ->paginate($request->get('per-page', 10))
                          ->appends('query', null)
                          ->withQueryString();

        return view('dashboard.referensi.penggunaan_tkd.index', compact('models'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $model = $this->findModel(['id' => $id]);
        return $model;
    }

    /**
     * Show the form for the specified resource.
     */
    public function form(string $id = null)
    {
        $model = empty($id) ? new PenggunaanTKD : $this->findModel(['id' => $id]);

        return view('dashboard.referensi.penggunaan_tkd.form', compact('model'));
    }

    /**
     * Save the specified resource in storage.
     */
    public function save(Request $request, string $id = null)
    {
        $model = empty($id) ? new PenggunaanTKD : $this->findModel(['id' => $id]);

        $params = $request->all();

        $rules = $model->rules(empty($id) ? null : 'update');

        $model->validator($params, $rules)->validate();

        if ($request->ajax()) {
            return;
        }

        $model->autoFill($params);

        $model->saveOrFail();

        return back()->with('success', 'New record has been stored.');
    }

    /**
     * Find the specified resource in storage.
     */
    public function findModel(array $params)
    {
        $model = PenggunaanTKD::where($params)->firstOrFail();
        return $model;
    }
}
