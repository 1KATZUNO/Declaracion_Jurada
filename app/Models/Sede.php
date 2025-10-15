namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasFactory;

    protected $table = 'sede';
    protected $primaryKey = 'id_sede';

    protected $fillable = ['nombre', 'ubicacion'];

    public function unidades()
    {
        return $this->hasMany(UnidadAcademica::class, 'id_sede', 'id_sede');
    }
}
