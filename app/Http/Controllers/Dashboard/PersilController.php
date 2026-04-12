<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Persil;
use App\Traits\AuthorizationTrait;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersilController extends Controller
{

    use ControllerTrait;
    use AuthorizationTrait;

    public function __construct()
    {
        // $this->authorizeResource(Persil::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : View
    {
        $models = Persil::filter($request->get('filters'))
                          ->search($request->get('search'))
                          ->orderBy('id')
                          ->paginate($request->get('per-page', 10))
                          ->appends('query', null)
                          ->withQueryString();

        return view('dashboard.persil.index', compact('models'));
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
        $model = empty($id) ? new Persil : $this->findModel(['id' => $id]);

        return view('dashboard.persil.form', compact('model'));
    }

    /**
     * Save the specified resource in storage.
     */
    public function save(Request $request, string $id = null)
    {
        $model = empty($id) ? new Persil : $this->findModel(['id' => $id]);

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
        $model = Persil::where($params)->firstOrFail();
        return $model;
    }
}
