<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lawyer;
use App\Models\User;
use App\Models\Assistant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendCredentialsToLawyer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LawyersExport;

class LawyerController extends Controller
{
    /**
     * Mapeo de campos frontend a backend
     */
    private const FIELD_MAP = [
        'numeroDocumento' => 'numero_documento',
        'numero_documento' => 'numero_documento',
        'correo' => 'correo',
        'telefono' => 'telefono',
        'nombre' => 'nombre',
        'apellido' => 'apellido'
    ];

    /**
     * Mensajes de duplicados por campo
     */
    private const DUPLICATE_MESSAGES = [
        'numero_documento' => 'El número de documento :value ya está registrado',
        'correo' => 'El correo electrónico :value ya está registrado',
    ];


    /**
     * Verificar si existe un campo específico
     */
    public function checkField(Request $request)
    {
        try {
            $field = $request->input('field');
            $value = $request->input('value');
            $currentId = $request->input('current_id');

            // Validación de entrada
            if (!$field || $value === null || trim($value) === '') {
                return response()->json([
                    'exists' => false,
                    'message' => 'Datos incompletos'
                ]);
            }

            // Verificar que el campo sea válido
            if (!isset(self::FIELD_MAP[$field])) {
                Log::warning('Campo no válido recibido', [
                    'field' => $field,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'exists' => false,
                    'message' => 'Campo no válido'
                ]);
            }

            $dbField = self::FIELD_MAP[$field];
            $exists = $this->fieldExists($dbField, $value, $currentId);

            Log::info('Resultado de validación', [
                'field' => $field,
                'dbField' => $dbField,
                'exists' => $exists
            ]);

            return response()->json([
                'exists' => $exists,
                'field' => $field,
                'value' => $value
            ]);
        } catch (\Exception $e) {
            Log::error('Error en checkField', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'exists' => false,
                'error' => 'Error del servidor'
            ], 500);
        }
    }

    /**
     * Verificar múltiples campos por duplicados
     */
    public function checkDuplicates(Request $request)
    {
        try {
            $duplicates = [];
            $currentId = $request->input('current_id');

            // SOLO revisar campos enviados
            foreach ($request->only(['numero_documento', 'correo']) as $field => $value) {

                if (!$value) {
                    continue;
                }

                if ($this->fieldExists($field, $value, $currentId)) {
                    $duplicates[] = [
                        'field' => $field,
                        'value' => $value,
                        'message' => str_replace(
                            ':value',
                            $value,
                            self::DUPLICATE_MESSAGES[$field] ?? 'El valor ya está registrado'
                        )
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'duplicates' => $duplicates,
                'has_duplicates' => !empty($duplicates)
            ]);
        } catch (\Exception $e) {
            Log::error('Error en checkDuplicates', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error del servidor'
            ], 500);
        }
    }

    /**
     * Helper: Verificar si un campo existe en la BD
     */
    private function fieldExists(string $field, $value, $currentId = null): bool
    {
        // ========= LAWYERS =========
        $lawyerQuery = Lawyer::where($field, $value);
        if ($currentId && is_numeric($currentId)) {
            $lawyerQuery->where('id', '!=', $currentId);
        }

        if ($lawyerQuery->exists()) {
            return true;
        }

        // ========= ASSISTANTS =========
        $assistantQuery = Assistant::where($field, $value);
        if ($currentId && is_numeric($currentId)) {
            $assistantQuery->where('id', '!=', $currentId);
        }

        if ($assistantQuery->exists()) {
            return true;
        }

        // ========= USERS =========
        if ($field === 'correo') {

            $userQuery = User::where('email', $value);

            if ($currentId && is_numeric($currentId)) {

                // Excluir usuario si es abogado
                $lawyer = Lawyer::find($currentId);
                if ($lawyer && $lawyer->user_id) {
                    $userQuery->where('id', '!=', $lawyer->user_id);
                }

                // Excluir usuario si es asistente
                $assistant = Assistant::find($currentId);
                if ($assistant && $assistant->user_id) {
                    $userQuery->where('id', '!=', $assistant->user_id);
                }
            }

            if ($userQuery->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Crear nuevo abogado
     */
    public function store(Request $request)
    {
        $tipo = $request->input('tipodeusuario');

        DB::beginTransaction();

        try {
            /**
             * ================================================
             *  CREAR ABOGADO
             * ================================================
             */
            if ($tipo === 'lawyer') {
                $validated = $request->validate([
                    'nombre' => 'required|string|max:255',
                    'apellido' => 'required|string|max:255',
                    'tipo_documento' => 'required|string|max:50',
                    'numero_documento' => 'required|string|max:50'
                        . '|unique:lawyers,numero_documento'
                        . '|unique:assistants,numero_documento',
                    'correo' => 'required|email|max:255'
                        . '|unique:lawyers,correo'
                        . '|unique:assistants,correo'
                        . '|unique:users,email',
                    'telefono' => 'nullable|string|max:20',
                    'especialidad' => 'nullable|string|max:255',
                ]);

                $user = User::create([
                    'name' => trim($validated['nombre']) . ' ' . trim($validated['apellido']),
                    'email' => trim(strtolower($validated['correo'])),
                    'password' => Hash::make($validated['numero_documento']),
                    'role_id' => 2,
                    'numero_documento' => trim($validated['numero_documento']),
                ]);

                $lawyer = Lawyer::create([
                    'nombre' => trim($validated['nombre']),
                    'apellido' => trim($validated['apellido']),
                    'tipo_documento' => $validated['tipo_documento'],
                    'numero_documento' => trim($validated['numero_documento']),
                    'correo' => trim(strtolower($validated['correo'])),
                    'telefono' => $validated['telefono'] ?? null,
                    'especialidad' => $validated['especialidad'] ?? null,
                    'user_id' => $user->id,
                ]);

                DB::commit();

                $this->sendCredentials($validated['correo'], $user, $validated['numero_documento'], $lawyer->id);

                return response()->json([
                    'success' => true,
                    'message' => 'Abogado creado correctamente.',
                    'lawyer' => $lawyer
                ], 201);
            }

            /**
             * ================================================
             *  CREAR ASISTENTE
             * ================================================
             */
            if ($tipo === 'assistant') {
                $validated = $request->validate([
                    'nombre' => 'required|string|max:255',
                    'apellido' => 'required|string|max:255',
                    'tipo_documento' => 'required|string|max:50',
                    'numero_documento' => 'required|string|max:50'
                        . '|unique:assistants,numero_documento'
                        . '|unique:lawyers,numero_documento',
                    'correo' => 'required|email|max:255|unique:assistants,correo|unique:users,email',
                    'telefono' => 'nullable|string|max:20',
                    'lawyers' => 'array',
                    'lawyers.*' => 'exists:lawyers,id',
                ]);

                $user = User::create([
                    'name' => $validated['nombre'] . ' ' . $validated['apellido'],
                    'email' => strtolower($validated['correo']),
                    'password' => Hash::make($validated['numero_documento']),
                    'role_id' => 3,
                    'numero_documento' => $validated['numero_documento'],
                ]);

                $assistant = Assistant::create([
                    'user_id' => $user->id,
                    'nombre' => $validated['nombre'],
                    'apellido' => $validated['apellido'],
                    'tipo_documento' => $validated['tipo_documento'],
                    'numero_documento' => $validated['numero_documento'],
                    'correo' => strtolower($validated['correo']),
                    'telefono' => $validated['telefono'] ?? null,
                ]);

                // 🔔 ASIGNAR ABOGADOS Y NOTIFICAR
                if ($request->has('lawyers') && count($request->lawyers) > 0) {
                    $assistant->lawyers()->sync($request->lawyers);

                    // Notificar a cada abogado asignado
                    foreach ($request->lawyers as $lawyerId) {
                        $lawyer = Lawyer::find($lawyerId);
                        if ($lawyer && $lawyer->user) {
                            $lawyer->user->notify(
                                new \App\Notifications\NuevoAsistenteAsignado($assistant)
                            );
                        }
                    }

                    // Notificar al asistente sobre su primera asignación
                    if ($assistant->user) {
                        $primerAbogado = Lawyer::find($request->lawyers[0]);
                        if ($primerAbogado) {
                            $assistant->user->notify(
                                new \App\Notifications\AsistenteAsignado($primerAbogado)
                            );
                        }
                    }
                }

                DB::commit();

                $this->sendCredentials($validated['correo'], $user, $validated['numero_documento'], $assistant->id);

                return $this->successResponse(
                    $request,
                    'Asistente creado correctamente.',
                    ['assistant' => $assistant],
                    201,
                    'dashboard'
                );
            }

            throw new \Exception('Tipo de usuario no válido.');
        } catch (ValidationException $e) {
            DB::rollBack();

            $messages = [];
            if (isset($e->errors()['numero_documento'])) {
                $messages[] = "• El número de documento {$request->numero_documento} ya está registrado";
            }
            if (isset($e->errors()['correo'])) {
                $messages[] = "• El correo electrónico {$request->correo} ya está registrado";
            }

            return response()->json([
                'success' => false,
                'type' => 'duplicate',
                'message' => 'Datos duplicados',
                'duplicates' => $messages
            ], 422);
        }
    }
    /**
     * Actualizar abogado existente
     */
    public function update(Request $request, Lawyer $lawyer)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'tipo_documento' => 'required|string|max:50',
                'numero_documento' => 'required|string|max:50',
                'correo' => 'required|email|max:255',
                'telefono' => 'nullable|string|max:20',
                'especialidad' => 'nullable|string|max:255',
            ]);

            // Actualizar abogado
            $lawyer->update([
                'nombre' => trim($validated['nombre']),
                'apellido' => trim($validated['apellido']),
                'tipo_documento' => $validated['tipo_documento'],
                'numero_documento' => trim($validated['numero_documento']),
                'correo' => trim(strtolower($validated['correo'])),
                'telefono' => $validated['telefono'] ?? null,
                'especialidad' => $validated['especialidad'] ?? null,
            ]);


            // Actualizar usuario asociado
            if ($lawyer->user) {
                $lawyer->user->update([
                    'name' => trim($validated['nombre']) . ' ' . trim($validated['apellido']),
                    'email' => trim(strtolower($validated['correo'])),
                    'numero_documento' => trim($validated['numero_documento']),

                ]);
            }

            DB::commit();

            return $this->successResponse(
                $request,
                'Abogado actualizado correctamente.',
                ['lawyer' => $lawyer->fresh()->load('user')],
                200,
                'lawyers.index'
            );
        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->validationErrorResponse($request, $e);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar abogado', [
                'lawyer_id' => $lawyer->id,
                'message' => $e->getMessage()
            ]);

            return $this->errorResponse(
                $request,
                'Error al actualizar abogado',
                $e->getMessage()
            );
        }
    }

    /**
     * Eliminar abogado
     */
    public function destroy(Request $request, Lawyer $lawyer)
    {
        DB::beginTransaction();

        try {
            $lawyerName = $lawyer->nombre . ' ' . $lawyer->apellido;
            $lawyerId = $lawyer->id;

            // Eliminar usuario asociado
            if ($lawyer->user_id) {
                $user = User::find($lawyer->user_id);
                $user?->delete();
            }

            $lawyer->delete();
            DB::commit();

            Log::info('Abogado eliminado', [
                'lawyer_id' => $lawyerId,
                'name' => $lawyerName,
                'deleted_by' => auth()->email ?? 'unknown'
            ]);

            return $this->successResponse(
                $request,
                'Abogado eliminado exitosamente.',
                [],
                200,
                'dashboard'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar abogado', [
                'lawyer_id' => $lawyer->id,
                'message' => $e->getMessage()
            ]);

            return $this->errorResponse(
                $request,
                'Error al eliminar abogado',
                $e->getMessage()
            );
        }
    }

    /**
     * Eliminar asistente jurídico
     */
    public function destroyAsist(Request $request, Assistant $assistant)
    {
        DB::beginTransaction();

        try {
            $assistantName = $assistant->nombre . ' ' . $assistant->apellido;
            $assistantId = $assistant->id;

            // Eliminar usuario asociado (si existe)
            if ($assistant->user_id) {
                $user = User::find($assistant->user_id);
                $user?->delete();
            }

            // Si tiene relación con abogados y solo quieres quitar la relación:
            $assistant->lawyers()->detach(); // opcional pero recomendado

            $assistant->delete();
            DB::commit();

            Log::info('Asistente eliminado', [
                'assistant_id' => $assistantId,
                'name' => $assistantName,
                'deleted_by' => auth()->email ?? 'unknown'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Asistente eliminado exitosamente.',
                'assistant_id' => $assistantId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar asistente', [
                'assistant_id' => $assistant->id,
                'message' => $e->getMessage()

            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar asistente'
            ], 500);
        }
    }

    public function updateAssistant(Request $request, Assistant $assistant)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'tipo_documento' => 'required|string|max:50',
                'numero_documento' => 'required|string|max:50|unique:assistants,numero_documento,' . $assistant->id,
                'correo' => 'required|email|max:255|unique:assistants,correo,' . $assistant->id . '|unique:users,email,' . ($assistant->user_id ?? 'NULL'),
                'telefono' => 'nullable|string|max:20',
                'lawyers' => 'array',
                'lawyers.*' => 'exists:lawyers,id',
            ]);

            $assistant->update([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'tipo_documento' => $validated['tipo_documento'],
                'numero_documento' => $validated['numero_documento'],
                'correo' => strtolower($validated['correo']),
                'telefono' => $validated['telefono'] ?? null,
            ]);

            if ($assistant->user) {
                $assistant->user->update([
                    'name' => $validated['nombre'] . ' ' . $validated['apellido'],
                    'email' => strtolower($validated['correo']),
                    'numero_documento' => $validated['numero_documento'],
                ]);
            }

            // 🔔 DETECTAR NUEVOS ABOGADOS ASIGNADOS
            $abogadosAnteriores = $assistant->lawyers->pluck('id')->toArray();
            $abogadosNuevos = $request->lawyers ?? [];
            $abogadosAgregados = array_diff($abogadosNuevos, $abogadosAnteriores);

            $assistant->lawyers()->sync($abogadosNuevos);

            // 👇 AGREGAR TRY-CATCH PARA NOTIFICACIONES
            try {
                // Notificar solo a los nuevos abogados
                foreach ($abogadosAgregados as $lawyerId) {
                    $lawyer = Lawyer::find($lawyerId);
                    if ($lawyer && $lawyer->user) {
                        $lawyer->user->notify(
                            new \App\Notifications\NuevoAsistenteAsignado($assistant)
                        );
                    }
                }

                // Notificar al asistente si se agregaron nuevos abogados
                if (count($abogadosAgregados) > 0 && $assistant->user) {
                    $primerNuevoAbogado = Lawyer::find(array_values($abogadosAgregados)[0]); // 👈 array_values
                    if ($primerNuevoAbogado) {
                        $assistant->user->notify(
                            new \App\Notifications\AsistenteAsignado($primerNuevoAbogado)
                        );
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Error al enviar notificaciones de asignación', [
                    'assistant_id' => $assistant->id,
                    'error' => $e->getMessage()
                ]);
                // Continuar sin fallar la actualización
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asistente actualizado correctamente.',
                'assistant' => $assistant->fresh()->load('lawyers')
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();

            $messages = [];
            if (isset($e->errors()['numero_documento'])) {
                $messages[] = "• El número de documento {$request->numero_documento} ya está registrado";
            }
            if (isset($e->errors()['correo'])) {
                $messages[] = "• El correo electrónico {$request->correo} ya está registrado";
            }

            return response()->json([
                'success' => false,
                'type' => 'duplicate',
                'message' => 'Información duplicada detectada',
                'duplicates' => $messages
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar asistente', [
                'assistant_id' => $assistant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar asistente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar listado a PDF
     */
    public function exportPDF()
    {
        try {
            $lawyers = Lawyer::orderBy('nombre')->get();
            $logoPath = public_path('img/LogoInsti.png');

            $pdf = Pdf::loadView('exports.lawyers-pdf', compact('lawyers', 'logoPath'))
                ->setPaper('a4', 'portrait');

            return $pdf->download('listado_abogados_' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error al exportar PDF de abogados', [
                'message' => $e->getMessage()
            ]);

            return back()->with('error', 'Error al generar el PDF');
        }
    }

    // ========== MÉTODOS HELPERS ==========

    /**
     * Enviar credenciales por correo
     */
    private function sendCredentials(string $email, User $user, string $password, int $lawyerId): void
    {
        try {
            Mail::to($email)->send(new SendCredentialsToLawyer($user, $password));
        } catch (\Exception $e) {
            Log::warning('Error al enviar correo, pero abogado creado', [
                'lawyer_id' => $lawyerId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Respuesta exitosa genérica
     */
    private function successResponse(Request $request, string $message, array $data = [], int $status = 200, string $route = null)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(array_merge([
                'success' => true,
                'message' => $message
            ], $data), $status);
        }

        return redirect()->route($route ?? 'dashboard')->with('success', $message);
    }

    /**
     * Respuesta de error de validación
     */
    private function validationErrorResponse(Request $request, ValidationException $e)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        return back()->withErrors($e->errors())->withInput();
    }

    /**
     * Respuesta de error genérica
     */
    private function errorResponse(Request $request, string $message, string $error = null, int $status = 500)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => config('app.debug') ? $error : 'Error interno del servidor'
            ], $status);
        }

        return back()->with('error', $message . ($error ? ': ' . $error : ''))->withInput();
    }
}
