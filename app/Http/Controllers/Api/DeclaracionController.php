<?php



namespace App\Http\Controllers\Api;

use App\Notifications\DeclaracionGenerada; //

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Declaracion;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;

class DeclaracionController extends Controller
{
    /**
     * Listar todas las declaraciones del usuario autenticado
     */
    public function index(Request $req)
    {
        return Declaracion::where('user_id', $req->user()->id)->get();
    }

    /**
     * Crear nueva declaración
     */
   public function store(Request $req)
{
    $req->validate([
        'formulario_id' => 'nullable|integer',
        'data' => 'required|array'
    ]);

    $decl = Declaracion::create([
        'user_id' => $req->user()->id,
        'formulario_id' => $req->formulario_id,
        'data' => $req->data,
        'estado' => 'generada'
    ]);

    // 🔔 Notificar al usuario
    $req->user()->notify(new DeclaracionGenerada($decl));

    return response()->json($decl, 201);
}
    /**
     * Mostrar una declaración específica
     */
    public function show(Declaracion $declaracion)
    {
        if ($declaracion->user_id !== auth()->id()) {
            return response()->json(['message'=>'No autorizado'], 403);
        }
        return $declaracion;
    }

    /**
     * Actualizar datos de la declaración
     */
    public function update(Request $req, Declaracion $declaracion)
    {
        if ($declaracion->user_id !== auth()->id()) {
            return response()->json(['message'=>'No autorizado'], 403);
        }

        $req->validate(['data'=>'required|array']);
        $declaracion->update(['data'=>$req->data]);

        return $declaracion;
    }

    /**
     * Eliminar declaración
     */
    public function destroy(Declaracion $declaracion)
    {
        if ($declaracion->user_id !== auth()->id()) {
            return response()->json(['message'=>'No autorizado'], 403);
        }

        $declaracion->delete();
        return response()->json(['message'=>'Eliminada']);
    }

    /**
     * Exportar declaración a Excel usando plantilla
     */
    public function export(Declaracion $declaracion)
    {
        if ($declaracion->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Ruta de la plantilla
        $templatePath = storage_path('app/templates/2025-II DJ ejemplo.xlsx');
        if (!file_exists($templatePath)) {
            return response()->json(['message'=>'Plantilla no encontrada'], 500);
        }

        // Cargar plantilla
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $user = $declaracion->user;
        $data = $declaracion->data ?? [];

        // ====== Datos personales ======
        $sheet->setCellValue('C3', $user->name ?? '');
        $sheet->setCellValue('M3', $user->cedula ?? '');
        $sheet->setCellValue('Q3', $user->telefono ?? '');
        $sheet->setCellValue('M4', $user->email ?? '');

        // ====== Workplaces (máx. 2 filas en plantilla: 9 y 16) ======
        $workplaces = $data['workplaces'] ?? [];
        $rows = [9, 16];
        $dayCols = [
            'monday'    => ['G','H'],
            'tuesday'   => ['I','J'],
            'wednesday' => ['K','L'],
            'thursday'  => ['M','N'],
            'friday'    => ['O','P'],
            'saturday'  => ['Q','R'],
        ];

        foreach ($workplaces as $i => $wp) {
            if (!isset($rows[$i])) break;
            $r = $rows[$i];

            $sheet->setCellValue("B{$r}", $wp['lugar'] ?? '');
            $sheet->setCellValue("C{$r}", $wp['cargo'] ?? '');
            $sheet->setCellValue("D{$r}", $wp['jornada'] ?? '');
            $sheet->setCellValue("E{$r}", $wp['vigencia_desde'] ?? '');
            $sheet->setCellValue("F{$r}", $wp['vigencia_hasta'] ?? '');

            $schedule = $wp['schedule'] ?? [];
            foreach ($dayCols as $day => [$colFrom, $colTo]) {
                $sheet->setCellValue("{$colFrom}{$r}", $schedule[$day]['from'] ?? '');
                $sheet->setCellValue("{$colTo}{$r}", $schedule[$day]['to'] ?? '');
            }
        }

        // Observaciones
        if (!empty($data['observaciones'])) {
            $sheet->setCellValue('C21', $data['observaciones']);
        }

        // ====== Guardar archivo en storage ======
        $outputDir = storage_path('app/declaraciones');
        if (!file_exists($outputDir)) mkdir($outputDir, 0755, true);

        $fileName = "declaracion_{$declaracion->id}.xlsx";
        $outputPath = $outputDir . DIRECTORY_SEPARATOR . $fileName;

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);

        // Actualizar en BD
        $declaracion->update(['archivo' => "declaraciones/{$fileName}"]);

        // Descargar
        return response()->download($outputPath, $fileName);
    }
}
