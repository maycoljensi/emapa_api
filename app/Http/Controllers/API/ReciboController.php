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

        // Llamada a la API externa
        $cliente = new Client();
        try {
            $response = $cliente->request('GET', 'http://127.0.0.1:8001/api/consultar_usuario', [
                'query' => [
                    'idsucursal' => 1,
                    'textbusqueda' => $suministro
                ]
            ]);

            $data = json_decode($response->getBody(), true); // decodifica como array

            if (empty($data)) {
                return $this->personalizado(null, false, "No existe el suministro");
            }

            // Consulta local: ¿Está ya registrado en recibo_digital?
            $clienteLocal = DB::select("SELECT * FROM recibo_digital WHERE numero_suministro = ?", [$suministro]);

            $yaRegistrado = !empty($clienteLocal);
            $estadoServicio = $data[0]['estado_servicio'] ?? null;
            $mostrarModal = ($estadoServicio === "Activo" && !$yaRegistrado);

            return $this->personalizado($data, true, $yaRegistrado ? "Ya está registrado" : "Existe pero no registrado", $mostrarModal);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al consultar el suministro',
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
            'datos' => $data,
            'mostrar_modal' => $mostrarModal
        ]);
    }
}
