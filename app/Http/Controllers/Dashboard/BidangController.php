<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BidangController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Bidang::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : View
    {
        $models = Bidang::filter($request->get('filters'))
                          ->search($request->get('search'))
                          ->where('id_persil', $request->route('persil'))
                          ->orderBy('id')
                          ->paginate($request->get('per-page', 10))
                          ->appends('query', null)
                          ->withQueryString();

        return view('dashboard.persil.bidang.index', compact('models'));
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
        $model = empty($id) ? new Bidang : $this->findModel(['id' => $id]);

        return view('dashboard.persil.bidang.form', compact('model'));
    }

    /**
     * Save the specified resource in storage.
     */
    public function save(Request $request, string $id = null)
    {
        $model = empty($id) ? new Bidang : $this->findModel(['id' => $id]);

        $params = $request->all();

        $rules = $model->rules(empty($id) ? null : 'update');

        $model->validator($params, $rules)->validate();

        if ($request->ajax()) {
            return;
        }

        $model->autoFill($params);

        $model->saveOrFail();

        return back()->with(empty($id) ? 'success' : 'info', empty($id) ? 'Berhasil menambahkan data baru.' : 'Berhasil mengubah data terpilih.');
    }

    /**
     * Find the specified resource in storage.
     */
    public function findModel(array $params)
    {
        $model = Bidang::where($params)->firstOrFail();
        return $model;
    }
}
