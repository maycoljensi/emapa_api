<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeudaController extends Controller
{
    public function consultar(Request $request)
    {
        $idsucursal = $request->input('idsucursal');
        $nroinscripcion = $request->input('numero_suministro');

        if (!$idsucursal || !$nroinscripcion) {
            return response()->json([
                'error' => true,
                'mensaje' => 'idsucursal y numero_suministro son requeridos.'
            ], 400);
        }

        try {
            // Consumir la API real
            $response = Http::post('http://127.0.0.1:8001/api/obtener-deuda', [
                'idsucursal' => $idsucursal,
                'textbusqueda' => $nroinscripcion
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Error consultando el sistema interno.'
                ], 502);
            }

            $total = $response->body(); // porque tu API final devuelve solo un string con el total

            return response()->json([
                'error' => false,
                'mensaje' => 'Consulta exitosa',
                'total_deuda' => $total
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Excepción: ' . $e->getMessage()
            ], 500);
        }
    }
}
