<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;

class PanelController extends Controller
{
    public function dashboard()
    {
        return view('rider.dashboard');
    }

    public function orders()
    {
        return view('rider.orders.index');
    }
}
