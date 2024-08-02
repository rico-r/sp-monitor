<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class KepalaCabangController extends Controller
{
    function dashboard()
    {
        $title = 'Dashboard Kepala Cabang';
        return view('kepala-cabang.dashboard', compact('title'));
    }
}
