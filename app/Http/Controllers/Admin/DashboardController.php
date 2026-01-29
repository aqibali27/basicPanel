<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;

class DashboardController extends Controller
{
    public function __construct(

        private Admin       $admin,
        private User        $user,
    )
    {
    }

    /**
     * @return Renderable
     */
    public function dashboard(): Renderable
    {
        return view('admin-views.dashboard');
    }
}
