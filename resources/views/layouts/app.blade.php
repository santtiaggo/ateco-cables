<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Ateco Cables | Soluciones en Cables Eléctricos')</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('searchToggle');
    if (!btn) return;
    btn.addEventListener('click', function () {
      document.body.classList.toggle('search-open');
    });
  });
</script>

 @vite(['resources/css/ateco.css'])


@stack('head')
</head>
<body>
  @include('partials.header')
  <main>@yield('content')</main>
  @include('partials.footer')
  @stack('scripts')
</body>
</html>

