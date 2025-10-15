<?php

namespace App\Http\Controllers;

use App\Models\{Declaracion, Documento};
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Str;

class DeclaracionExportController extends Controller
{
    public function exportar($id)
    {
        $d = Declaracion::with(['usuario','unidad.sede','cargo','horarios','formulario'])->findOrFail($id);

        $tpl = storage_path('app/plantillas/declaracion_jurada.xlsx');
        if (!file_exists($tpl)) abort(500,'No se encontró la plantilla declaracion_jurada.xlsx');

        $spreadsheet = IOFactory::load($tpl);
        $sheet = $spreadsheet->getActiveSheet();

        // ======= ESTILO GENERAL =======
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Century Gothic')
            ->setSize(11)
            ->getColor()->setRGB('000000');

        // ======= CABECERA =======
        $sheet->mergeCells('B3:K3');
        $sheet->mergeCells('L3:O3');
        $sheet->mergeCells('P3:R3');

        $sheet->setCellValue('B3', 'Nombre de la persona funcionaria: ' . $d->usuario->nombre . ' ' . $d->usuario->apellido);
        $sheet->setCellValue('L3', 'Identificación: ' . ($d->usuario->identificacion ?? ''));
        $sheet->setCellValue('P3', 'Teléfono: ' . ($d->usuario->telefono ?? ''));

        $sheet->getStyle('B3:R3')->getFont()->setBold(true);

        // ======= FILA 4 =======
        $sheet->mergeCells('B4:K4');
        $sheet->mergeCells('L4:R4');
        $sheet->setCellValue('B4', 'Unidad Académica o Administrativa: ' . ($d->unidad->nombre ?? ''));
        $sheet->setCellValue('L4', 'Correo electrónico: ' . ($d->usuario->correo ?? ''));
        $sheet->getStyle('B4:R4')->getFont()->setBold(true);

        // ======= FILA 5 =======
        $sheet->mergeCells('B5:R5');
        $sheet->setCellValue('B5', 'A continuación declaro los horarios y jornadas convenidos con:');
        $sheet->getStyle('B5:R5')->getFont()->setBold(true);

        // ======= HORARIOS UNIVERSIDAD =======
        $fila = 9;
        $horariosUCR = $d->horarios->where('tipo', 'ucr');
        foreach ($horariosUCR as $h) {
            $sheet->setCellValue("B{$fila}", $d->unidad->sede->nombre ?? '');
            $sheet->setCellValue("C{$fila}", $d->cargo->nombre ?? '');
            $sheet->setCellValue("D{$fila}", $d->cargo->jornada ?? '');
            $sheet->setCellValue("E{$fila}", $d->fecha_desde);
            $sheet->setCellValue("F{$fila}", $d->fecha_hasta);

            // Días
            $sheet->setCellValue("G{$fila}", $h->dia === 'Lunes' ? $h->hora_inicio : '');
            $sheet->setCellValue("H{$fila}", $h->dia === 'Lunes' ? $h->hora_fin : '');
            $sheet->setCellValue("I{$fila}", $h->dia === 'Martes' ? $h->hora_inicio : '');
            $sheet->setCellValue("J{$fila}", $h->dia === 'Martes' ? $h->hora_fin : '');
            $sheet->setCellValue("K{$fila}", $h->dia === 'Miércoles' ? $h->hora_inicio : '');
            $sheet->setCellValue("L{$fila}", $h->dia === 'Miércoles' ? $h->hora_fin : '');
            $sheet->setCellValue("M{$fila}", $h->dia === 'Jueves' ? $h->hora_inicio : '');
            $sheet->setCellValue("N{$fila}", $h->dia === 'Jueves' ? $h->hora_fin : '');
            $sheet->setCellValue("O{$fila}", $h->dia === 'Viernes' ? $h->hora_inicio : '');
            $sheet->setCellValue("P{$fila}", $h->dia === 'Viernes' ? $h->hora_fin : '');
            $sheet->setCellValue("Q{$fila}", $h->dia === 'Sábado' ? $h->hora_inicio : '');
            $sheet->setCellValue("R{$fila}", $h->dia === 'Sábado' ? $h->hora_fin : '');

            $fila++;
        }

        // ======= HORARIOS OTRAS INSTITUCIONES =======
        $fila = 16;
        $horariosExternos = $d->horarios->where('tipo', 'externo');
        foreach ($horariosExternos as $h) {
            $sheet->setCellValue("B{$fila}", $h->lugar ?? '');
            $sheet->setCellValue("C{$fila}", $h->cargo ?? '');
            $sheet->setCellValue("D{$fila}", $h->jornada ?? '');
            $sheet->setCellValue("E{$fila}", $h->desde ?? '');
            $sheet->setCellValue("F{$fila}", $h->hasta ?? '');

            $sheet->setCellValue("G{$fila}", $h->dia === 'Lunes' ? $h->hora_inicio : '');
            $sheet->setCellValue("H{$fila}", $h->dia === 'Lunes' ? $h->hora_fin : '');
            $sheet->setCellValue("I{$fila}", $h->dia === 'Martes' ? $h->hora_inicio : '');
            $sheet->setCellValue("J{$fila}", $h->dia === 'Martes' ? $h->hora_fin : '');
            $sheet->setCellValue("K{$fila}", $h->dia === 'Miércoles' ? $h->hora_inicio : '');
            $sheet->setCellValue("L{$fila}", $h->dia === 'Miércoles' ? $h->hora_fin : '');
            $sheet->setCellValue("M{$fila}", $h->dia === 'Jueves' ? $h->hora_inicio : '');
            $sheet->setCellValue("N{$fila}", $h->dia === 'Jueves' ? $h->hora_fin : '');
            $sheet->setCellValue("O{$fila}", $h->dia === 'Viernes' ? $h->hora_inicio : '');
            $sheet->setCellValue("P{$fila}", $h->dia === 'Viernes' ? $h->hora_fin : '');
            $sheet->setCellValue("Q{$fila}", $h->dia === 'Sábado' ? $h->hora_inicio : '');
            $sheet->setCellValue("R{$fila}", $h->dia === 'Sábado' ? $h->hora_fin : '');

            $fila++;
        }

        // ======= AJUSTES VISUALES =======
        $sheet->getStyle('B3:R5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B3:R5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B3:R5')->getFont()->getColor()->setRGB('000000');

        // ======= EXPORTAR Y GUARDAR =======
        $nombre = 'Declaracion_' . Str::slug($d->usuario->nombre . ' ' . $d->usuario->apellido) . '_' . $d->id_declaracion . '.xlsx';
        $ruta = storage_path("app/public/{$nombre}");
        (new Xlsx($spreadsheet))->save($ruta);

        Documento::create([
            'id_declaracion' => $d->id_declaracion,
            'archivo' => "public/{$nombre}",
            'formato' => 'EXCEL',
            'fecha_generacion' => now(),
        ]);

        return response()->download($ruta)->deleteFileAfterSend(true);
    }
}


