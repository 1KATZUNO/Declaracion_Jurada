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
                // Obtener jornada
                $jornadaTexto = '';
                if ($h->jornada) {
                    $tipo = $h->jornada->tipo ?? '';
                    $horas = $h->jornada->horas_por_semana ?? '';
                    if ($tipo && $horas) {
                        $jornadaTexto = $tipo . ' - ' . $horas . ' horas semanales';
                    } elseif ($tipo) {
                        $jornadaTexto = $tipo;
                    }
                }
                
                $grouped[$key] = [
                    'lugar' => $h->lugar ?? '',
                    'cargo' => $h->cargo ?? '',
                    'jornada' => $jornadaTexto,
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
        $d = Declaracion::with(['usuario','unidad.sede','cargo','horarios.jornada','horarios.cargo','formulario'])->findOrFail($id);

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

        // ======= CABECERA (FILA 3) =======
        // Nombre de la persona (B3:K3 ya fusionado en plantilla)
        $sheet->setCellValue('B3', 'Nombre de la persona funcionaria: ' . ($d->usuario->nombre ?? '') . ' ' . ($d->usuario->apellido ?? ''));
        
        // Identificación en P3:R3 (la plantilla tiene "Identificación:" en L3:O3)
        $sheet->setCellValue('P3', $identificacion ?? '');

        $sheet->getStyle('B3:R3')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('B3:R3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // ======= FILA 4 =======
        // Unidad (B4:K4 ya fusionado en plantilla)
        $sheet->setCellValue('B4', 'Unidad Académica o Administrativa: ' . ($d->unidad->nombre ?? ''));
        
        // Correo (L4:R4 ya fusionado en plantilla)
        $sheet->setCellValue('L4', 'Correo electrónico: ' . ($correo ?? ''));
        
        $sheet->getStyle('B4:R4')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('B4:R4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // ======= FILA 5 =======
        // B5:R5 ya debe estar fusionado en la plantilla
        $sheet->setCellValue('B5', 'A continuación declaro los horarios y jornadas convenidos con:');
        $sheet->getStyle('B5')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('B5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
        // La plantilla tiene datos de ejemplo en fila 9, vamos a empezar desde fila 10
        $baseFila = 10;
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

            // Obtener jornada del primer horario UCR
            $jornadaTexto = '';
            foreach ($byDay as $dia => $horariosDia) {
                if (!empty($horariosDia)) {
                    $primerHorario = $horariosDia[0];
                    if ($primerHorario->jornada) {
                        $tipo = $primerHorario->jornada->tipo ?? '';
                        $horas = $primerHorario->jornada->horas_por_semana ?? '';
                        if ($tipo && $horas) {
                            $jornadaTexto = $tipo . ' - ' . $horas . ' horas semanales';
                        } elseif ($tipo) {
                            $jornadaTexto = $tipo;
                        }
                        break;
                    }
                }
            }
            
            // Si no hay jornada en horarios, usar la del cargo
            if (!$jornadaTexto) {
                $jornadaTexto = $d->cargo->jornada ?? '';
            }

            // escribir fila por fila: la primera fila incluye B-F, las siguientes dejan B-F vacíos
            for ($i = 0; $i < $maxLines; $i++) {
                $fila = $baseFila + $i;
                if ($i === 0) {
                    $sheet->setCellValue("B{$fila}", $d->unidad->sede->nombre ?? '');
                    $sheet->setCellValue("C{$fila}", $d->cargo->nombre ?? '');
                    $sheet->setCellValue("D{$fila}", $jornadaTexto);
                    
                    // Obtener fechas de vigencia del primer horario UCR (no de la declaración)
                    $fechaDesde = '';
                    $fechaHasta = '';
                    
                    $primerHorarioUCR = $horariosUCR->first();
                    if ($primerHorarioUCR) {
                        if (!empty($primerHorarioUCR->desde)) {
                            try {
                                $fechaDesde = date('Y-m-d', strtotime($primerHorarioUCR->desde));
                            } catch (\Exception $e) {
                                $fechaDesde = $primerHorarioUCR->desde;
                            }
                        }
                        
                        if (!empty($primerHorarioUCR->hasta)) {
                            try {
                                $fechaHasta = date('Y-m-d', strtotime($primerHorarioUCR->hasta));
                            } catch (\Exception $e) {
                                $fechaHasta = $primerHorarioUCR->hasta;
                            }
                        }
                    }
                    
                    // Escribir en E10 y F10 para UCR
                    $sheet->setCellValue("E{$fila}", $fechaDesde);
                    $sheet->setCellValue("F{$fila}", $fechaHasta);
                    
                    // Aplicar estilo a las celdas de vigencia
                    $sheet->getStyle("E{$fila}:F{$fila}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("E{$fila}:F{$fila}")->getFont()->setSize(9);
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
        // La plantilla tiene headers en fila 17-18, empezar datos desde fila 19
        $fila = max(19, $nextFila); // garantizar que empiece en 19 o después de UCR si ocupó más espacio
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
                    
                    // Formatear fechas de vigencia para externos - más robusto
                    $desdeExt = '';
                    $hastaExt = '';
                    
                    if (!empty($grp['desde'])) {
                        try {
                            $desdeExt = date('Y-m-d', strtotime($grp['desde']));
                        } catch (\Exception $e) {
                            $desdeExt = $grp['desde'];
                        }
                    }
                    
                    if (!empty($grp['hasta'])) {
                        try {
                            $hastaExt = date('Y-m-d', strtotime($grp['hasta']));
                        } catch (\Exception $e) {
                            $hastaExt = $grp['hasta'];
                        }
                    }
                    
                    // Escribir en E19+ y F19+ para instituciones externas
                    $sheet->setCellValue("E{$r}", $desdeExt);
                    $sheet->setCellValue("F{$r}", $hastaExt);
                    
                    // Aplicar estilo a las celdas de vigencia
                    $sheet->getStyle("E{$r}:F{$r}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("E{$r}:F{$r}")->getFont()->setSize(9);
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

        // ======= OBSERVACIONES =======
        // La plantilla ya tiene "Observaciones:" en fila 32, solo actualizar el contenido en fila 33
        $sheet->setCellValue("B33", $d->observaciones_adicionales ?? 'Sin observaciones.');
        $sheet->getStyle("B33:R33")->getAlignment()
            ->setWrapText(true)
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
      
        // ======= NO AGREGAR FIRMAS NI NOTA AL PIE - YA ESTÁN EN LA PLANTILLA =======
        // La plantilla ya incluye las firmas en las filas 41+ con el formato correcto

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



