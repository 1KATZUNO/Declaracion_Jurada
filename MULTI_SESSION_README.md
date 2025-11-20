# Sistema de Múltiples Sesiones Simultáneas

## 🎯 ¿Qué es esto?

Este sistema permite que **múltiples usuarios puedan iniciar sesión simultáneamente en el mismo navegador**, cada uno en su propia ventana o pestaña, sin que se cierren las sesiones entre sí.

## ✨ Características

- ✅ **Múltiples ventanas/pestañas** con diferentes usuarios
- ✅ **Sesiones independientes** por ventana
- ✅ **No se cierran entre sí** al hacer login
- ✅ **Indicador visual** de sesiones múltiples activas
- ✅ **Logout selectivo** - Solo cierra la ventana actual
- ✅ **Compatible con el sistema existente**

## 📖 ¿Cómo funciona?

### Para el usuario:

1. **Abrir primera sesión:**
   - Ir a la página de login
   - Iniciar sesión con Usuario 1
   - La ventana queda autenticada como Usuario 1

2. **Abrir segunda sesión:**
   - Abrir una **nueva ventana o pestaña** del mismo navegador
   - Ir a la página de login
   - Iniciar sesión con Usuario 2
   - La nueva ventana queda autenticada como Usuario 2
   - **La primera ventana (Usuario 1) sigue activa**

3. **Trabajar con ambas sesiones:**
   - Puedes alternar entre ventanas
   - Cada una mantiene su propia sesión
   - El indicador muestra "X ventanas activas"

4. **Cerrar sesión:**
   - Al hacer "Cerrar sesión" solo se cierra la ventana actual
   - Las demás ventanas siguen funcionando

## 🔧 Tecnología

### Frontend:
- **SessionStorage**: Almacena token único por ventana/pestaña
- **LocalStorage**: Rastrea ventanas abiertas
- **Cookies**: Token de sesión para requests HTTP
- **JavaScript**: Intercepta requests y formularios

### Backend:
- **Namespace de sesiones**: `auth_sessions.{token}`
- **Token único** generado al login
- **Middleware personalizado**: Restaura sesión por token
- **Logout selectivo**: Solo elimina la sesión del token actual

## 📁 Archivos modificados

### Backend:
- `app/Http/Controllers/LoginController.php` - Genera tokens y maneja logout selectivo
- `app/Http/Middleware/MultiSessionAuth.php` - Middleware para multi-sesión (nuevo)
- `app/Http/Middleware/VerificarRol.php` - Actualizado para usar tokens

### Frontend:
- `resources/views/layout.blade.php` - Script de multi-sesión y indicador visual
- `resources/views/auth/login.blade.php` - Compatible con tokens

## 🎨 Indicador Visual

En el dropdown del usuario aparece:
```
Angelita Aguilar
ADMIN
✓ Sesión independiente (2 ventanas activas)
```

Este indicador se actualiza cada 3 segundos para reflejar el número de ventanas abiertas.

## ⚙️ Configuración

No se requiere configuración adicional. El sistema funciona automáticamente al:
1. Hacer login
2. Navegar por el sitio
3. Cerrar sesión

## 🔒 Seguridad

- Los tokens son únicos y aleatorios (64 caracteres hexadecimales)
- Se almacenan en SessionStorage (no persisten al cerrar ventana)
- Las cookies son SameSite=Lax para evitar CSRF
- Cada sesión está aislada de las demás

## 🐛 Solución de problemas

### "Mi sesión se cierra cuando abro otra ventana"
- Asegúrate de que JavaScript esté habilitado
- Revisa la consola del navegador para ver logs
- Verifica que el token se esté guardando en SessionStorage

### "El indicador no muestra las ventanas correctas"
- El contador se actualiza cada 3 segundos
- LocalStorage se limpia al cerrar ventanas
- Puedes forzar actualización abriendo el dropdown del usuario

## 📝 Notas

- Compatible con navegadores modernos (Chrome, Firefox, Edge, Safari)
- Requiere JavaScript habilitado
- SessionStorage se limpia automáticamente al cerrar ventana/pestaña
- Las sesiones antiguas se limpian del servidor según la configuración de Laravel

## 🚀 Próximas mejoras

- [ ] Dashboard para administrar todas las sesiones activas
- [ ] Notificación cuando otra ventana hace login
- [ ] Sincronización de eventos entre ventanas
- [ ] Límite configurable de sesiones simultáneas
