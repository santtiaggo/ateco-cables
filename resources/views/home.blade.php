@extends('layouts.app')

@section('content')
<section class="hero" style="background-image:url('/images/ateco/7LmCtYnL9iN9VDoSManbu6IzjOXD4e2Clsf6w1mP.png')">
  <div class="hero-overlay"></div>
  <div class="container hero-inner">
    <div class="hero-content">
      <h1>Asesoramiento Técnico y Comercialización de Cables</h1>
      <p class="lead">Somos una empresa familiar que desde el año 1990 atiende al mercado de cables, principalmente de energía y de hasta 33kV.</p>
      <a href="{{ route('products.catalog') }}" class="btn-primary">Ver Productos</a>
    </div>
  </div>
</section>

<section class="industries container">
  <div class="section-title" style="margin-top:36px">
    <h2>Productos</h2>
    <a href="{{ route('products.catalog') }}" class="btn-ghost">Ver todos</a>
  </div>

  <div class="grid-6">
    @foreach($featured as $item)
      @if(is_object($item) || is_array($item))
        @php
          // Si viene Eloquent -> usar accessor image_url; si array -> usar image key
          $name = is_object($item) ? ($item->title ?? $item->name) : ($item['name'] ?? pathinfo($item['filename'], PATHINFO_FILENAME));
          $img = is_object($item) ? ($item->image_url ?? (isset($item->image) ? asset('images/ateco/products/'.$item->image) : null)) : ($item['image'] ?? null);
        @endphp

        <a href="#" class="category-card">
          <div class="cat-media"><img src="{{ $img }}" alt="{{ $name }}"></div>
          <div class="cat-body"><h3>{{ $name }}</h3><span class="arrow">›</span></div>
        </a>
      @endif
    @endforeach
  </div>
</section>

<section class="about-section">
  <div class="about-grid">
    <div class="about-media">
      <img src="/images/ateco/worker.png" alt="Sobre nosotros">
    </div>

    <div class="about-body" style="height: 50px">
      <div class="about-body-inner">        
        <h3>Sobre Nosotros</h3>
        <p>
          ATECO CABLES SRL es una empresa familiar que desde el año 1990 atiende al mercado de cables, principalmente de energía y de hasta 33kV.<br><br>
          El funcionamiento de nuestra actividad está basado en brindar soluciones a nuestros clientes mediante asesoramiento técnico y la comercialización de productos. La estrategia consiste en profundizar y adecuar estos conceptos para lograr una integración con las necesidades que se requieren en el desarrollo de los proyectos.
        </p>
        <a href="/nosotros" class="btn-outline">Más información</a>
      </div>
    </div>
  </div>
</section>
@endsection
