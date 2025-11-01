<!doctype html>
<html>
<head>
	<!-- ...existing code... -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- ...existing code... -->
</head>
<body>
	<!-- ...existing code... -->

	<!-- Si usas jQuery, agrega esto para enviar el token en AJAX -->
	<script>
		// ...existing code...
		@if (app()->environment('local') || app()->environment('production'))
		(function () {
			var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
			if (token) {
				// jQuery
				if (window.jQuery) {
					$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });
				}
				// Fetch/axios example (axios suele configurar automáticamente si lo require)
				// window.axios && (window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token);
			}
		})();
		@endif
		// ...existing code...
	</script>

	<!-- ...existing code... -->
</body>
</html>