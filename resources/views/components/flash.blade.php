@csrf
{{-- Estilos para las notificaciones toast --}}
<style>
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes pulse-glow {
        0%, 100% {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        50% {
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.2), 0 10px 15px -6px rgba(0, 0, 0, 0.15);
        }
    }

    .toast-notification {
        animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.2), 0 10px 15px -6px rgba(0, 0, 0, 0.15);
    }

    .toast-notification.closing {
        animation: slideOutRight 0.3s cubic-bezier(0.5, 0, 0.75, 0);
    }

    .toast-progress {
        animation: shrink 5s linear forwards;
    }

    @keyframes shrink {
        from { width: 100%; }
        to { width: 0%; }
    }
</style>

{{-- Container fijo para las notificaciones toast --}}
<div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-3 pointer-events-none" style="max-width: 420px;">
</div>

{{-- Script para manejar las notificaciones --}}
<script>
    // Función global para mostrar notificaciones toast desde cualquier parte del código
    window.showToast = function(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) {
            console.warn('Toast container not found');
            return;
        }
        
        const toastId = 'toast-' + Date.now();
        
        // Configuración según el tipo
        const config = {
            success: {
                icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>`,
                bgClass: 'bg-gradient-to-r from-green-500 to-emerald-600',
                textClass: 'text-white'
            },
            error: {
                icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>`,
                bgClass: 'bg-gradient-to-r from-red-500 to-rose-600',
                textClass: 'text-white'
            },
            warning: {
                icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                      </svg>`,
                bgClass: 'bg-gradient-to-r from-amber-500 to-orange-600',
                textClass: 'text-white'
            },
            info: {
                icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>`,
                bgClass: 'bg-gradient-to-r from-blue-500 to-indigo-600',
                textClass: 'text-white'
            }
        };

        const settings = config[type] || config.success;
        
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast-notification pointer-events-auto ${settings.bgClass} ${settings.textClass} rounded-lg shadow-2xl overflow-hidden backdrop-blur-sm`;
        toast.style.minWidth = '320px';
        
        toast.innerHTML = `
            <div class="flex items-start gap-3 p-4">
                <div class="flex-shrink-0 mt-0.5">
                    ${settings.icon}
                </div>
                <div class="flex-1 pt-0.5">
                    <p class="text-sm font-medium leading-relaxed">${message}</p>
                </div>
                <button onclick="closeToast('${toastId}')" class="flex-shrink-0 ml-2 hover:bg-white/20 rounded-full p-1 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="h-1 bg-white/30">
                <div class="toast-progress h-full bg-white/50"></div>
            </div>
        `;
        
        container.appendChild(toast);
        
        // Auto-cerrar después de 5 segundos
        setTimeout(() => closeToast(toastId), 5000);
    }

    window.closeToast = function(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.add('closing');
            setTimeout(() => toast.remove(), 300);
        }
    }

    // Mostrar notificaciones desde la sesión de Laravel
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('ok'))
            showToast("{{ session('ok') }}", 'success');
        @endif

        @if (session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        @if (session('warning'))
            showToast("{{ session('warning') }}", 'warning');
        @endif

        @if (session('info'))
            showToast("{{ session('info') }}", 'info');
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                showToast("{{ $error }}", 'error');
            @endforeach
        @endif
    });
</script>
