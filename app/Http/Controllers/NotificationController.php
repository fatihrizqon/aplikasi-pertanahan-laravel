<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : View
    {
        $models = auth()->user()->notifications()->latest()->paginate(5);
        return view('profile.notifications', compact('models'));
    }
}
