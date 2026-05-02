<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StatsController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        return view('stats.show', compact('user'));
    }
}
