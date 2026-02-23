<?php

namespace App\Http\Controllers;

use App\Models\Lawyer; // modelo de abogados
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $lawyers = Lawyer::all(); // obtiene todos los abogados
        $user = Auth::user(); // obtiene el usuario autenticado
        return view('dashboard', compact('lawyers' , 'user')); // envía a la vista
    }
}

