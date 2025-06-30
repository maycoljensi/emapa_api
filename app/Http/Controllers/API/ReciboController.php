<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class ReciboController extends Controller
{
    public function getReciboDuplicado(Request $request)
    {
        $suministro = $request->input('numero_suministro');

        if (!$suministro) {
            return response()->json(['message' => 'Número de suministro requerido'], 400);
        }

        // Paso 1: Llamada a la API externa que valida si el suministro existe
        $cliente = new Client();
        try {
            $response = $cliente->request('GET', 'http://127.0.0.1:8001/api/consultar_usuario', [
                'query' => [
                    'idsucursal' => 1,
                    'textbusqueda' => $suministro
                ]
            ]);

            $resultado = (string) $response->getBody(); // devuelve '1' o '0'

            if ($resultado === '0') {
                return $this->personalizado(null, false, "No existe el suministro");
            }

            // Paso 2: Consulta local si ya está registrado en recibo_digital
            $clienteLocal = DB::select("SELECT * FROM recibo_digital WHERE numero_suministro = ?", [$suministro]);

            $yaRegistrado = !empty($clienteLocal);
            $mostrarModal = !$yaRegistrado; // Si no está registrado, mostrar formulario

            return $this->personalizado(
                ['numero_suministro' => $suministro],
                true,
                $yaRegistrado ? "Ya está registrado" : "Existe pero no se encuentra registrado al recibo digital",
                $mostrarModal
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al consultar el suministro no pasa al postgres',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    // Respuesta personalizada
    private function personalizado($data, $estado, $mensaje = '', $mostrarModal = false)
    {
        return response()->json([
            'estado' => $estado,
            'mensaje' => $mensaje,
            'mostrar_modal' => $mostrarModal
        ]);
    }
}
