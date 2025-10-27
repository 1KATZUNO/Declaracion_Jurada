<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Usuario;

class UsuarioCreado extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $passwordPlain;

    public function __construct(Usuario $usuario, $passwordPlain)
    {
        $this->usuario = $usuario;
        $this->passwordPlain = $passwordPlain;
    }

    public function build()
    {
        return $this->subject('Cuenta creada en el sistema')
                    ->view('emails.usuario_creado');
    }
}

