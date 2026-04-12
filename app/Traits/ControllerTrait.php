<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

trait ControllerTrait{

	public function show(string $id)
	{
		return App::call([$this, 'view'], ['id' => $id]);
	}

	public function create()
	{
		return App::call([$this, 'form']);
	}

	public function edit(string $id)
	{
		return App::call([$this, 'form'], ['id' => $id]);
	}

	public function store()
	{
		return App::call([$this, 'save']);
	}

	public function update(string $id)
	{
		return App::call([$this, 'save'], ['id' => $id]);
	}

	public function destroy(string $id)
	{
		return App::call([$this, 'delete'], ['id' => $id]);
	}

	public function delete(string $id, Request $request)
	{
		$model = $this->findModel(['id' => $id]);

		try {
			$model->deleteOrFail();
		} catch (\Throwable $e) {
			return redirect()->back()->with('warning', 'Unable to delete selected record, data is still in use.');
		}

		if ($request->ajax()) {
			return;
		}

		return redirect()->back()->with('success', 'Selected record has been deleted.');
	}
}
