<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Pago;
use App\Models\Lawyer;
use App\Models\Assistant;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        View::composer('*', function ($view) {

            $pagosPendientesCount = 0;
            $pagosPendientes = collect();

            if (Auth::check()) {

                $lawyer = Lawyer::where('user_id', Auth::id())->first();

                if ($lawyer) {

                    $pagosPendientes = Pago::where('estado', 'Pendiente')
                        ->whereHas('proceso', function ($q) use ($lawyer) {
                            $q->where('lawyer_id', $lawyer->id);
                        })
                        ->with('proceso.user') //user del proceso
                        ->get();

                    // Agregar el nombre del asistente a cada pago
                    $pagosPendientes->each(function ($pago) {
                        if ($pago->proceso && $pago->proceso->user_id) {
                            $assistant = Assistant::where('user_id', $pago->proceso->user_id)->first();
                            $pago->nombre_asistente = $assistant
                                ? $assistant->nombre . ' ' . $assistant->apellido
                                : 'Asistente';
                        } else {
                            $pago->nombre_asistente = 'Asistente';
                        }
                    });

                    $pagosPendientesCount = $pagosPendientes->count();
                }
            }

            $view->with([
                'pagosPendientesCount' => $pagosPendientesCount,
                'pagosPendientes' => $pagosPendientes
            ]);
        });
    }
}
