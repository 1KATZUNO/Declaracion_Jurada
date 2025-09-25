use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeclaracionController;
use App\Http\Controllers\Api\FormularioController; // faltaba este import

// Registro / Login / Logout (sin middleware)
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/logout', [AuthController::class,'logout'])->middleware('auth:sanctum');

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function(Request $r){ return $r->user(); });

    Route::apiResource('declaraciones', DeclaracionController::class);
    Route::post('declaraciones/{declaracion}/export', [DeclaracionController::class,'export']);

    Route::get('/notificaciones', function(Request $req){
        return $req->user()->notifications;
    });

    Route::apiResource('formularios', FormularioController::class);
});


