<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\RedirectResponse;

/**
 * Class HomeController.
 */
class HomeController
{
    /**
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route(homeRoute());
        }

        return redirect()->route('frontend.auth.login');
    }
}
