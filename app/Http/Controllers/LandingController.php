<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Show the public landing page.
     */
    public function index(): View
    {
        return view('landing', [
            'plan' => Plan::annual(),
        ]);
    }
}
