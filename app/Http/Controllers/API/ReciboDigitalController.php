<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReciboDigitalController extends Controller
{
    public function registrarAfiliacion(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'nombres' => 'required|string|max:100',
            'apellidos_paternos' => 'required|string|max:100',
            'apellidos_maternos' => 'required|string|max:100',
            'dni' => 'required|string|max:20',
            'soy' => 'required|string|max:50',
            'numero_suministro' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'telefono_celular' => 'required|string|max:20',
            'telefono_fijo' => 'nullable|string|max:20',
            'email' => 'required|email|max:150',
        ]);

        try {
            DB::table('recibo_digital')->insert([
                'fecha' => $validated['fecha'],
                'nombres' => $validated['nombres'],
                'apellidos_paternos' => $validated['apellidos_paternos'],
                'apellidos_maternos' => $validated['apellidos_maternos'],
                'dni' => $validated['dni'],
                'soy' => $validated['soy'],
                'numero_suministro' => $validated['numero_suministro'],
                'direccion' => $validated['direccion'],
                'telefono_celular' => $validated['telefono_celular'],
                'telefono_fijo' => $validated['telefono_fijo'],
                'email' => $validated['email'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['mensaje' => 'Afiliación registrada con éxito'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al registrar la afiliación',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}
