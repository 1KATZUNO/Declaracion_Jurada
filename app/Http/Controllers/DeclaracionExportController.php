<?php

namespace App\Http\Controllers;

use App\Models\{Declaracion, Documento};
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class DeclaracionExportController extends Controller
{
    public function exportarPdf($id)
    {
        $d = Declaracion::with(['usuario','unidad.sede','cargo','horarios.jornada','horarios.cargo','formulario'])->findOrFail($id);

        // Obtener identificación
        $identificacion = null;
        if ($d->usuario) {
            $attrs = $d->usuario->getAttributes();
            foreach (['identificacion','cedula','numero_identificacion','numero_cedula','dni','ci'] as $key) {
                if (!empty($attrs[$key])) { $identificacion = trim($attrs[$key]); break; }
            }
            if (empty($identificacion)) {
                foreach (['identificacion','cedula','numero_identificacion','numero_cedula','dni','ci'] as $key) {
                    $val = $d->usuario->{$key} ?? null;
                    if (!empty($val)) { $identificacion = trim($val); break; }
                }
            }
        }

        $correo = $d->usuario->correo ?? $d->usuario->email ?? '';

        // Agrupar horarios UCR por día
        $horariosUCR = $d->horarios->where('tipo', 'ucr');
        $byDayUCR = [];
        foreach ($horariosUCR as $h) {
            if (empty($h->dia)) continue;
            $byDayUCR[$h->dia][] = $h;
        }

        // Agrupar horarios externos por lugar
        $horariosExternos = $d->horarios->where('tipo', 'externo');
        $grouped = [];
        foreach ($horariosExternos as $h) {
            $key = $h->lugar ?? ('_ext_' . $h->id_horario);
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'lugar' => $h->lugar ?? '',
                    'cargo' => $h->cargo ?? '',
                    'jornada' => $h->jornada ?? '',
                    'desde' => $h->desde ?? '',
                    'hasta' => $h->hasta ?? '',
                    'horarios' => [],
                ];
            }
            $grouped[$key]['horarios'][] = $h;
        }

        // Agrupar horarios externos por día dentro de cada grupo
        foreach ($grouped as &$grp) {
            $byDayExt = [];
            foreach ($grp['horarios'] as $h) {
                if (empty($h->dia)) continue;
                $byDayExt[$h->dia][] = $h;
            }
            $grp['byDay'] = $byDayExt;
        }

        $pdf = Pdf::loadView('declaraciones.pdf', [
            'declaracion' => $d,
            'identificacion' => $identificacion,
            'correo' => $correo,
            'byDayUCR' => $byDayUCR,
            'groupedExternos' => $grouped,
        ]);

        $nombre = 'Declaracion_' . Str::slug(($d->usuario->nombre ?? '') . ' ' . ($d->usuario->apellido ?? '')) . '_' . $d->id_declaracion . '.pdf';
        
        // Guardar en storage
        $dir = storage_path('app/public');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $ruta = $dir . DIRECTORY_SEPARATOR . $nombre;
        $pdf->save($ruta);

        // Registrar documento
        Documento::create([
            'id_declaracion' => $d->id_declaracion,
            'archivo' => "public/{$nombre}",
            'formato' => 'PDF',
            'fecha_generacion' => now(),
        ]);

        // Generar el PDF y forzar descarga con diálogo "Guardar como"
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $nombre . '"');
    }

    public function exportar($id)
    {
        $d = Declaracion::with(['usuario','unidad.sede','cargo','horarios','formulario'])->findOrFail($id);

        // --- Fallback robusto para identificación y correo ---
        $identificacion = null;
        if ($d->usuario) {
            // primero intentar sobre los atributos crudos (evita depender solo de accessors)
            $attrs = $d->usuario->getAttributes();
            foreach (['identificacion','cedula','numero_identificacion','numero_cedula','dni','ci'] as $key) {
                if (!empty($attrs[$key])) { $identificacion = trim($attrs[$key]); break; }
            }
            // si sigue vacío, probar accessors / propiedades dinámicas como último recurso
            if (empty($identificacion)) {
                foreach (['identificacion','cedula','numero_identificacion','numero_cedula','dni','ci'] as $key) {
                    $val = $d->usuario->{$key} ?? null;
                    if (!empty($val)) { $identificacion = trim($val); break; }
                }
            }
        }

        $correo = $d->usuario->correo ?? $d->usuario->email ?? '';

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

        $sheet->setCellValue('B3', 'Nombre de la persona funcionaria: ' . ($d->usuario->nombre ?? '') . ' ' . ($d->usuario->apellido ?? ''));
        $sheet->setCellValue('L3', 'Identificación: ' . ($identificacion ?? ''));
        $sheet->setCellValue('P3', 'Teléfono: ' . ($d->usuario->telefono ?? ''));

        $sheet->getStyle('B3:R3')->getFont()->setBold(true);

        // ======= FILA 4 =======
        $sheet->mergeCells('B4:K4');
        $sheet->mergeCells('L4:R4');
        $sheet->setCellValue('B4', 'Unidad Académica o Administrativa: ' . ($d->unidad->nombre ?? ''));
        $sheet->setCellValue('L4', 'Correo electrónico: ' . ($correo ?? ''));
        $sheet->getStyle('B4:R4')->getFont()->setBold(true);

        // ======= FILA 5 =======
        $sheet->mergeCells('B5:R5');
        $sheet->setCellValue('B5', 'A continuación declaro los horarios y jornadas convenidos con:');
        $sheet->getStyle('B5:R5')->getFont()->setBold(true);

        // ======= FILA 6: Mostrar identificación también en lugar visible =======
        $sheet->mergeCells('B6:R6');
        $sheet->setCellValue('B6', 'Identificación / Cédula: ' . ($identificacion ?? ''));
        $sheet->getStyle('B6:R6')->getFont()->setItalic(true);

        // Map de columnas por día (inicio/fin pares)
        $cols = [
            'Lunes' => ['G','H'],
            'Martes' => ['I','J'],
            'Miércoles' => ['K','L'],
            'Jueves' => ['M','N'],
            'Viernes' => ['O','P'],
            'Sábado' => ['Q','R'],
        ];

        // ======= HORARIOS UNIVERSIDAD (una o varias filas según intervalos) =======
        $baseFila = 9;
        $horariosUCR = $d->horarios->where('tipo', 'ucr');

        if ($horariosUCR->isNotEmpty()) {
            // agrupar intervalos por día, manteniendo orden de inserción
            $byDay = [];
            foreach ($horariosUCR as $h) {
                if (empty($h->dia)) continue;
                $byDay[$h->dia][] = $h;
            }

            // calcular máximo de intervalos entre los días (número de filas necesarias)
            $maxLines = 0;
            foreach ($cols as $dia => $pair) {
                $count = !empty($byDay[$dia]) ? count($byDay[$dia]) : 0;
                if ($count > $maxLines) $maxLines = $count;
            }
            $maxLines = max(1, $maxLines);

            // escribir fila por fila: la primera fila incluye B-F, las siguientes dejan B-F vacíos
            for ($i = 0; $i < $maxLines; $i++) {
                $fila = $baseFila + $i;
                if ($i === 0) {
                    $sheet->setCellValue("B{$fila}", $d->unidad->sede->nombre ?? '');
                    $sheet->setCellValue("C{$fila}", $d->cargo->nombre ?? '');
                    $sheet->setCellValue("D{$fila}", $d->cargo->jornada ?? '');
                    $sheet->setCellValue("E{$fila}", $d->fecha_desde);
                    $sheet->setCellValue("F{$fila}", $d->fecha_hasta);
                } else {
                    // limpiar B-F para filas secundarias (asegura que no se muestre nada)
                    $sheet->setCellValue("B{$fila}", '');
                    $sheet->setCellValue("C{$fila}", '');
                    $sheet->setCellValue("D{$fila}", '');
                    $sheet->setCellValue("E{$fila}", '');
                    $sheet->setCellValue("F{$fila}", '');
                }

                // para cada día, si existe el intervalo i, escribir inicio y fin en sus columnas respectivas
                foreach ($cols as $dia => $pair) {
                    $startCell = $pair[0] . $fila;
                    $endCell   = $pair[1] . $fila;
                    if (!empty($byDay[$dia]) && isset($byDay[$dia][$i])) {
                        $h = $byDay[$dia][$i];
                        $sheet->setCellValue($startCell, $h->hora_inicio ?? '');
                        $sheet->setCellValue($endCell, $h->hora_fin ?? '');
                    } else {
                        $sheet->setCellValue($startCell, '');
                        $sheet->setCellValue($endCell, '');
                    }
                    // wrap y alineación por seguridad
                    $sheet->getStyle($startCell)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
                    $sheet->getStyle($endCell)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
                }

                // ajustar alto de fila (multiplicador probado)
                $sheet->getRowDimension($fila)->setRowHeight(18);
            }

            // mover el puntero de fila inicial para las externas después de las filas UCR
            $nextFila = $baseFila + $maxLines;
        } else {
            $nextFila = 9;
        }

        // ======= HORARIOS OTRAS INSTITUCIONES =======
        $fila = max(16, $nextFila); // garantizar que empiece en 16 o después de UCR si ocupó más espacio
        $horariosExternos = $d->horarios->where('tipo', 'externo');

        // Agrupar por 'lugar' (institución). Si no hay 'lugar', agrupar por índice único.
        $grouped = [];
        foreach ($horariosExternos as $h) {
            $key = $h->lugar ?? ('_ext_' . $h->id_horario);
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'lugar' => $h->lugar ?? '',
                    'cargo' => $h->cargo ?? '',
                    'jornada' => $h->jornada ?? '',
                    'desde' => $h->desde ?? '',
                    'hasta' => $h->hasta ?? '',
                    'horarios' => [],
                ];
            }
            $grouped[$key]['horarios'][] = $h;
        }

        foreach ($grouped as $grp) {
            // agrupar por día dentro del grupo
            $byDayExt = [];
            foreach ($grp['horarios'] as $h) {
                if (empty($h->dia)) continue;
                $byDayExt[$h->dia][] = $h;
            }

            // calcular cuántas filas necesita este grupo
            $maxLinesExt = 0;
            foreach ($cols as $dia => $pair) {
                $count = !empty($byDayExt[$dia]) ? count($byDayExt[$dia]) : 0;
                if ($count > $maxLinesExt) $maxLinesExt = $count;
            }
            $maxLinesExt = max(1, $maxLinesExt);

            // escribir filas del grupo; primera fila con B-F, siguientes con B-F vacíos
            for ($i = 0; $i < $maxLinesExt; $i++) {
                $r = $fila + $i;
                if ($i === 0) {
                    $sheet->setCellValue("B{$r}", $grp['lugar']);
                    $sheet->setCellValue("C{$r}", $grp['cargo']);
                    $sheet->setCellValue("D{$r}", $grp['jornada']);
                    $sheet->setCellValue("E{$r}", $grp['desde']);
                    $sheet->setCellValue("F{$r}", $grp['hasta']);
                } else {
                    $sheet->setCellValue("B{$r}", '');
                    $sheet->setCellValue("C{$r}", '');
                    $sheet->setCellValue("D{$r}", '');
                    $sheet->setCellValue("E{$r}", '');
                    $sheet->setCellValue("F{$r}", '');
                }

                foreach ($cols as $dia => $pair) {
                    $startCell = $pair[0] . $r;
                    $endCell   = $pair[1] . $r;
                    if (!empty($byDayExt[$dia]) && isset($byDayExt[$dia][$i])) {
                        $h = $byDayExt[$dia][$i];
                        $sheet->setCellValue($startCell, $h->hora_inicio ?? '');
                        $sheet->setCellValue($endCell, $h->hora_fin ?? '');
                    } else {
                        $sheet->setCellValue($startCell, '');
                        $sheet->setCellValue($endCell, '');
                    }
                    $sheet->getStyle($startCell)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
                    $sheet->getStyle($endCell)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
                }

                $sheet->getRowDimension($r)->setRowHeight(18);
            }

            // actualizar fila siguiente para el próximo grupo
            $fila += $maxLinesExt;
            // dejar una fila vacía entre grupos opcionalmente
            $fila++;
        }

        // ======= AJUSTES VISUALES =======
        $sheet->getStyle('B3:R6')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B3:R6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B3:R6')->getFont()->getColor()->setRGB('000000');

      
                // ======= ESPACIO PARA FIRMAS IMPRESAS =======
        $lastRow = $sheet->getHighestRow() + 3; // deja un poco de espacio al final del documento

        // Firma del funcionario (profesor)
        $sheet->mergeCells("B{$lastRow}:H" . ($lastRow + 1));
        $sheet->setCellValue("B{$lastRow}", 'Firma del funcionario: _______________________________');
        $sheet->getStyle("B{$lastRow}:H" . ($lastRow + 1))->getFont()->setBold(true);

        // Firma del encargado o validador (unidad académica o administrativo)
        $sheet->mergeCells("J{$lastRow}:R" . ($lastRow + 1));
        $sheet->setCellValue("J{$lastRow}", 'Firma del encargado: _______________________________');
        $sheet->getStyle("J{$lastRow}:R" . ($lastRow + 1))->getFont()->setBold(true);

        // Centrar visualmente las firmas
        $sheet->getStyle("B{$lastRow}:R" . ($lastRow + 1))
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // ======= EXPORTAR Y GUARDAR =======
        $nombre = 'Declaracion_' . Str::slug(($d->usuario->nombre ?? '') . ' ' . ($d->usuario->apellido ?? '')) . '_' . $d->id_declaracion . '.xlsx';
        $dir = storage_path('app/public');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ruta = $dir . DIRECTORY_SEPARATOR . $nombre;
        (new Xlsx($spreadsheet))->save($ruta);

        Documento::create([
            'id_declaracion' => $d->id_declaracion,
            'archivo' => "public/{$nombre}",
            'formato' => 'EXCEL',
            'fecha_generacion' => now(),
        ]);

        // No eliminar el archivo tras el envío para que el registro Documento apunte a un archivo existente.
        return response()->download($ruta, $nombre)->deleteFileAfterSend(false);
    }
}



