<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Auth;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

/**
 * Class VerifiedController.
 */
class VerifiedController
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('frontend.passbase.index');
    }
}
