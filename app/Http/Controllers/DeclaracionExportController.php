<?php

namespace App\Http\Controllers;

use App\Models\{Declaracion, Documento};
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

class DeclaracionExportController extends Controller
{
    public function exportar($id)
    {
        $d = Declaracion::with(['usuario','unidad.sede','cargo','horarios'])->findOrFail($id);

        $tpl = storage_path('app/plantillas/declaracion_jurada.xlsx');
        if (!file_exists($tpl)) abort(500,'No se encontró la plantilla declaracion_jurada.xlsx');

        $spreadsheet = IOFactory::load($tpl);
        $sheet = $spreadsheet->getActiveSheet();

        // Mapeo (ajusta si tu plantilla usa otras celdas)
        $sheet->setCellValue('C4', $d->usuario->nombre.' '.$d->usuario->apellido);
        $sheet->setCellValue('F4', $d->usuario->correo);
        $sheet->setCellValue('C5', $d->unidad->nombre);
        $sheet->setCellValue('F5', $d->unidad->sede->nombre ?? '');
        $sheet->setCellValue('C6', $d->cargo->nombre);
        $sheet->setCellValue('F6', $d->cargo->jornada);
        $sheet->setCellValue('C7', $d->fecha_desde);
        $sheet->setCellValue('E7', $d->fecha_hasta);
        $sheet->setCellValue('F9', $d->horas_totales);

        // Horarios: desde fila 13 (ajusta a tu plantilla)
        $fila = 13;
        foreach ($d->horarios as $h) {
            $sheet->setCellValue("B{$fila}", $h->dia);
            $sheet->setCellValue("C{$fila}", $h->hora_inicio);
            $sheet->setCellValue("D{$fila}", $h->hora_fin);
            $fila++;
        }

        $nombre = 'Declaracion_'.Str::slug($d->usuario->nombre.' '.$d->usuario->apellido).'_'.$d->id_declaracion.'.xlsx';
        $ruta = storage_path("app/public/{$nombre}");
        (new Xlsx($spreadsheet))->save($ruta);

        // Guardar registro documento
        Documento::create([
            'id_declaracion'=>$d->id_declaracion,
            'archivo'=>"public/{$nombre}",
            'formato'=>'EXCEL',
            'fecha_generacion'=>now(),
        ]);

        return response()->download($ruta)->deleteFileAfterSend(true);
    }
}

