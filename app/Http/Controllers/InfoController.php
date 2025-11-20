<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function accesibilidad()
    {
        return view('info.accesibilidad');
    }

    public function ayuda(Request $request)
    {
        $searchTerm = $request->input('search', '');
        
        // Manual de usuario organizado por secciones
        $manual = [
            [
                'titulo' => 'Inicio de Sesión',
                'icono' => '🔐',
                'contenido' => 'Para acceder al sistema, ingrese su correo electrónico y contraseña institucional. Si olvidó su contraseña, puede restablecerla desde el enlace "¿Olvidó su contraseña?".',
                'pasos' => [
                    'Ingrese a la página principal del sistema',
                    'Escriba su correo electrónico institucional',
                    'Ingrese su contraseña',
                    'Haga clic en "Iniciar Sesión"'
                ]
            ],
            [
                'titulo' => 'Crear una Declaración Jurada',
                'icono' => '📝',
                'contenido' => 'Las declaraciones juradas permiten registrar sus actividades laborales dentro y fuera de la UCR. Es importante completar todos los campos requeridos.',
                'pasos' => [
                    'Vaya al menú "Declaraciones"',
                    'Haga clic en "Nueva declaración"',
                    'Complete los datos personales y de la unidad académica',
                    'Agregue los horarios de UCR con sus respectivos cargos',
                    'Si aplica, agregue horarios de instituciones externas',
                    'Revise la información y haga clic en "Guardar"'
                ]
            ],
            [
                'titulo' => 'Gestión de Horarios',
                'icono' => '⏰',
                'contenido' => 'Los horarios deben cumplir con las restricciones establecidas: horario UCR de 7:00 AM a 9:00 PM (excepto 12:01-12:59 PM), sin traslapes, y con buffer de 1 hora entre actividades.',
                'pasos' => [
                    'Seleccione el día de la semana',
                    'Ingrese la hora de inicio y fin',
                    'Asegúrese de no tener conflictos con otros horarios',
                    'Los horarios deben completar las horas de la jornada asignada'
                ]
            ],
            [
                'titulo' => 'Exportar Declaraciones',
                'icono' => '📄',
                'contenido' => 'Puede exportar sus declaraciones en formato Excel o PDF para mantener un registro personal o para presentación.',
                'pasos' => [
                    'Vaya a la lista de declaraciones',
                    'Busque la declaración que desea exportar',
                    'Haga clic en el botón "Excel" o "PDF"',
                    'El archivo se descargará automáticamente'
                ]
            ],
            [
                'titulo' => 'Actualizar Perfil',
                'icono' => '👤',
                'contenido' => 'Puede actualizar su información personal, incluyendo su foto de perfil, desde el menú de usuario en la esquina superior derecha.',
                'pasos' => [
                    'Haga clic en su nombre en la esquina superior derecha',
                    'Actualice su nombre, apellido o foto de perfil',
                    'Haga clic en "Guardar"',
                    'Los cambios se reflejarán inmediatamente'
                ]
            ],
            [
                'titulo' => 'Notificaciones',
                'icono' => '🔔',
                'contenido' => 'El sistema le enviará notificaciones sobre eventos importantes como declaraciones generadas, recordatorios, o cambios en su cuenta.',
                'pasos' => [
                    'Revise el ícono de notificaciones en la barra superior',
                    'Haga clic para ver las notificaciones pendientes',
                    'Las notificaciones no leídas aparecen con un badge numérico'
                ]
            ],
            [
                'titulo' => 'Comentarios y Observaciones',
                'icono' => '💬',
                'contenido' => 'Puede agregar comentarios a sus declaraciones para aclaraciones o información adicional que considere relevante.',
                'pasos' => [
                    'Abra una declaración',
                    'Busque la sección de comentarios',
                    'Escriba su comentario u observación',
                    'Guarde los cambios'
                ]
            ],
            [
                'titulo' => 'Cambiar Contraseña',
                'icono' => '🔑',
                'contenido' => 'Por seguridad, se recomienda cambiar su contraseña periódicamente. Asegúrese de usar una contraseña segura.',
                'pasos' => [
                    'Vaya a su perfil de usuario',
                    'Seleccione "Cambiar contraseña"',
                    'Ingrese su contraseña actual',
                    'Escriba y confirme su nueva contraseña',
                    'Guarde los cambios'
                ]
            ]
        ];

        // Filtrar por búsqueda si existe
        if (!empty($searchTerm)) {
            $manual = array_filter($manual, function($item) use ($searchTerm) {
                $searchLower = mb_strtolower($searchTerm);
                return mb_stripos(mb_strtolower($item['titulo']), $searchLower) !== false ||
                       mb_stripos(mb_strtolower($item['contenido']), $searchLower) !== false;
            });
        }

        return view('info.ayuda', [
            'manual' => $manual,
            'searchTerm' => $searchTerm
        ]);
    }

    public function acercaDe()
    {
        return view('info.acerca-de');
    }
}
