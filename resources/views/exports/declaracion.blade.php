<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Declaración Jurada - {{ $declaracion->usuario->nombre ?? 'Funcionario' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        .no-border td {
            border: none !important;
        }

        .section-title {
            font-weight: bold;
            text-align: left;
            padding: 6px;
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
        }

        .sub {
            text-align: left;
            font-weight: bold;
        }

        .signature {
            margin-top: 30px;
            text-align: center;
        }

        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #000;
            width: 60%;
            margin-left: auto;
            margin-right: auto;
        }

        .no-border {
            border: none !important;
        }
    </style>
</head>
<body>

    <!-- ENCABEZADO -->
    <table class="no-border">
        <tr>
            <td colspan="8" class="header">
                DECLARACIÓN JURADA DE HORARIO Y JORNADA DE TRABAJO<br>
                UNIVERSIDAD DE COSTA RICA
            </td>
        </tr>
    </table>

    <!-- DATOS PERSONALES -->
    <table>
        <tr>
            <td class="sub">Nombre completo:</td>
            <td colspan="3">{{ $declaracion->usuario->nombre }} {{ $declaracion->usuario->apellido }}</td>
            <td class="sub">Cédula:</td>
            <td>{{ $declaracion->usuario->cedula ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="sub">Unidad académica:</td>
            <td colspan="3">{{ $declaracion->unidad->nombre }}</td>
            <td class="sub">Correo electrónico:</td>
            <td>{{ $declaracion->usuario->correo }}</td>
        </tr>
        <tr>
            <td class="sub">Teléfono:</td>
            <td colspan="2">{{ $declaracion->usuario->telefono }}</td>
            <td class="sub">Rol:</td>
            <td colspan="2">{{ ucfirst($declaracion->usuario->rol) }}</td>
        </tr>
    </table>

    <!-- INFORMACIÓN DEL NOMBRAMIENTO -->
    <table>
        <tr>
            <th>Lugar de Trabajo</th>
            <th>Cargo o Categoría</th>
            <th>Jornada</th>
            <th>Vigencia Desde</th>
            <th>Vigencia Hasta</th>
        </tr>
        <tr>
            <td>{{ $declaracion->unidad->sede->nombre }}</td>
            <td>{{ $declaracion->cargo->nombre }}</td>
            <td>{{ $declaracion->cargo->jornada }}</td>
            <td>{{ $declaracion->fecha_desde }}</td>
            <td>{{ $declaracion->fecha_hasta }}</td>
        </tr>
    </table>

    <!-- HORARIO SEMANAL -->
    <table>
        <tr>
            <th>Día</th>
            <th>Hora inicio</th>
            <th>Hora fin</th>
            <th>Total horas</th>
        </tr>
        @foreach ($declaracion->horarios as $h)
        <tr>
            <td>{{ $h->dia }}</td>
            <td>{{ $h->hora_inicio }}</td>
            <td>{{ $h->hora_fin }}</td>
            <td>
                @php
                    $inicio = strtotime($h->hora_inicio);
                    $fin = strtotime($h->hora_fin);
                    echo number_format(($fin - $inicio) / 3600, 2);
                @endphp
            </td>
        </tr>
        @endforeach
        <tr>
            <td colspan="3" style="text-align:right;"><strong>Total semanal</strong></td>
            <td><strong>{{ $declaracion->horas_totales }}</strong></td>
        </tr>
    </table>

    <!-- DECLARACIÓN -->
    <table class="no-border">
        <tr>
            <td colspan="8" style="text-align:justify;">
                <br>
                Declaro bajo juramento que la información consignada en este documento es verídica y completa,
                y que conozco las sanciones establecidas en caso de falsedad o incumplimiento.
                <br><br>
            </td>
        </tr>
    </table>

    <!-- FIRMAS -->
    <div class="signature">
        <div class="signature-line"></div>
        <p>Firma del funcionario</p>

        <br><br>

        <div class="signature-line"></div>
        <p>Firma del jefe inmediato</p>
    </div>

</body>
</html>

