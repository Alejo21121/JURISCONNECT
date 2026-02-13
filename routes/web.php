<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LawyerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AbogadoController;
use App\Http\Controllers\AsistenteController;
use App\Http\Controllers\LegalProcessController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConceptoController;
use App\Http\Controllers\ProcesoReportController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssistantExport;
use App\Exports\LawyersExport;
use App\Http\Controllers\DocumentoProcesoController;
use App\Http\Controllers\PagoController;

// ===================================================================
// RUTA POR DEFECTO
// ===================================================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ===================================================================
// RUTAS DE AUTENTICACIÓN
// ===================================================================
require __DIR__ . '/auth.php';


// ===================================================================
// RUTAS PROTEGIDAS POR AUTENTICACIÓN
// ===================================================================
Route::middleware(['auth'])->group(function () {

    // ===============================================================
    // DASHBOARDS
    // ===============================================================
    Route::get('/dashboard', [AdminController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard/abogado', [AbogadoController::class, 'index'])
        ->name('dashboard.abogado');

    Route::get('/dashboard/asistente', [AsistenteController::class, 'index'])
        ->name('dashboard.asistente');

    // ===============================================================
    // PERFIL
    // ===============================================================
    Route::prefix('perfil')->name('profile.')->group(function () {
        Route::get('/foto', [ProfileController::class, 'editPhoto'])->name('photo');
        Route::post('/foto', [ProfileController::class, 'updatePhoto'])->name('photo.update');
    });

    // ===============================================================
    // ABOGADOS (LAWYERS) - VALIDACIONES Y RUTAS ESPECÍFICAS
    // ===============================================================
    Route::prefix('lawyers')->name('lawyers.')->group(function () {

        // VALIDACIONES (DEBEN IR PRIMERO)
        Route::post('/check-duplicates', [LawyerController::class, 'checkDuplicates'])
            ->name('check-duplicates');

        Route::post('/check-field', [LawyerController::class, 'checkField'])
            ->name('check-field');

        // EXPORTACIONES
        Route::get('/export-pdf', [LawyerController::class, 'exportPDF'])
            ->name('export.pdf');

        Route::get('/export-excel', function () {
            return Excel::download(new LawyersExport, 'abogados.xlsx');
        })->name('export.excel');
    });

    // RUTAS DE ACTUALIZACIÓN Y ELIMINACIÓN DE LAWYERS
    Route::put('/lawyers/{lawyer}', [LawyerController::class, 'update'])
        ->name('lawyers.update');

    // RESOURCE DE LAWYERS (DEBE IR DESPUÉS DE LAS RUTAS ESPECÍFICAS)
    Route::resource('lawyers', LawyerController::class)->except(['update']);

    // ===============================================================
    // ASISTENTES - RUTAS ESPECÍFICAS
    // ===============================================================
    Route::prefix('assistants')->name('assistants.')->group(function () {
        Route::put('/{assistant}', [LawyerController::class, 'updateAssistant'])
            ->name('update');

        Route::delete('/{assistant}', [LawyerController::class, 'destroyAsist'])
            ->name('destroy');
    });

    // ALIAS PARA COMPATIBILIDAD
    Route::delete('/asistentes/{assistant}', [LawyerController::class, 'destroyAsist'])
        ->name('asistentes.destroy');

    // EXPORTACIONES DE ASISTENTES
    Route::prefix('asistente')->name('asistente.')->group(function () {
        Route::get('/export-pdf', [AssistantExport::class, 'exportPDF'])
            ->name('export.pdf');

        Route::get('/export-excel', function () {
            return Excel::download(new AssistantExport, 'Asistentes.xlsx');
        })->name('export.excel');
    });

    // ===============================================================
    // PROCESOS LEGALES
    // ===============================================================
    Route::prefix('procesos')->name('procesos.')->group(function () {

        // EXPORTACIONES
        Route::get('/export-pdf', [ProcesoReportController::class, 'exportPdf'])
            ->name('export.pdf');

        Route::get('/export-excel', [ProcesoReportController::class, 'exportExcel'])
            ->name('export.excel');

        // CRUD DE PROCESOS
        Route::get('/', [LegalProcessController::class, 'index'])->name('index');
        Route::get('/create', [LegalProcessController::class, 'create'])->name('create');
        Route::post('/', [LegalProcessController::class, 'store'])->name('store');
        Route::get('/{proceso}', [LegalProcessController::class, 'show'])->name('show');
        Route::get('/{proceso}/edit', [LegalProcessController::class, 'edit'])->name('edit');
        Route::put('/{proceso}', [LegalProcessController::class, 'update'])->name('update');
        Route::delete('/{proceso}', [LegalProcessController::class, 'destroy'])->name('destroy');

        // CONCEPTOS DENTRO DE PROCESOS
        Route::get('/{proceso}/concepto', [AbogadoController::class, 'mostrarFormularioConcepto'])
            ->name('concepto.create');

        Route::post('/{proceso}/concepto', [AbogadoController::class, 'guardarConcepto'])
            ->name('concepto.store');

        Route::put('/{proceso}/conceptos/{concepto}', [AbogadoController::class, 'updateConcepto'])
            ->name('conceptos.update');

        // LISTAR CONCEPTOS POR PROCESO
        Route::get('/{proceso}/conceptos', [ConceptoController::class, 'listByProceso'])
            ->name('conceptos');
    });

    // RUTAS ALTERNATIVAS DE PROCESOS
    Route::get('/mis-procesos', [LegalProcessController::class, 'index'])
        ->name('mis.procesos');

    Route::get('/legal-processes/create', [LegalProcessController::class, 'create'])
        ->name('legal_processes.create');

    // ===============================================================
    // CONCEPTOS JURÍDICOS (ABOGADO)
    // ===============================================================
    Route::prefix('abogado')->name('abogado.')->group(function () {

        Route::get('/mis-procesos', [AbogadoController::class, 'misProcesos'])
            ->name('misConceptos');

        Route::get('/procesos', [AbogadoController::class, 'listarProcesos'])
            ->name('listar-procesos');

        Route::post('/finalizar-proceso/{id}', [AbogadoController::class, 'finalizarProceso'])
            ->name('finalizar-proceso');

        Route::post('/proceso/{id}/concepto', [AbogadoController::class, 'guardarConcepto'])
            ->name('guardar-concepto');

        Route::get('/crear-concepto/{id}', [AbogadoController::class, 'mostrarFormularioConcepto'])
            ->name('crear-concepto');

        Route::post('/procesos/{id}/conceptos', [ConceptoController::class, 'storeProceso'])
            ->name('conceptos.storeProceso');
    });

    // ===============================================================
    // CONCEPTOS (GENERALES)
    // ===============================================================
    Route::prefix('conceptos')->name('conceptos.')->group(function () {
        Route::get('/create', [ConceptoController::class, 'create'])->name('create');

        Route::post('/procesos/{proceso}/conceptos', [ConceptoController::class, 'store'])
            ->name('store');

        Route::delete('/{id}', [ConceptoController::class, 'destroy'])
            ->name('destroy');
    });

    // MOSTRAR CONCEPTO ESPECÍFICO
    Route::get('/concepto_juridicos/{id}', [ConceptoController::class, 'show'])
        ->name('concepto.show');
});

//Validar asistente por admin
Route::post('/validar-asistente', [LawyerController::class, 'validarAsistente'])->name('validar.asistente');

//Historial de procesos

Route::get('/procesos/{id}/historial', [LegalProcessController::class, 'historial'])
    ->name('procesos.historial');

Route::delete('/documentos/{id}', [LegalProcessController::class, 'destroyDocumento'])
    ->name('documentos.destroy');

Route::middleware(['auth'])->group(function () {
    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
});

Route::post('/procesos/{id}/reabrir', [LegalProcessController::class, 'reabrir'])
    ->middleware('auth');

Route::post('/pagos/{pago}/aprobar', [PagoController::class, 'aprobar'])
    ->name('pagos.aprobar');

Route::post('/pagos/{pago}/rechazar', [PagoController::class, 'rechazar'])
    ->name('pagos.rechazar');

Route::post('/notifications/mark-all-read', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return redirect()->back();
})->name('notifications.mark-all-read')->middleware('auth');
