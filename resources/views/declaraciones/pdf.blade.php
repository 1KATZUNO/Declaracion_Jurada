<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Declaración Jurada</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9px;
            color: #000;
            padding: 0;
            margin: 0;
        }
        @page {
            margin: 0;
            size: letter;
        }
        .page-container {
            margin: 0;
            position: relative;
            min-height: 279mm;
        }
        .content-wrapper {
            padding: 0 10px 10px 10px;
        }
        .header {
            background-color: #4DBEEE;
            padding: 15px;
            margin: 0;
            position: relative;
            min-height: 90px;
            border-bottom: 1px solid #000;
        }
        .header-logo-left {
            position: absolute;
            left: 15px;
            top: 15px;
        }
        .header-logo-left img {
            width: 80px;
            height: 30px;
        }
        .header-logo-right {
            position: absolute;
            right: 15px;
            top: 15px;
        }
        .header-logo-right img {
            width: 60px;
            height: auto;
        }
        .header-content {
            text-align: center;
            padding-top: 25px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            margin-top: 10px;
        }
        .info-grid td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 8px;
        }
        .info-label {
            font-weight: bold;
        }
        .section-text {
            border: 1px solid #000;
            padding: 4px;
            margin-bottom: 10px;
            margin-top: 5px;
            font-size: 8px;
            font-weight: bold;
        }
        table.horarios {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 7px;
        }
        table.horarios th,
        table.horarios td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
        }
        table.horarios th {
            background-color: #fff;
            font-weight: bold;
            font-size: 7px;
        }
        .col-lugar { width: 10%; text-align: left; }
        .col-cargo { width: 10%; text-align: left; }
        .col-jornada { width: 7%; }
        .col-vigencia { width: 8%; }
        .col-dia { width: 4%; }
        .observaciones {
            border: 1px solid #000;
            padding: 4px;
            margin-top: 10px;
            margin-bottom: 20px;
            min-height: 40px;
            font-size: 7px;
        }
        .firmas-container {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 10px;
            width: 100%;
        }
        .firmas {
            display: table;
            width: 100%;
            margin-top: 0;
            page-break-inside: avoid;
        }
        .firma-col {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 0 10px;
        }
        .firma-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 3px;
            font-size: 7px;
        }
        .footer-note {
            font-size: 6px;
            text-align: justify;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="page-container">
    <div class="header">
        <div class="header-logo-left">
            <img src="{{ public_path('imagenes/ucr-logo.png') }}" alt="UCR Logo">
        </div>
        <div class="header-logo-right">
            <img src="{{ public_path('imagenes/orh-logo.png') }}" alt="ORH Logo">
        </div>
        <div class="header-content">
            <h1>DECLARACION JURADA DE HORARIO Y JORNADA DE TRABAJO</h1>
        </div>
    </div>

    <div class="content-wrapper">
    <table class="info-grid">
        <tr>
            <td class="info-label" style="width: 20%;">Nombre de la persona funcionaria:</td>
            <td style="width: 50%;">{{ $declaracion->usuario->nombre ?? '' }} {{ $declaracion->usuario->apellido ?? '' }}</td>
            <td class="info-label" style="width: 15%;">Identificación:</td>
            <td style="width: 15%;">{{ $identificacion ?? '' }}</td>
        </tr>
        <tr>
            <td class="info-label">Unidad Académica o Administrativa: {{ $declaracion->unidad->sede->nombre ?? 'Sede Guanacaste' }}</td>
            <td colspan="2" class="info-label">Correo electrónico:</td>
            <td>{{ $correo ?? '' }}</td>
        </tr>
    </table>

    <div class="section-text">
        A continuación declaro los horarios y jornadas convenidos con:
    </div>

    @if(count($byDayUCR) > 0)
        <table class="horarios">
            <thead>
                <tr>
                    <th colspan="17" style="background-color: #fff; font-weight: bold; text-align: center;">
                        UNIVERSIDAD DE COSTA RICA (sea como docente y/o administrativo)
                    </th>
                </tr>
                <tr>
                    <th class="col-lugar" rowspan="2">Lugar de Trabajo</th>
                    <th class="col-cargo" rowspan="2">Cargo o Categoría</th>
                    <th class="col-jornada" rowspan="2">Jornada de Trabajo</th>
                    <th class="col-vigencia" colspan="2">Vigencia del nombramiento</th>
                    <th class="col-dia" colspan="2">Lunes</th>
                    <th class="col-dia" colspan="2">Martes</th>
                    <th class="col-dia" colspan="2">Miércoles</th>
                    <th class="col-dia" colspan="2">Jueves</th>
                    <th class="col-dia" colspan="2">Viernes</th>
                    <th class="col-dia" colspan="2">Sábado</th>
                </tr>
                <tr>
                    <th style="font-size: 6px;">Desde</th>
                    <th style="font-size: 6px;">Hasta</th>
                    <th style="font-size: 6px;">De</th>
                    <th style="font-size: 6px;">A</th>
                    <th style="font-size: 6px;">De</th>
                    <th style="font-size: 6px;">A</th>
                    <th style="font-size: 6px;">De</th>
                    <th style="font-size: 6px;">A</th>
                    <th style="font-size: 6px;">De</th>
                    <th style="font-size: 6px;">A</th>
                    <th style="font-size: 6px;">De</th>
                    <th style="font-size: 6px;">A</th>
                    <th style="font-size: 6px;">De</th>
                    <th style="font-size: 6px;">A</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                    $maxLines = 0;
                    foreach ($dias as $dia) {
                        $count = isset($byDayUCR[$dia]) ? count($byDayUCR[$dia]) : 0;
                        if ($count > $maxLines) $maxLines = $count;
                    }
                    $maxLines = max(1, $maxLines);
                    
                    // Obtener jornada del primer horario UCR si existe
                    $jornadaTexto = '';
                    $fechaDesde = '';
                    $fechaHasta = '';
                    
                    foreach ($byDayUCR as $dia => $horariosDia) {
                        if (!empty($horariosDia)) {
                            $primerHorario = $horariosDia[0];
                            
                            // Intentar obtener jornada de la relación con tabla jornada
                            if (!$jornadaTexto && $primerHorario->jornada) {
                                $tipo = $primerHorario->jornada->tipo ?? '';
                                $horas = $primerHorario->jornada->horas_por_semana ?? '';
                                if ($tipo && $horas) {
                                    $jornadaTexto = $tipo . ' - ' . $horas . ' horas semanales';
                                } elseif ($tipo) {
                                    $jornadaTexto = $tipo;
                                }
                            }
                            
                            // Si el horario tiene cargo, intentar obtener jornada del cargo
                            if (!$jornadaTexto && $primerHorario->cargo) {
                                $jornadaTexto = $primerHorario->cargo->jornada ?? '';
                            }
                            
                            // Intentar obtener fechas de vigencia
                            if (!$fechaDesde) $fechaDesde = $primerHorario->desde ?? '';
                            if (!$fechaHasta) $fechaHasta = $primerHorario->hasta ?? '';
                            
                            if ($jornadaTexto) {
                                break;
                            }
                        }
                    }
                    
                    // Si no hay datos de horarios, usar los de la declaración
                    if (!$jornadaTexto && $declaracion->cargo) {
                        $jornadaTexto = $declaracion->cargo->jornada ?? '';
                    }
                    if (!$fechaDesde) {
                        $fechaDesde = $declaracion->fecha_desde ?? '';
                    }
                    if (!$fechaHasta) {
                        $fechaHasta = $declaracion->fecha_hasta ?? '';
                    }
                @endphp

                @for($i = 0; $i < $maxLines; $i++)
                    <tr>
                        @if($i === 0)
                            <td class="col-lugar">{{ $declaracion->unidad->nombre ?? '' }}</td>
                            <td class="col-cargo">{{ $declaracion->cargo->nombre ?? '' }}</td>
                            <td class="col-jornada">{{ $jornadaTexto }}</td>
                            <td class="col-vigencia">{{ $fechaDesde }}</td>
                            <td class="col-vigencia">{{ $fechaHasta }}</td>
                        @else
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif

                        @foreach($dias as $dia)
                            @if(isset($byDayUCR[$dia][$i]))
                                <td class="col-dia">{{ $byDayUCR[$dia][$i]->hora_inicio ?? '' }}</td>
                                <td class="col-dia">{{ $byDayUCR[$dia][$i]->hora_fin ?? '' }}</td>
                            @else
                                <td class="col-dia"></td>
                                <td class="col-dia"></td>
                            @endif
                        @endforeach
                    </tr>
                @endfor
                <!-- Filas vacías adicionales -->
                <tr>
                    <td></td><td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            </tbody>
        </table>
    @endif

    @if(count($groupedExternos) > 0)
        <table class="horarios">
            <thead>
                <tr>
                    <th colspan="17" style="background-color: #fff; font-weight: bold; text-align: center;">
                        OTRAS INSTITUCIONES PÚBLICAS, PRIVADAS Y FUNDACIONES
                    </th>
                </tr>
                <tr>
                    <th class="col-lugar" rowspan="2">Lugar de Trabajo</th>
                    <th class="col-cargo" rowspan="2">Cargo o Categoría</th>
                    <th class="col-jornada" rowspan="2">Jornada de Trabajo</th>
                    <th class="col-vigencia" colspan="2">Vigencia del nombramiento</th>
                    <th class="col-dia" colspan="2">Lunes</th>
                    <th class="col-dia" colspan="2">Martes</th>
                    <th class="col-dia" colspan="2">Miércoles</th>
                    <th class="col-dia" colspan="2">Jueves</th>
                    <th class="col-dia" colspan="2">Viernes</th>
                    <th class="col-dia" colspan="2">Sábado</th>
                </tr>
                <tr>
                    <th style="font-size: 6px;">Desde</th>
                    <th style="font-size: 6px;">Hasta</th>
                    <th style="font-size: 6px;">De</th>
                    <th style="font-size: 6px;">A</th>
                    <th style="font-size: 6px;">De</th>
                    <th style="font-size: 6px;">A</th>
                    <th style="font-size: 6px;">De</th>
                    <th style="font-size: 6px;">A</th>
                    <th style="font-size: 6px;">De</th>
                    <th style="font-size: 6px;">A</th>
                    <th style="font-size: 6px;">De</th>
                    <th style="font-size: 6px;">A</th>
                    <th style="font-size: 6px;">De</th>
                    <th style="font-size: 6px;">A</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedExternos as $grp)
                    @php
                        $byDayExt = $grp['byDay'];
                        $maxLinesExt = 0;
                        foreach ($dias as $dia) {
                            $count = isset($byDayExt[$dia]) ? count($byDayExt[$dia]) : 0;
                            if ($count > $maxLinesExt) $maxLinesExt = $count;
                        }
                        $maxLinesExt = max(1, $maxLinesExt);
                    @endphp

                    @for($i = 0; $i < $maxLinesExt; $i++)
                        <tr>
                            @if($i === 0)
                                <td class="col-lugar">{{ $grp['lugar'] }}</td>
                                <td class="col-cargo">{{ $grp['cargo'] }}</td>
                                <td class="col-jornada">{{ $grp['jornada'] }}</td>
                                <td class="col-vigencia">{{ $grp['desde'] }}</td>
                                <td class="col-vigencia">{{ $grp['hasta'] }}</td>
                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif

                            @foreach($dias as $dia)
                                @if(isset($byDayExt[$dia][$i]))
                                    <td class="col-dia">{{ $byDayExt[$dia][$i]->hora_inicio ?? '' }}</td>
                                    <td class="col-dia">{{ $byDayExt[$dia][$i]->hora_fin ?? '' }}</td>
                                @else
                                    <td class="col-dia"></td>
                                    <td class="col-dia"></td>
                                @endif
                            @endforeach
                        </tr>
                    @endfor
                @endforeach
                <!-- Filas vacías adicionales -->
                <tr>
                    <td></td><td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="observaciones">
        <strong>Observaciones:</strong><br>
        {{ $declaracion->observaciones_adicionales ?? 'Sin observaciones.' }}
    </div>
    </div>

    <div class="firmas-container">
    <div class="firmas">
        <div class="firma-col">
            <div class="firma-line">
                Firma de la persona funcionaria<br>Declarante
            </div>
        </div>
        <div class="firma-col">
            <div class="firma-line">
                Firma superior jerárquico Unidad
            </div>
        </div>
        <div class="firma-col">
            <div class="firma-line">
                Director(a) del Departamento
            </div>
        </div>
        <div class="firma-col">
            <div class="firma-line">
                Coordinador(a)<br>de Carrera
            </div>
        </div>
    </div>

    <div class="footer-note">
        *Se informa que el tratamiento de los datos proporcionados se rige de conformidad con la ley 8968 Ley de Protección de la Persona frente al tratamiento de sus datos personales.
    </div>
    </div>
    </div>
    </div>
</body>
</html>
