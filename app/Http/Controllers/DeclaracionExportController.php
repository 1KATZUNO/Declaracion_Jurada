<?php

namespace App\Http\Controllers;

use App\Models\{Declaracion, Documento};
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
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
        $telefono = $d->usuario->telefono ?? '';

        // Debug para verificar los valores
        // dd(['nombre' => $nombreCompleto, 'id' => $identificacion, 'tel' => $telefono, 'correo' => $correo]);

        $tpl = storage_path('app/plantillas/declaracion_jurada.xlsx');
        if (!file_exists($tpl)) abort(500,'No se encontró la plantilla declaracion_jurada.xlsx');

        $spreadsheet = IOFactory::load($tpl);
        $sheet = $spreadsheet->getActiveSheet();

        // ======= CONFIGURACIÓN DE COLUMNAS =======
        // Ajustar anchos de columnas para mejor visualización
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(12);
        // Columnas de días de la semana
        foreach(['G','H','I','J','K','L','M','N','O','P','Q','R'] as $col) {
            $sheet->getColumnDimension($col)->setWidth(10);
        }

        // ======= ESTILO GENERAL =======
        $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Century Gothic')
            ->setSize(11)
            ->getColor()->setRGB('000000');

        // ======= LLENAR SOLO LOS VALORES EN LA PLANTILLA EXISTENTE =======
        
        // La plantilla YA tiene los textos, solo necesitamos llenar los valores en las celdas específicas
        
        $nombreCompleto = ($d->usuario->nombre ?? '') . ' ' . ($d->usuario->apellido ?? '');
        
        // Llenar según las posiciones exactas especificadas
        
        // Fila 3: Primera fila de datos
        $sheet->setCellValue('B3', 'Nombre de la persona funcionaria: ' . $nombreCompleto);
        $sheet->setCellValue('L3', 'Identificación: ' . ($identificacion ?? ''));
        $sheet->setCellValue('P3', 'Teléfono: ' . ($telefono ?? ''));

        // Fila 4: Segunda fila de datos  
        $sheet->setCellValue('B4', 'Unidad Académica o Administrativa: ' . ($d->unidad->nombre ?? ''));
        $sheet->setCellValue('L4', 'Correo electrónico: ' . ($correo ?? ''));

        // Ajustar altura para las filas de datos
        $sheet->getRowDimension('3')->setRowHeight(20);  // Fila de datos personales
        $sheet->getRowDimension('4')->setRowHeight(20);  // Fila de datos institucionales
        $sheet->getRowDimension('5')->setRowHeight(20);  // Fila "A continuación declaro..."

        // Map de columnas por día (inicio/fin pares) - versión que funcionaba
        $cols = [
            'Lunes' => ['G','H'],
            'Martes' => ['I','J'],
            'Miércoles' => ['K','L'],
            'Jueves' => ['M','N'],
            'Viernes' => ['O','P'],
            'Sábado' => ['Q','R'],
        ];

        // ======= HORARIOS UNIVERSIDAD =======
        // Basándome en la imagen, los datos UCR van en la primera fila de datos de la tabla UCR
        $baseFila = 9;  // Ajustar según la tabla UCR de la plantilla
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
            
            // Si no hay jornada en horarios, usar la del cargo - asegurar que sea texto
            if (!$jornadaTexto && $d->cargo) {
                if ($d->cargo->jornada) {
                    // Si jornada es un objeto, obtener el texto correcto
                    if (is_object($d->cargo->jornada)) {
                        $tipo = $d->cargo->jornada->tipo ?? '';
                        $horas = $d->cargo->jornada->horas_por_semana ?? '';
                        if ($tipo && $horas) {
                            $jornadaTexto = $tipo . ' - ' . $horas . ' horas semanales';
                        } elseif ($tipo) {
                            $jornadaTexto = $tipo;
                        }
                    } else {
                        $jornadaTexto = (string) $d->cargo->jornada;
                    }
                }
            }

            // escribir fila por fila: la primera fila incluye B-F, las siguientes dejan B-F vacíos
            for ($i = 0; $i < $maxLines; $i++) {
                $fila = $baseFila + $i;
                if ($i === 0) {
                    // Volver a la estructura que funcionaba
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
                    
                    // Escribir en E y F para UCR (como estaba funcionando)
                    $sheet->setCellValue("E{$fila}", $fechaDesde);
                    $sheet->setCellValue("F{$fila}", $fechaHasta);
                    
                    // Aplicar estilo a las celdas de vigencia
                    $sheet->getStyle("E{$fila}:F{$fila}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("E{$fila}:F{$fila}")->getFont()->setSize(9);
                } else {
                    // limpiar B-F para filas secundarias (como estaba funcionando)
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

                // Ajustar altura automáticamente para que el texto sea visible
                $sheet->getRowDimension($fila)->setRowHeight(-1); // Auto height
            }

            // mover el puntero de fila inicial para las externas después de las filas UCR
            $nextFila = $baseFila + $maxLines;
        } else {
            $nextFila = 9;
        }

        // ======= HORARIOS OTRAS INSTITUCIONES =======
        // Basándome en la imagen, las otras instituciones empiezan en fila 16
        $fila = max(16, $nextFila); // Empezar en fila 16 según la plantilla
        $horariosExternos = $d->horarios->where('tipo', 'externo');

        // Agrupar por 'lugar' (institución). Si no hay 'lugar', agrupar por índice único.
        $grouped = [];
        foreach ($horariosExternos as $h) {
            $key = $h->lugar ?? ('_ext_' . $h->id_horario);
            if (!isset($grouped[$key])) {
                // Procesar jornada correctamente para externos
                $jornadaTexto = '';
                if ($h->jornada) {
                    if (is_object($h->jornada)) {
                        $tipo = $h->jornada->tipo ?? '';
                        $horas = $h->jornada->horas_por_semana ?? '';
                        if ($tipo && $horas) {
                            $jornadaTexto = $tipo . ' - ' . $horas . ' horas semanales';
                        } elseif ($tipo) {
                            $jornadaTexto = $tipo;
                        }
                    } else {
                        $jornadaTexto = (string) $h->jornada;
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
                    
                    // Escribir en E y F para instituciones externas (como funcionaba)
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

                // Ajustar altura automáticamente para que el texto sea visible
                $sheet->getRowDimension($r)->setRowHeight(-1); // Auto height
            }

            // actualizar fila siguiente para el próximo grupo
            $fila += $maxLinesExt;
            // dejar una fila vacía entre grupos opcionalmente
            $fila++;
        }

        // ======= AJUSTES VISUALES =======
        $sheet->getStyle('B3:R5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B3:R5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B3:R5')->getFont()->getColor()->setRGB('000000');

        // ======= OBSERVACIONES =======
        // C21: Centrado verticalmente, texto alineado a la izquierda
        $sheet->setCellValue("C21", $d->observaciones_adicionales ?? 'Sin observaciones.');
        $sheet->getStyle("C21:R25")->getAlignment()
            ->setWrapText(true)
            ->setVertical(Alignment::VERTICAL_CENTER)  // Centrado en altura
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);  // Texto desde la izquierda
      
        // ======= ALINEACIÓN DE FIRMAS EXISTENTES EN LA PLANTILLA =======
        // Solo alinear las celdas de líneas de firma que ya existen: B24, E24, K24, O24
        
        // Línea de firma 1: B24
        $sheet->getStyle("B24")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
            
        // Línea de firma 2: E24  
        $sheet->getStyle("E24")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
            
        // Línea de firma 3: K24
        $sheet->getStyle("K24")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
            
        // Línea de firma 4: O24
        $sheet->getStyle("O24")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

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



