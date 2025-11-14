@extends('layouts.app')

@section('title', 'Dashboard - TRIMAX')

@section('content')

    <!-- card info -->
    <div class="mb-4 row g-4">
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="stat-card w-100">
                <div class="stat-label">Happiness Index</div>
                <div class="stat-value blue count" style="color: {{ $avgColor }}; display:inline-block"
                    data-target=" {{ number_format($avgRating, 2) }} "> 0 </div>
                <div class="stat-value dark-blue" style="display: inline-block;"> /4.00</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 d-flex">
            <div class="stat-card w-100">
                <div class="stat-label">Respuestas</div>
                <div class="stat-value dark-blue count" data-target="{{ $totalSurveys }}">0</div>
            </div>
        </div>
        @forelse($topSedes as $item)
            @if ($loop->first)
                <div class="col-lg-3 col-md-6 d-flex">
                    <div class="stat-card w-100">
                        <div class="stat-label">Sede Top</div>
                        <div class="stat-value green" style="font-size: 35px;">{{ $item['user']->name }}</div>
                        @if ($item['user']->consultor)
                            <small style="color: #666;">Consultor:
                                {{ $item['user']->consultor->name }}</small>
                        @endif
                    </div>
                </div>
            @endif
        @empty
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="stat-card w-100">
                    <div class="stat-label">No hay datos</div>
                </div>
            </div>
        @endforelse
        @forelse($topConsultores as $item)
            @if ($loop->first)
                @php
                    $nombreCompleto = $item['user']->name;
                    $partes = explode(' ', trim($nombreCompleto));
                    $primerNombre = $partes[0] ?? '';
                    $primerApellido = $partes[1] ?? '';
                @endphp

                <div class="col-lg-3 col-md-6 d-flex">
                    <div class="stat-card w-100">
                        <div class="stat-label">Consultor TOP</div>
                        <div class="stat-value dark-blue" style="font-size: 35px;">
                            {{ $primerNombre }} {{ $primerApellido }}
                        </div>
                        <small style="color: #666;">{{ $item['stats']['sedes_count'] }} sedes</small>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="stat-card w-100">
                    <div class="stat-label">No hay datos</div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- grafic -->
    <div class="chart-container">
        <div class="chart-title">
            Resumen general - <span style="color: #1e3a8a;">TRIMAX GENERAL</span>
        </div>
        <div class="chart-subtitle">% por día e índice de felicidad</div>

        <div class="chart-legend">
            <div class="legend-item">
                <div class="legend-color" style="background: #16a34a; width: 2rem;"></div>
                <span>Muy feliz</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #4ade80; width: 2rem;"></div>
                <span>Feliz</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #f87171; width: 2rem;"></div>
                <span>Insatisfecho</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #dc2626; width: 2rem;"></div>
                <span>Muy insatisfecho</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #2563eb; width: 2rem;"></div>
                <span>Índice de felicidad</span>
            </div>
        </div>

        <div style="position: relative; height: 400px;">
            <canvas id="mainChart"></canvas>
        </div>
    </div>

    

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const counters = document.querySelectorAll(".count");
            const speed = 150; // Ajusta velocidad (más alto = más lento)

            counters.forEach(counter => {
                const animate = () => {
                    const target = +counter.getAttribute("data-target");
                    const count = +counter.innerText;
                    const increment = target / speed;

                    if (count < target) {
                        counter.innerText = Math.ceil(count + increment);
                        setTimeout(animate, 10);
                    } else {
                        counter.innerText = target;
                    }
                };
                animate();
            });
        });
    </script>

@endsection
