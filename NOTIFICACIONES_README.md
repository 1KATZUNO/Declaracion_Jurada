# Sistema de Notificaciones - Declaraciones Juradas UCR

## 📋 Resumen de Implementación

Se ha implementado exitosamente un sistema completo de notificaciones para el módulo de "Declaraciones Juradas" con notificaciones en tiempo real, actualizaciones automáticas y gestión completa del ciclo de vida de las notificaciones.

---

## ✅ Características Principales

### 🔔 Notificaciones en Tiempo Real
- **Actualización automática cada 1 segundo** del badge de notificaciones
- **Badge visual** en la campana mostrando el número de notificaciones no leídas
- **Sin necesidad de recargar la página** para ver nuevas notificaciones
- **Dropdown interactivo** con vista previa de notificaciones recientes

### 📊 Gestión Completa de Notificaciones
- **Vista de listado** con toda la información descriptiva
- **Vista individual** con detalles completos de cada notificación
- **Marcado automático como leída** al abrir una notificación
- **Fecha y hora de lectura** registrada automáticamente
- **Estados visuales**: "Leída" / "No leída" con colores distintivos
- **Tipos de notificación**: Crear, Editar, Eliminar, Exportar con íconos y colores

### 📧 Notificaciones por Email
- Sistema SMTP de Gmail configurado
- Mensajes personalizados con información detallada
- Formato profesional con saludo y firma UCR

---

## 🚀 Componentes Implementados

#### 1. **NotificacionService** - Servicio Centralizado
- **Ubicación**: `app/Services/NotificacionService.php`
- **Función**: Maneja toda la lógica de notificaciones de forma centralizada
- **Métodos implementados**:
  - `crearNotificacion()` - Método base para crear notificaciones
  - `notificarCrearDeclaracion()` - Notificación al crear declaración
  - `notificarEditarDeclaracion()` - Notificación al editar declaración
  - `notificarEliminarDeclaracion()` - Notificación al eliminar declaración
  - `notificarExportarDeclaracion()` - Notificación al exportar (PDF/Excel)
  - `notificarVencimientoProximo()` - Recordatorios de vencimiento
  - `obtenerNoLeidasPorUsuario()` - Obtener notificaciones no leídas
  - `marcarTodasComoLeidas()` - Marcar como leídas
  - `contarNoLeidas()` - Contar notificaciones pendientes

#### 2. **Integración en Controladores**

##### DeclaracionController
- **Archivo**: `app/Http/Controllers/DeclaracionController.php`
- **Integraciones**:
  - `store()` - Notificación al crear nueva declaración
  - `update()` - Notificación al actualizar declaración existente
  - `destroy()` - Notificación al eliminar declaración

##### DeclaracionExportController
- **Archivo**: `app/Http/Controllers/DeclaracionExportController.php`
- **Integraciones**:
  - `exportarPdf()` - Notificación después de generar PDF
  - `exportar()` - Notificación después de generar Excel

#### 3. **Modelo de Notificaciones Mejorado**
- **Archivo**: `app/Models/Notificacion.php`
- **Nuevos campos agregados**:
  - `titulo` - Título de la notificación
  - `tipo` - Tipo de notificación (crear, editar, eliminar, exportar, vencimiento)
  - `id_declaracion` - Relación con declaración específica
  - `leida` - Estado de lectura (boolean)

- **Constantes definidas**:
  ```php
  const TIPO_CREAR = 'crear';
  const TIPO_EDITAR = 'editar';
  const TIPO_ELIMINAR = 'eliminar';
  const TIPO_EXPORTAR = 'exportar';
  const TIPO_VENCIMIENTO = 'vencimiento';
  ```

#### 4. **Comando de Recordatorios Automáticos** 🎯
- **Archivo**: `app/Console/Commands/EnviarRecordatoriosVencimiento.php`
- **Comando**: `php artisan notificaciones:recordatorios-vencimiento`
- **Opciones**: `--dias=7` (configurable, por defecto 7 días)
- **Programación**: Configurado para ejecutarse diariamente a las 8:00 AM
- **Función Mejorada**: 
  - ✅ Busca declaraciones con `fecha_hasta` = hoy + X días
  - ✅ Solo envía recordatorios para declaraciones próximas a vencer realmente
  - ✅ Evita duplicados (no reenvía en 6 horas)
  - ✅ Incluye fecha exacta de vencimiento en el mensaje

#### 5. **Configuración de Tareas Programadas**
- **Archivo**: `app/Console/Kernel.php`
- **Configuración Principal**: `$schedule->command('notificaciones:recordatorios-vencimiento')->dailyAt('08:00');`
- **Lógica Inteligente**: 
  - 🔍 Ejecuta diariamente pero solo envía cuando hay declaraciones que vencen
  - 📅 Compara `fecha_hasta` de declaraciones con la fecha objetivo
  - ⏰ **NO** envía recordatorios a todos los usuarios todos los días
- **Configuraciones Opcionales**:
  ```php
  // Recordatorios múltiples (opcional):
  $schedule->command('notificaciones:recordatorios-vencimiento --dias=3')->dailyAt('09:00'); // 3 días antes
  $schedule->command('notificaciones:recordatorios-vencimiento --dias=1')->dailyAt('10:00'); // 1 día antes
  ```

#### 6. **Migración de Base de Datos**
- **Archivo**: `database/migrations/2025_11_11_202302_add_fields_to_notificacion_table.php`
- **Estado**: ✅ Ejecutada exitosamente
- **Campos agregados**:
  ```sql
  titulo VARCHAR(255)
  tipo VARCHAR(50)
  id_declaracion BIGINT UNSIGNED (Foreign Key)
  leida BOOLEAN DEFAULT FALSE
  ```

### 🔧 Configuración del Entorno (.env)

Para que el sistema de notificaciones funcione correctamente, asegúrate de tener la siguiente configuración en tu archivo `.env`:

```env
# Configuración de Correo (Gmail SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=declaracionesjuradasucr@gmail.com
MAIL_PASSWORD="cgww tyqx wbzn syyu"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=declaracionesjuradasucr@gmail.com
MAIL_FROM_NAME="Declaraciones Juradas UCR"

# Configuración de Base de Datos
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/a/tu/database.sqlite

# Configuración de la Aplicación
APP_NAME="Declaraciones Juradas UCR"
APP_ENV=local
APP_KEY=base64:tu-clave-generada
APP_DEBUG=true
APP_URL=http://localhost

# Zona Horaria
APP_TIMEZONE=America/Costa_Rica
```

**Notas Importantes:**
- La contraseña de Gmail es una **contraseña de aplicación**, no la contraseña normal de la cuenta
- Para generar una contraseña de aplicación: [Google App Passwords](https://myaccount.google.com/apppasswords)
- Asegúrate de habilitar la autenticación de dos factores en Gmail primero

---

### 📧 Tipos de Notificaciones

#### 1. **Notificaciones en el Sistema** (Badge en Campana)
- **Ubicación**: Campana en la barra de navegación superior
- **Actualización**: Cada 1 segundo automáticamente
- **Contenido**:
  - Contador de notificaciones no leídas
  - Vista previa en dropdown
  - Acceso directo a cada notificación
  - Animación visual cuando hay nuevas notificaciones

#### 2. **Notificaciones en Base de Datos**
- **Tabla**: `notificacion`
- **Campos principales**:
  - `titulo`: Título descriptivo de la notificación
  - `mensaje`: Mensaje completo y detallado
  - `tipo`: Tipo de acción (crear, editar, eliminar, exportar)
  - `leida`: Estado de lectura (boolean)
  - `fecha_lectura`: Fecha y hora en que se leyó
  - `id_declaracion`: Relación con la declaración específica
  - `estado`: Estado de envío (enviada, pendiente, error)

#### 3. **Notificaciones por Email**
- Utiliza Gmail SMTP configurado
- Formato profesional con saludo personalizado
- Incluye toda la información relevante de la acción
- Enviadas de forma asíncrona para no bloquear el sistema

---

### 🎨 Interfaz de Usuario

#### **Vista de Listado** (`/notificaciones`)
- **Tabla completa** con todas las notificaciones del usuario
- **Columnas**:
  - Usuario (nombre y correo)
  - Mensaje (título + descripción + tipo)
  - Fecha de envío (con tiempo relativo)
  - Estado (Leída/No leída con colores)
  - Acciones (Ver, Eliminar)
- **Paginación** para manejar grandes cantidades
- **Botón** para marcar todas como leídas

#### **Vista Individual** (`/notificaciones/{id}`)
- **Información completa del usuario** destinatario (nombre, correo, teléfono)
- **Título destacado** de la notificación
- **Mensaje descriptivo completo** en sección separada
- **Tipo de notificación** con ícono y color distintivo
- **Estado con fecha**: "Leída el 11/11/2025 21:37" o "No leída"
- **Enlace** a la declaración relacionada (si aplica)
- **Marcado automático como leída** al abrir

#### **Badge en Navbar**
- **Ícono de campana** con animación
- **Contador numérico** sobre la campana
- **Actualización en tiempo real** cada 1 segundo
- **Dropdown con vista previa** de últimas notificaciones
- **Animación** cuando llegan nuevas notificaciones

---

### 🎯 Eventos que Disparan Notificaciones

1. **✅ Crear Declaración**: 
   - Título: "Declaración Creada"
   - Mensaje: "Se ha creado exitosamente su declaración jurada con fecha inicio [fecha]"
   - Momento: Inmediatamente después de guardar

2. **✏️ Editar Declaración**: 
   - Título: "Declaración Actualizada"
   - Mensaje: "Se ha actualizado su declaración jurada con los nuevos datos proporcionados"
   - Momento: Después de guardar los cambios

3. **🗑️ Eliminar Declaración**: 
   - Título: "Declaración Eliminada"
   - Mensaje: "Se ha eliminado su declaración jurada del sistema"
   - Momento: Después de confirmar eliminación

4. **📄 Exportar PDF**: 
   - Título: "Declaración Exportada"
   - Mensaje: "Se ha generado exitosamente la exportación en formato PDF de su declaración jurada"
   - Momento: Después de generar el PDF

5. **📊 Exportar Excel**: 
   - Título: "Declaración Exportada"
   - Mensaje: "Se ha generado exitosamente la exportación en formato Excel de su declaración jurada"
   - Momento: Después de generar el Excel

6. **⏰ Recordatorio de Vencimiento**: 
   - Título: "Recordatorio: Declaración Próxima a Vencer"
   - Mensaje: "Su declaración jurada vence el [fecha]. Por favor, tome las acciones necesarias"
   - Momento: Automático, 7 días antes del vencimiento (8:00 AM)

---

### 🔄 Flujo Técnico del Sistema

#### **Notificaciones Inmediatas (CRUD + Exportación)**
```
┌─────────────────┐
│ Usuario realiza │
│     acción      │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Controlador   │
│    procesa      │
└────────┬────────┘
         │
         ▼
┌─────────────────────────┐
│  NotificacionService    │
│   crearNotificacion()   │
└────────┬───────┬────────┘
         │       │
         ▼       ▼
    ┌────────┐ ┌──────┐
    │   BD   │ │Email │
    └────┬───┘ └──────┘
         │
         ▼
┌─────────────────┐
│ Badge actualiza │
│  en 1 segundo   │
└─────────────────┘
```

#### **Badge en Tiempo Real (JavaScript)**
```
┌──────────────────┐
│  Página cargada  │
└────────┬─────────┘
         │
         ▼
┌──────────────────────────┐
│ JavaScript inicia polling│
│    cada 1 segundo        │
└────────┬─────────────────┘
         │
         ▼ (cada 1 segundo)
┌──────────────────────────┐
│ Fetch a /notificaciones- │
│       unread             │
└────────┬─────────────────┘
         │
         ▼
┌──────────────────────────┐
│ Recibe {count: X,        │
│ notifications: [...]}    │
└────────┬─────────────────┘
         │
         ▼
┌──────────────────────────┐
│ Actualiza badge number   │
│ Actualiza dropdown       │
│ Añade animación          │
└──────────────────────────┘
```

#### **Recordatorios Automáticos (Cron Job)**
```
┌──────────────────┐
│ Cron Job diario  │
│   (8:00 AM)      │
└────────┬─────────┘
         │
         ▼
┌──────────────────────────┐
│ Comando busca            │
│ declaraciones con        │
│ fecha_hasta = hoy + 7    │
└────────┬─────────────────┘
         │
    ┌────┴────┐
    ▼         ▼
┌───────┐ ┌───────┐
│  SÍ   │ │  NO   │
│ Envía │ │  Nada │
└───┬───┘ └───────┘
    │
    ▼
┌─────────────────────────┐
│  NotificacionService    │
│   crearNotificacion()   │
└────────┬───────┬────────┘
         │       │
         ▼       ▼
    ┌────────┐ ┌──────┐
    │   BD   │ │Email │
    └────────┘ └──────┘
```

---

### ⚙️ Comandos Útiles

```bash
# Ejecutar recordatorios manualmente
php artisan notificaciones:recordatorios-vencimiento

# Con días personalizados (ej. 3 días)
php artisan notificaciones:recordatorios-vencimiento --dias=3

# Ver ayuda del comando
php artisan notificaciones:recordatorios-vencimiento --help

# Ejecutar migraciones
php artisan migrate

# Ver rutas de notificaciones
php artisan route:list | grep notificaciones

# Iniciar servidor de desarrollo
php artisan serve

# Limpiar caché (útil después de cambios)
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

### 🛠️ Instalación y Configuración

#### 1. **Configurar el Entorno**
```bash
# Copiar archivo de configuración
cp .env.example .env

# Editar .env con tu configuración
nano .env
```

#### 2. **Ejecutar Migraciones**
```bash
php artisan migrate
```

#### 3. **Configurar Cron Job** (Para recordatorios automáticos)

En tu servidor, edita el crontab:
```bash
crontab -e
```

Añade esta línea:
```bash
* * * * * cd /ruta/completa/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

#### 4. **Probar el Sistema**
```bash
# Iniciar servidor
php artisan serve

# En otra terminal, probar recordatorios manualmente
php artisan notificaciones:recordatorios-vencimiento
```

---

### 📊 Estructura de Base de Datos

#### Tabla: `notificacion`

```sql
CREATE TABLE notificacion (
    id_notificacion INTEGER PRIMARY KEY AUTOINCREMENT,
    id_usuario INTEGER NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensaje TEXT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    id_declaracion INTEGER NULL,
    fecha_envio TIMESTAMP NULL,
    estado VARCHAR(50) DEFAULT 'enviada',
    leida BOOLEAN DEFAULT 0,
    fecha_lectura TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_declaracion) REFERENCES declaracion(id_declaracion) ON DELETE SET NULL
);

-- Índices para optimización
CREATE INDEX idx_notificacion_usuario_leida ON notificacion(id_usuario, leida);
CREATE INDEX idx_notificacion_tipo ON notificacion(tipo);
```

---

### 🎨 Personalización

#### **Cambiar el Intervalo de Actualización del Badge**

Editar `resources/views/layout.blade.php`:
```javascript
// Cambiar de 1000ms (1 segundo) a otro valor
notificationUpdateInterval = setInterval(updateNotifications, 1000);

// Ejemplos:
// 5000 = 5 segundos
// 10000 = 10 segundos
// 30000 = 30 segundos
```

#### **Modificar Días de Anticipación de Recordatorios**

Editar `app/Console/Kernel.php`:
```php
// Cambiar de 7 a otro número de días
$schedule->command('notificaciones:recordatorios-vencimiento --dias=7')->dailyAt('08:00');

// Múltiples recordatorios
$schedule->command('notificaciones:recordatorios-vencimiento --dias=7')->dailyAt('08:00');
$schedule->command('notificaciones:recordatorios-vencimiento --dias=3')->dailyAt('09:00');
$schedule->command('notificaciones:recordatorios-vencimiento --dias=1')->dailyAt('10:00');
```

#### **Personalizar Mensajes de Notificaciones**

Editar `app/Services/NotificacionService.php`:
```php
// Encontrar el método correspondiente y modificar el mensaje
public function notificarCrearDeclaracion($declaracion)
{
    return $this->crearNotificacion(
        $declaracion->id_usuario,
        'Tu Título Personalizado', // ← Cambiar aquí
        'Tu mensaje personalizado aquí', // ← Cambiar aquí
        Notificacion::TIPO_CREAR,
        $declaracion->id_declaracion
    );
}
```

---

### 📊 Estructura de Archivos del Sistema

```
app/
├── Services/
│   └── NotificacionService.php              ← Lógica centralizada de notificaciones
├── Console/
│   ├── Kernel.php                           ← Configuración de tareas programadas
│   └── Commands/
│       └── EnviarRecordatoriosVencimiento.php  ← Comando de recordatorios
├── Models/
│   ├── Notificacion.php                     ← Modelo con campos personalizados
│   └── Usuario.php                          ← Relación con notificaciones
├── Http/Controllers/
│   ├── NotificacionController.php           ← CRUD de notificaciones
│   ├── DeclaracionController.php            ← Integrado con notificaciones
│   └── DeclaracionExportController.php      ← Integrado con notificaciones
├── Notifications/
│   └── NotificacionPersonalizada.php        ← Clase de notificación por email
└── Mail/
    └── UsuarioCreado.php                    ← Plantilla de correo

database/
└── migrations/
    ├── xxxx_create_notificacion_table.php           ← Tabla base
    ├── 2025_11_11_202302_add_fields_to_notificacion_table.php  ← Campos adicionales
    └── 2025_11_11_213133_add_fecha_lectura_to_notificacion_table.php  ← Fecha de lectura

resources/
└── views/
    ├── layout.blade.php                     ← Badge y JavaScript de polling
    └── notificaciones/
        ├── index.blade.php                  ← Vista de listado
        └── show.blade.php                   ← Vista individual

routes/
└── web.php                                  ← Rutas del sistema de notificaciones
```

---

### ✅ Checklist de Funcionalidades Implementadas

- [x] **Servicio centralizado** de notificaciones (`NotificacionService`)
- [x] **Modelo personalizado** con campos adicionales
- [x] **Migraciones de base de datos** ejecutadas
- [x] **Integración en controladores** (Crear, Editar, Eliminar, Exportar)
- [x] **Notificaciones por email** (Gmail SMTP)
- [x] **Notificaciones en base de datos** (tabla `notificacion`)
- [x] **Badge en tiempo real** (actualización cada 1 segundo)
- [x] **Vista de listado** con tabla completa
- [x] **Vista individual** con detalles completos
- [x] **Marcado automático como leída** al abrir
- [x] **Fecha de lectura** registrada automáticamente
- [x] **Estados visuales** (Leída/No leída con colores)
- [x] **Tipos de notificación** (íconos y colores por tipo)
- [x] **Comando de recordatorios** automáticos
- [x] **Configuración de cron job** para recordatorios
- [x] **Dropdown interactivo** con vista previa
- [x] **Animación visual** en badge
- [x] **Botón marcar todas como leídas**
- [x] **Enlace a declaración relacionada**
- [x] **Mensajes descriptivos completos**
- [x] **Sistema de eliminación** de notificaciones
- [x] **Paginación** en listado

---

### 🎯 Estado Final del Sistema

#### **✅ Sistema 100% Funcional**

El sistema de notificaciones está completamente implementado y operativo. Incluye:

- ✅ **Backend completo**: Servicio, modelos, controladores, comandos
- ✅ **Frontend completo**: Vistas, JavaScript, estilos
- ✅ **Base de datos**: Migraciones ejecutadas, índices optimizados
- ✅ **Email**: Configuración SMTP de Gmail funcionando
- ✅ **Tiempo real**: Badge actualizado cada 1 segundo
- ✅ **Automatización**: Recordatorios programados diariamente
- ✅ **Interfaz completa**: Listado, detalle, badge, dropdown

#### **🚀 Listo para Producción**

El sistema está preparado para su uso en producción. Solo necesitas:
1. Configurar el archivo `.env` con tus credenciales
2. Ejecutar las migraciones (`php artisan migrate`)
3. Configurar el cron job para recordatorios automáticos
4. ¡Empezar a usar el sistema!

---

### 📞 Soporte y Documentación

- **Código fuente**: Todo el código está documentado con comentarios
- **README principal**: Ver `README.md` en la raíz del proyecto
- **Configuración**: Revisar `.env.example` para variables requeridas
- **Logs**: Revisar `storage/logs/laravel.log` para depuración

---

### 🎉 Conclusión

El sistema de notificaciones para Declaraciones Juradas UCR está **completamente implementado y funcional**, con:

- ✨ Notificaciones en tiempo real
- 📧 Emails automáticos
- ⏰ Recordatorios programados
- 🎨 Interfaz intuitiva y moderna
- 🔄 Actualización automática cada 1 segundo
- 📊 Gestión completa del ciclo de vida

**¡El sistema está listo para ser utilizado!** 🚀