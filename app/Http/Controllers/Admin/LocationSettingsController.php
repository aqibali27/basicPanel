<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;


class LocationSettingsController extends Controller
{
    public function __construct()
    {
    }

    /**
     * @return Renderable
     */
    public function location_index(): Renderable
    {
        return view('admin-views.business-settings.location-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */

}
