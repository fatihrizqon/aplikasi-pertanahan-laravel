<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(): View
    {
        $model = Auth::user();

        return view('profile.edit', compact('model'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $model = $this->findModel(['id' => $request->user()->id]);

        $params = $request->all();

        $rules = $model->rules('update-profile');

        $model->validator($params, $rules)->validate();

        if (empty($params['password'])) {
            unset($params['password'], $params['password_confirmation']);
        }

        if ($request->ajax()) {
            return;
        }

        $model->autoFill($params);

        $model->saveOrFail();

        return back()->with('success', 'Profile has been updated.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function findModel(array $params)
    {
        $model = User::where($params)->firstOrFail();
        return $model;
    }
}
