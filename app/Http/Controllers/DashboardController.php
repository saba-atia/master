<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
       public function index()
    {
        $user = auth()->user();
        $evaluation = $user->latestEvaluation;
        
        return view('dashboard', compact('user', 'evaluation'));
    }
}
