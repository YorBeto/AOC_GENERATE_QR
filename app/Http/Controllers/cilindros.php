<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cilindro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class cilindros extends Controller
{
    public function index()
    {
        $cilindros = Cilindro::all();

        return response()->json([
            'status' => 'success',
            'data' => $cilindros
        ],200);
    }


public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'numero_serie' => 'required|string|max:25',
        'fecha_recepcion' => 'required|date',
        'fecha_registro' => 'required|date',
        'url_ficha' => 'nullable|file|mimes:pdf|max:5120', 
        'QR_code' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    $urlPublica = null;

    if ($request->hasFile('url_ficha')) {
        try {
            $archivo = $request->file('url_ficha');
            \Log::info('Archivo recibido:', [
                'name' => $archivo->getClientOriginalName(),
                'size' => $archivo->getSize(),
                'mime' => $archivo->getMimeType()
            ]);

            $rutaCarpeta = 'Fichas/';
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            
            // Intenta subir el archivo
            $path = Storage::disk('s3')->putFileAs(
                $rutaCarpeta,
                $archivo,
                $nombreArchivo,
            );
            
            \Log::info('Resultado de putFileAs:', ['path' => $path]);

            // Verifica si el archivo existe
            $exists = Storage::disk('s3')->exists($rutaCarpeta . $nombreArchivo);
            \Log::info('Archivo existe en S3:', ['exists' => $exists]);

            if (!$exists) {
                throw new \Exception("El archivo no se subió correctamente. Path: " . $rutaCarpeta . $nombreArchivo);
            }
            
            $urlPublica = Storage::disk('s3')->url($rutaCarpeta . $nombreArchivo);
            \Log::info('URL generada:', ['url' => $urlPublica]);
            
        } catch (\Exception $e) {
            \Log::error('Error completo al subir archivo:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error al subir el archivo PDF',
                'error' => $e->getMessage(),
                'details' => 'Ver logs para más información'
            ], 500);
        }
    }



    $cilindro = new Cilindro();
    $cilindro->numero_serie = $request->numero_serie;
    $cilindro->fecha_recepcion = $request->fecha_recepcion;
    $cilindro->fecha_registro = $request->fecha_registro;
    $cilindro->url_ficha = $urlPublica;
    $cilindro->QR_code = $request->QR_code;
    $cilindro->estado = 'activo';
    $cilindro->save();

    return response()->json([
        'status' => 'success',
        'data' => $cilindro
    ], 201);
}


    public function show($numero_serie)
    {
        $cilindro = Cilindro::where('numero_serie', $numero_serie)->first();

        if (!$cilindro) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cilindro not found'
            ], 404);
        }

        

        return response()->json([
            'status' => 'success',
            'data' => [
                'numero_serie' => $cilindro->numero_serie,
                'fecha_recepcion' => $cilindro->fecha_recepcion,
                'fecha_registro' => $cilindro->fecha_registro,
                'url_ficha' => $cilindro->url_ficha,
                'QR_code' => $cilindro->QR_code,
                'estado' => $cilindro->estado
            ]
        ],200);
    }

            public function updateQr(Request $req, $numero)
        {
            $cil = Cilindro::where('numero_serie', $numero)->firstOrFail();
            $cil->QR_code = $req->input('QR_code');
            $cil->save();
            return response()->json(['status'=>'success'],200);
        }

}
