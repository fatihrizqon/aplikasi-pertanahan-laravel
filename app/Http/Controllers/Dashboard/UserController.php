<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\View\View;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Traits\ControllerTrait;
use App\Traits\AuthorizationTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\NotificationService;

class UserController extends Controller
{
    private NotificationService $notificationService;

    use ControllerTrait;
    use AuthorizationTrait;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : View
    {
        $models = User::visibleTo($request->user())
                      ->filter($request->get('filters'))
                      ->search($request->get('search'))
                      ->orderBy('id')
                      ->paginate($request->get('per-page', 10))
                      ->appends('query', null)
                      ->withQueryString();

        return view('dashboard.users.index', compact('models'));
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
        $model = empty($id) ? new User : $this->findModel(['id' => $id]);

        return view('dashboard.users.form', compact('model'));
    }

    /**
     * Save the specified resource in storage.
     */
    public function save(Request $request, string $id = null)
    {
        $model = empty($id) ? new User : $this->findModel(['id' => $id]);

        $params = $request->all();

        $rules = $model->rules(empty($id) ? null : 'update');

        $model->validator($params, $rules)->validate();

        if (! empty($id) && empty($params['password'])) {
            unset($params['password'], $params['password_confirmation']);
        }

        if ($request->ajax()) {
            return;
        }

        $model->autoFill($params);

        $model->saveOrFail();

        $this->notificationService->userManagement(
            actor: auth()->user(),
            targets: User::role('superadmin')->get(),
            action: empty($id) ? 'created' : 'updated',
        );

        return back()->with(empty($id) ? 'success' : 'info', empty($id) ? 'Berhasil menambahkan data baru.' : 'Berhasil mengubah data terpilih.');
    }

    /**
     * Find the specified resource in storage.
     */
    public function findModel(array $params)
    {
        $model = User::where($params)->firstOrFail();
        return $model;
    }

    public function lock(string $id){
        $model = $this->findModel(['id' => $id]);
        $model->status = 0;
        $model->save();
        return redirect()->back()->with('warning', 'Selected record has been locked.');
    }

    public function unlock(string $id){
        $model = $this->findModel(['id' => $id]);
        $model->status = 1;
        $model->save();
        return redirect()->back()->with('info', 'Selected record has been unlocked.');
    }

    /**
     * Export a listing of the resource.
     */
    public function export(Request $request)
    {
        return Excel::download(
            new UsersExport($request),
            'users_' . now()->format('Ymd_His') . '.xlsx',
        );
    }

    /**
     * Import a listing of the resource.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            $import = new UsersImport();
            $import->beforeImport();
            Excel::import($import, $request->file('file'));
            $import->afterImport();

            return redirect()->back()->with('success', "The import process has completed successfully: {$import->created} created and {$import->updated} updated.");
        } catch (\Exception $e) {
            return redirect()->back()->with('danger', $e->getMessage());
        }
    }
}
