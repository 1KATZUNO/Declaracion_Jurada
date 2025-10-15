<?php

namespace App\Exports;

use App\Models\Declaracion;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DeclaracionExport implements FromView
{
    protected $declaracion;

    public function __construct(Declaracion $declaracion)
    {
        $this->declaracion = $declaracion;
    }

    public function view(): View
    {
        return view('exports.declaracion', [
            'declaracion' => $this->declaracion
        ]);
    }
}
