<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Assistant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;


class AsistenteController extends Controller
{

    public function index()
    {
        return app(AbogadoController::class)->index();
    }
}
//solucion temporal