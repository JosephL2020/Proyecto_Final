@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Estilos específicos del dashboard (corporativo moderno y compacto) --}}
    <style>
        .kpi-link {
            text-decoration: none;
        }

        .kpi-card {
            border-radius: 0.9rem;
            border: 0;
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.65rem 1.2rem rgba(15, 23, 42, 0.22);
            filter: brightness(1.05);
        }

        .kpi-icon {
            opacity: .9;
        }

        .section-title {
            font-size: .78rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
        }

        .table thead th {
            font-size: .76rem;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .dashboard-subtitle {
            font-size: .88rem;
        }

        .card-compact .card-body {
            padding: 0.85rem 1rem;
        }

        .badge-chip {
            font-size: 0.76rem;
            font-weight: 500;
            border-radius: 999px;
        }

        /* MEJORAS PARA TABLA DE RENDIMIENTO IT */
.performance-table {
    font-size: 0.82rem;
}

.performance-table th {
    text-align: center;
    white-space: nowrap;
    padding: 0.5rem 0.4rem;
    font-weight: 600;
}

.performance-table td {
    text-align: center;
    padding: 0.5rem 0.4rem;
    vertical-align: middle;
}

.performance-table .user-name {
    text-align: left;
    font-weight: 500;
    white-space: nowrap;
}

.performance-table .rating-stars {
    font-size: 0.75rem;
    letter-spacing: 1px;
}

.performance-table .stat-number {
    font-weight: 600;
    font-size: 0.85rem;
}

/* Header compacto y centrado */
.performance-table thead th {
    background-color: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
}

/* Modo oscuro */
body.dark-mode .performance-table thead th {
    background-color: #1e293b;
    border-bottom-color: #374151;
}

body.dark-mode .performance-table .stat-number {
    color: #f1f5f9;
}
    </style>

    {{-- Encabezado --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h3 class="mb-1">Panel de Control</h3>
            <div class="dashboard-subtitle text-muted">
                Visión general del soporte, tiempos de atención y rendimiento del equipo
            </div>
        </div>
    </div>

    {{-- HERO RESUMEN DEL MES --}}
    <div class="row g-2 mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm card-compact" style="border-radius: 0.9rem;">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="mb-2 mb-md-0">
                        <div class="section-title mb-1">Resumen del mes</div>
                        <h6 class="mb-1">
                            {{ ucfirst($monthSummary['label']) }}
                        </h6>
                        <div class="text-muted small">
                            Actividad general en el mes actual.
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge badge-chip bg-primary-subtle text-primary border">
                            Creados: <strong>{{ $monthSummary['created'] }}</strong>
                        </span>
                        <span class="badge badge-chip bg-success-subtle text-success border">
                            Resueltos/cerrados: <strong>{{ $monthSummary['resolved'] }}</strong>
                        </span>
                        <span class="badge badge-chip bg-warning-subtle text-warning border">
                            SLA prom.: 
                            <strong>
                                @if($monthSummary['avg_resolution_hours'] !== null)
                                    {{ $monthSummary['avg_resolution_hours'] }} h
                                @else
                                    —
                                @endif
                            </strong>
                        </span>
                        <span class="badge badge-chip bg-secondary-subtle text-secondary border">
                            Pendientes:
                            <strong>{{ $monthSummary['open_now'] }}</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ESTADO GENERAL --}}
    <div class="mb-1">
        <span class="section-title">Estado general de los tickets</span>
    </div>
    <div class="row g-2 mb-3">

        {{-- Total --}}
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('tickets.index') }}" class="kpi-link">
                <div class="card kpi-card text-white h-100">
                    <div class="card-body py-3 px-3"
                         style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-white-50 mb-1">Total de tickets</div>
                                <div class="h3 mb-0">{{ $stats['total'] ?? 0 }}</div>
                            </div>
                            <i class="bi bi-collection-play h3 mb-0 kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Abiertos --}}
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('tickets.index', ['status' => 'open']) }}" class="kpi-link">
                <div class="card kpi-card text-white h-100">
                    <div class="card-body py-3 px-3"
                         style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-white-50 mb-1">Abiertos</div>
                                <div class="h3 mb-0">{{ $stats['open'] ?? 0 }}</div>
                            </div>
                            <i class="bi bi-envelope-open h3 mb-0 kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Asignados --}}
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('tickets.index', ['status' => 'assigned']) }}" class="kpi-link">
                <div class="card kpi-card text-dark h-100">
                    <div class="card-body py-3 px-3"
                         style="background: linear-gradient(135deg, #facc15, #eab308);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-dark mb-1">Asignados</div>
                                <div class="h3 mb-0">{{ $stats['assigned'] ?? 0 }}</div>
                            </div>
                            <i class="bi bi-person-workspace h3 mb-0 kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- En progreso --}}
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}" class="kpi-link">
                <div class="card kpi-card text-white h-100">
                    <div class="card-body py-3 px-3"
                         style="background: linear-gradient(135deg, #6b7280, #4b5563);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-white-50 mb-1">En progreso</div>
                                <div class="h3 mb-0">{{ $stats['in_progress'] ?? 0 }}</div>
                            </div>
                            <i class="bi bi-arrow-repeat h3 mb-0 kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Resueltos --}}
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('tickets.index', ['status' => 'resolved']) }}" class="kpi-link">
                <div class="card kpi-card text-white h-100">
                    <div class="card-body py-3 px-3"
                         style="background: linear-gradient(135deg, #22c55e, #16a34a);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-white-50 mb-1">Resueltos</div>
                                <div class="h3 mb-0">{{ $stats['resolved'] ?? 0 }}</div>
                            </div>
                            <i class="bi bi-check2-circle h3 mb-0 kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Cerrados --}}
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('tickets.index', ['status' => 'closed']) }}" class="kpi-link">
                <div class="card kpi-card text-white h-100">
                    <div class="card-body py-3 px-3"
                         style="background: linear-gradient(135deg, #111827, #020617);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-white-50 mb-1">Cerrados</div>
                                <div class="h3 mb-0">{{ $stats['closed'] ?? 0 }}</div>
                            </div>
                            <i class="bi bi-lock-fill h3 mb-0 kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- FILA 2: KPIs de tiempo + Volumen reciente --}}
    <div class="row g-3 mb-3">
        {{-- KPIs de tiempo globales --}}
        <div class="col-lg-8">
            <div class="mb-1">
                <span class="section-title">Tiempos de atención (global)</span>
            </div>
            <div class="card border-0 shadow-sm card-compact h-100">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="small text-muted mb-1">Promedio de resolución</div>
                            <div class="h5 mb-0">
                                @if(!empty($timeKpis['global']['avg_resolution_hours']))
                                    {{ $timeKpis['global']['avg_resolution_hours'] }} h
                                @else
                                    <span class="text-muted">Sin datos</span>
                                @endif
                            </div>
                            <small class="text-muted">Tiempo medio para resolver/cerrar</small>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="small text-muted mb-1">Resolución más rápida</div>
                            <div class="h5 mb-0">
                                @if(!empty($timeKpis['global']['fastest_resolution_hours']))
                                    {{ $timeKpis['global']['fastest_resolution_hours'] }} h
                                @else
                                    <span class="text-muted">Sin datos</span>
                                @endif
                            </div>
                            <small class="text-muted">Ticket atendido en menos tiempo</small>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="small text-muted mb-1">Resolución más lenta</div>
                            <div class="h5 mb-0">
                                @if(!empty($timeKpis['global']['slowest_resolution_hours']))
                                    {{ $timeKpis['global']['slowest_resolution_hours'] }} h
                                @else
                                    <span class="text-muted">Sin datos</span>
                                @endif
                            </div>
                            <small class="text-muted">Caso que más demoró</small>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="small text-muted mb-1">Promedio en cola (abiertos)</div>
                            <div class="h5 mb-0">
                                @if(!empty($timeKpis['open']['avg_open_age_hours']))
                                    {{ $timeKpis['open']['avg_open_age_hours'] }} h
                                @else
                                    <span class="text-muted">Sin datos</span>
                                @endif
                            </div>
                            <small class="text-muted">Tiempo promedio que llevan abiertos</small>
                        </div>
                    </div>

                    <div class="mt-2 small text-muted">
                        <strong>Nota:</strong> Los tiempos se calculan desde la creación del ticket
                        hasta su resolución/cierre. El tiempo en cola indica cuánto tiempo, en promedio,
                        llevan abiertos los tickets pendientes.
                    </div>
                </div>
            </div>
        </div>

        {{-- Volumen reciente --}}
        <div class="col-lg-4">
            <div class="mb-1">
                <span class="section-title">Volumen reciente</span>
            </div>
            <div class="card border-0 shadow-sm card-compact h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="small text-muted">Tickets creados hoy</div>
                            <div class="h4 mb-0">{{ $volumeKpis['today_created'] ?? 0 }}</div>
                        </div>
                        <span class="badge badge-chip bg-primary-subtle text-primary border">
                            Hoy
                        </span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Tickets últimos 7 días</div>
                            <div class="h4 mb-0">{{ $volumeKpis['last7_created'] ?? 0 }}</div>
                        </div>
                        <span class="badge badge-chip bg-info-subtle text-info border">
                            7 días
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILA 3: Gráficas --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
        <div class="card border-0 shadow-sm card-compact h-100">
                <div class="card-body">
                    <div class="mb-2">
                        <span class="section-title">Tendencia de tickets (últimos 14 días)</span>
                    </div>
                    <canvas id="ticketsDailyChart" height="135"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
        <div class="card border-0 shadow-sm card-compact h-100">
                <div class="card-body">
                    <div class="mb-2">
                        <span class="section-title">Distribución por categoría</span>
                    </div>
                    <canvas id="ticketsCategoryChart" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>


{{-- FILA 4: Rendimiento por soporte IT + Top categorías --}}
<div class="row g-3">
    {{-- Tabla IT MEJORADA --}}
    <div class="col-lg-8">
        <div class="mb-1">
            <span class="section-title">Rendimiento por soporte (IT)</span>
        </div>
        <div class="card border-0 shadow-sm card-compact">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table performance-table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Soporte</th>
                                <th>Abiertos</th>
                                <th>Asignados</th>
                                <th>En prog.</th>
                                <th>Resueltos</th>
                                <th>Cerrados</th>
                                <th># Calif.</th>
                                <th>Resoluciones</th>
                                <th>Tiempo prom.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($its as $it)
                                @php
                                    $s = $itStats->get($it->id);
                                    $avg = (float)($s->avg_rating ?? 0);
                                    $userTime = $perUserTimeKpis[$it->id] ?? null;
                                    $totalResolved = ($s->resolved_cnt ?? 0) + ($s->closed_cnt ?? 0);
                                @endphp
                                <tr>
                                    {{-- Nombre --}}
                                    <td class="user-name">
                                        <div class="fw-semibold">{{ $it->name }}</div>
                                        <small class="text-muted">{{ $s->rated_count ?? 0 }} calif.</small>
                                    </td>
                                    
                                    {{-- Estados --}}
                                    <td>
                                        <span class="stat-number">{{ $s->open_cnt ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="stat-number">{{ $s->assigned_cnt ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="stat-number">{{ $s->in_progress_cnt ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="stat-number text-success">{{ $s->resolved_cnt ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="stat-number text-secondary">{{ $s->closed_cnt ?? 0 }}</span>
                                    </td>
                                    
                                    {{-- Calificación --}}
                                    <td>
                                        @if($avg > 0)
                                            <div class="rating-stars text-warning mb-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($avg))
                                                        ★
                                                    @elseif($i - 0.5 <= $avg)
                                                        ⭐
                                                    @else
                                                        ☆
                                                    @endif
                                                @endfor
                                            </div>
                                            <small class="text-muted">{{ number_format($avg, 1) }}</small>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Resoluciones --}}
                                    <td>
                                        <span class="stat-number text-primary">{{ $totalResolved }}</span>
                                    </td>
                                    
                                    {{-- Tiempo promedio --}}
                                    <td>
                                        @if($userTime && $userTime['avg_resolution_hours'] !== null)
                                            <span class="stat-number">{{ $userTime['avg_resolution_hours'] }}h</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-3">
                                        No hay usuarios IT configurados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
           </div>
        </div>
    </div>

   

        {{-- Top categorías --}}
        <div class="col-lg-4">
            @php
                $maxTickets = max(1, optional($topCategories)->max('tickets_count') ?? 1);
            @endphp

            <div class="mb-1">
                <span class="section-title">Top categorías</span>
            </div>
            <div class="card border-0 shadow-sm card-compact h-100">
                <div class="card-body">
                    @forelse($topCategories ?? [] as $c)
                        @php
                            $pct = $maxTickets ? ($c->tickets_count / $maxTickets * 100) : 0;
                            $url = route('tickets.index', ['category_id' => $c->id]);
                        @endphp
                        <a href="{{ $url }}" class="d-block text-decoration-none mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-truncate">{{ $c->name }}</strong>
                                <span class="text-muted small">{{ $c->tickets_count }} tickets</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar"
                                     role="progressbar"
                                     style="width: {{ number_format($pct, 2) }}%;"
                                     aria-valuenow="{{ number_format($pct, 2) }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </a>
                    @empty
                        <em class="text-muted">Sin datos de categorías.</em>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts de Chart.js --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos para gráfica de tickets por día
    const dailyLabels = @json($chartDaily['labels']);
    const dailyData   = @json($chartDaily['data']);

    const ctxDaily = document.getElementById('ticketsDailyChart');
    if (ctxDaily) {
        new Chart(ctxDaily, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Tickets por día',
                    data: dailyData,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.25)',
                    pointRadius: 4,
                    pointHoverRadius: 5,
                    pointBackgroundColor: 'rgba(37, 99, 235, 1)',
                    pointBorderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Datos para gráfica de tickets por categoría
    const categoryLabels = @json($categoryChart['labels']);
    const categoryData   = @json($categoryChart['data']);

    const ctxCategory = document.getElementById('ticketsCategoryChart');
    if (ctxCategory) {
        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: [
                        '#3b82f6', // azul
                        '#22c55e', // verde
                        '#f97316', // naranja
                        '#ec4899', // rosa
                        '#a855f7', // morado
                        '#eab308'  // amarillo
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '60%'
            }
        });
    }
</script>
@endpush
@endsection
