<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        $defaultDateRange = Carbon::now()->subDays(30);

        return view('sales');
    }
}
