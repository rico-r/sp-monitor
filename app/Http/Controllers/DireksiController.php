<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class DireksiController extends Controller
{
    function dashboard()
    {
        $title = 'Dashboard Direksi';
        return view('direksi.dashboard', compact('title'));
    }
}
