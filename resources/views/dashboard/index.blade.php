@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Estilos profesionales del dashboard --}}
    <style>
        :root {
            --primary: #2c5aa0;
            --primary-dark: #1e3d72;
            --primary-light: #4a7bce;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-400: #ced4da;
            --gray-500: #adb5bd;
            --gray-600: #6c757d;
            --gray-700: #495057;
            --gray-800: #343a40;
            --gray-900: #212529;
            --border-radius: 0.5rem;
            --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --box-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .kpi-link {
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .kpi-link:hover {
            transform: translateY(-2px);
        }

        .kpi-card {
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-300);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
            background: white;
            overflow: hidden;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--box-shadow-hover);
        }

        .kpi-icon {
            opacity: 0.8;
            font-size: 1.75rem;
        }

        .section-title {
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--gray-600);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .dashboard-subtitle {
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        .card-compact .card-body {
            padding: 1rem;
        }

        .badge-chip {
            font-size: 0.7rem;
            font-weight: 500;
            border-radius: 50px;
            padding: 0.25rem 0.5rem;
        }

        /* MEJORAS PARA TABLA DE RENDIMIENTO IT */
        .performance-table {
            font-size: 0.8rem;
        }

        .performance-table th {
            text-align: center;
            white-space: nowrap;
            padding: 0.5rem 0.4rem;
            font-weight: 600;
            background-color: var(--gray-100);
            border-bottom: 1px solid var(--gray-300);
        }

        .performance-table td {
            text-align: center;
            padding: 0.5rem 0.4rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--gray-200);
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
            background-color: var(--gray-100);
            border-bottom: 2px solid var(--gray-300);
        }

        /* Estilos para la cuadrícula de estado general mejorada */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        @media (min-width: 768px) {
            .status-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1200px) {
            .status-grid {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        .status-card {
            border-radius: var(--border-radius);
            padding: 1rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            min-height: 100px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: var(--box-shadow);
        }

        .status-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--box-shadow-hover);
        }

        .status-value {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .status-label {
            font-size: 0.75rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .status-icon {
            font-size: 1.5rem;
            opacity: 0.8;
            align-self: flex-end;
        }

        /* Gráficos más compactos */
        .compact-chart {
            height: 220px !important;
        }

        /* Colores profesionales para las tarjetas de estado */
        .status-total {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }

        .status-open {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
        }

        .status-assigned {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .status-progress {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        .status-resolved {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .status-closed {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        /* Tarjetas con bordes sutiles */
        .card-elegant {
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: all 0.2s ease;
        }

        .card-elegant:hover {
            box-shadow: var(--box-shadow-hover);
        }

        /* Mejoras para la tabla de rendimiento */
        .table-hover tbody tr:hover {
            background-color: rgba(44, 90, 160, 0.04);
        }

        /* Estilos para las barras de progreso */
        .progress {
            height: 6px;
            border-radius: 3px;
            background-color: var(--gray-200);
        }

        .progress-bar {
            border-radius: 3px;
        }

        /* Encabezado mejorado */
        .dashboard-header {
            border-bottom: 1px solid var(--gray-300);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        /* Modo oscuro */
        body.dark-mode .performance-table thead th {
            background-color: var(--gray-800);
            border-bottom-color: var(--gray-700);
        }

        body.dark-mode .performance-table .stat-number {
            color: var(--gray-100);
        }

        body.dark-mode .card-elegant {
            background-color: var(--gray-800);
            border-color: var(--gray-700);
        }

        /* Mejoras en los badges del resumen */
        .summary-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
        }
    </style>

    {{-- Encabezado --}}
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1 fw-bold text-dark">Panel de Control</h3>
                <div class="dashboard-subtitle">
                    Visión general del soporte, tiempos de atención y rendimiento del equipo
                </div>
            </div>
            <div class="d-none d-md-block">
                <span class="badge bg-light text-dark summary-badge">
                    <i class="bi bi-calendar-week me-1"></i>
                    {{ now()->format('d M Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- RESUMEN DEL MES --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card card-elegant">
                <div class="card-body py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-2 mb-md-0">
                            <div class="section-title">Resumen del Mes Actual</div>
                            <h5 class="mb-1 text-dark">
                                {{ ucfirst($monthSummary['label']) }}
                            </h5>
                            <div class="text-muted small">
                                Actividad general en el mes actual.
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge badge-chip bg-primary text-white">
                                <i class="bi bi-plus-circle me-1"></i>
                                Creados: <strong>{{ $monthSummary['created'] }}</strong>
                            </span>
                            <span class="badge badge-chip bg-success text-white">
                                <i class="bi bi-check-circle me-1"></i>
                                Resueltos: <strong>{{ $monthSummary['resolved'] }}</strong>
                            </span>
                            <span class="badge badge-chip bg-warning text-dark">
                                <i class="bi bi-clock me-1"></i>
                                SLA: 
                                <strong>
                                    @if($monthSummary['avg_resolution_hours'] !== null)
                                        {{ $monthSummary['avg_resolution_hours'] }} h
                                    @else
                                        —
                                    @endif
                                </strong>
                            </span>
                            <span class="badge badge-chip bg-secondary text-white">
                                <i class="bi bi-hourglass-split me-1"></i>
                                Pendientes: <strong>{{ $monthSummary['open_now'] }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ESTADO GENERAL - REORGANIZADO EN BLOQUES --}}
    <div class="mb-3">
        <span class="section-title">Estado General de Tickets</span>
    </div>
    <div class="status-grid mb-4">
        {{-- Total --}}
        <a href="{{ route('tickets.index') }}" class="kpi-link">
            <div class="status-card status-total">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="status-label">Total de Tickets</div>
                        <div class="status-value">{{ $stats['total'] ?? 0 }}</div>
                    </div>
                    <i class="bi bi-collection-play status-icon"></i>
                </div>
            </div>
        </a>

        {{-- Abiertos --}}
        <a href="{{ route('tickets.index', ['status' => 'open']) }}" class="kpi-link">
            <div class="status-card status-open">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="status-label">Abiertos</div>
                        <div class="status-value">{{ $stats['open'] ?? 0 }}</div>
                    </div>
                    <i class="bi bi-envelope-open status-icon"></i>
                </div>
            </div>
        </a>

        {{-- Asignados --}}
        <a href="{{ route('tickets.index', ['status' => 'assigned']) }}" class="kpi-link">
            <div class="status-card status-assigned">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="status-label">Asignados</div>
                        <div class="status-value">{{ $stats['assigned'] ?? 0 }}</div>
                    </div>
                    <i class="bi bi-person-workspace status-icon"></i>
                </div>
            </div>
        </a>

        {{-- En progreso --}}
        <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}" class="kpi-link">
            <div class="status-card status-progress">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="status-label">En Progreso</div>
                        <div class="status-value">{{ $stats['in_progress'] ?? 0 }}</div>
                    </div>
                    <i class="bi bi-arrow-repeat status-icon"></i>
                </div>
            </div>
        </a>

        {{-- Resueltos --}}
        <a href="{{ route('tickets.index', ['status' => 'resolved']) }}" class="kpi-link">
            <div class="status-card status-resolved">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="status-label">Resueltos</div>
                        <div class="status-value">{{ $stats['resolved'] ?? 0 }}</div>
                    </div>
                    <i class="bi bi-check2-circle status-icon"></i>
                </div>
            </div>
        </a>

        {{-- Cerrados --}}
        <a href="{{ route('tickets.index', ['status' => 'closed']) }}" class="kpi-link">
            <div class="status-card status-closed">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="status-label">Cerrados</div>
                        <div class="status-value">{{ $stats['closed'] ?? 0 }}</div>
                    </div>
                    <i class="bi bi-lock-fill status-icon"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- FILA 2: KPIs de tiempo + Volumen reciente --}}
    <div class="row g-3 mb-4">
        {{-- KPIs de tiempo globales --}}
        <div class="col-lg-8">
            <div class="mb-2">
                <span class="section-title">Métricas de Tiempo de Atención</span>
            </div>
            <div class="card card-elegant h-100">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-speedometer2 text-primary fs-2"></i>
                            </div>
                            <div class="small text-muted mb-1">Promedio de Resolución</div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                @if(!empty($timeKpis['global']['avg_resolution_hours']))
                                    {{ $timeKpis['global']['avg_resolution_hours'] }} h
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                            <small class="text-muted">Tiempo medio para resolver/cerrar</small>
                        </div>

                        <div class="col-6 col-md-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-lightning text-success fs-2"></i>
                            </div>
                            <div class="small text-muted mb-1">Resolución Más Rápida</div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                @if(!empty($timeKpis['global']['fastest_resolution_hours']))
                                    {{ $timeKpis['global']['fastest_resolution_hours'] }} h
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                            <small class="text-muted">Ticket atendido en menos tiempo</small>
                        </div>

                        <div class="col-6 col-md-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-hourglass-bottom text-warning fs-2"></i>
                            </div>
                            <div class="small text-muted mb-1">Resolución Más Lenta</div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                @if(!empty($timeKpis['global']['slowest_resolution_hours']))
                                    {{ $timeKpis['global']['slowest_resolution_hours'] }} h
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                            <small class="text-muted">Caso que más demoró</small>
                        </div>

                        <div class="col-6 col-md-3 text-center">
                            <div class="mb-2">
                                <i class="bi bi-clock-history text-info fs-2"></i>
                            </div>
                            <div class="small text-muted mb-1">Promedio en Cola</div>
                            <div class="h5 mb-0 fw-bold text-dark">
                                @if(!empty($timeKpis['open']['avg_open_age_hours']))
                                    {{ $timeKpis['open']['avg_open_age_hours'] }} h
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                            <small class="text-muted">Tiempo promedio en abiertos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Volumen reciente --}}
        <div class="col-lg-4">
            <div class="mb-2">
                <span class="section-title">Volumen Reciente</span>
            </div>
            <div class="card card-elegant h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                        <div>
                            <div class="small text-muted">Tickets Creados Hoy</div>
                            <div class="h3 mb-0 fw-bold text-primary">{{ $volumeKpis['today_created'] ?? 0 }}</div>
                        </div>
                        <span class="badge bg-primary text-white p-2 rounded-circle">
                            <i class="bi bi-calendar-day fs-5"></i>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div>
                            <div class="small text-muted">Tickets Últimos 7 Días</div>
                            <div class="h3 mb-0 fw-bold text-info">{{ $volumeKpis['last7_created'] ?? 0 }}</div>
                        </div>
                        <span class="badge bg-info text-white p-2 rounded-circle">
                            <i class="bi bi-calendar-week fs-5"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILA 3: Gráficas más compactas --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card card-elegant h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="section-title">Tendencia de Tickets (Últimos 14 Días)</span>
                        <div class="text-muted small">
                            <i class="bi bi-graph-up me-1"></i>
                            Vista temporal
                        </div>
                    </div>
                    <canvas id="ticketsDailyChart" class="compact-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-elegant h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="section-title">Distribución por Categoría</span>
                        <div class="text-muted small">
                            <i class="bi bi-pie-chart me-1"></i>
                            Vista categórica
                        </div>
                    </div>
                    <canvas id="ticketsCategoryChart" class="compact-chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- FILA 4: Rendimiento por soporte IT + Top categorías --}}
    <div class="row g-3">
        {{-- Tabla IT MEJORADA --}}
        <div class="col-lg-8">
            <div class="mb-2">
                <span class="section-title">Rendimiento del Equipo de Soporte</span>
            </div>
            <div class="card card-elegant">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table performance-table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-start ps-3">Soporte</th>
                                    <th>Abiertos</th>
                                    <th>Asignados</th>
                                    <th>En Prog.</th>
                                    <th>Resueltos</th>
                                    <th>Cerrados</th>
                                    <th>Calificación</th>
                                    <th>Resoluciones</th>
                                    <th class="pe-3">Tiempo Prom.</th>
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
                                        <td class="user-name ps-3">
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
                                                            
                                                        @elseif($i - 0.5 <= $avg)
                                                            
                                                        @else
                                                            
                                                        @endif
                                                    @endfor
                                                </div>
                                                <small class="text-muted">{{ number_format($avg, 1) }}/5.0</small>
                                            @else
                                                <span class="text-muted small">Sin calificaciones</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Resoluciones --}}
                                        <td>
                                            <span class="stat-number text-primary">{{ $totalResolved }}</span>
                                        </td>
                                        
                                        {{-- Tiempo promedio --}}
                                        <td class="pe-3">
                                            @if($userTime && $userTime['avg_resolution_hours'] !== null)
                                                <span class="stat-number">{{ $userTime['avg_resolution_hours'] }}h</span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="bi bi-people fs-4 d-block mb-2"></i>
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

            <div class="mb-2">
                <span class="section-title">Categorías con Más Tickets</span>
            </div>
            <div class="card card-elegant">
                <div class="card-body">
                    @forelse($topCategories ?? [] as $c)
                        @php
                            $pct = $maxTickets ? ($c->tickets_count / $maxTickets * 100) : 0;
                            $url = route('tickets.index', ['category_id' => $c->id]);
                        @endphp
                        <a href="{{ $url }}" class="d-block text-decoration-none mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-truncate text-dark">{{ $c->name }}</strong>
                                <span class="text-muted small">{{ $c->tickets_count }} tickets</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ number_format($pct, 2) }}%;"
                                     aria-valuenow="{{ number_format($pct, 2) }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                            Sin datos de categorías.
                        </div>
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
                    tension: 0.4,
                    borderWidth: 2,
                    borderColor: 'rgba(44, 90, 160, 1)',
                    backgroundColor: 'rgba(44, 90, 160, 0.1)',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgba(44, 90, 160, 1)',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleFont: {
                            size: 12
                        },
                        bodyFont: {
                            size: 11
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
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
                        '#2c5aa0', // azul principal
                        '#10b981', // verde
                        '#f59e0b', // amarillo/naranja
                        '#8b5cf6', // violeta
                        '#ef4444', // rojo
                        '#06b6d4'  // cian
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 11
                            },
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleFont: {
                            size: 12
                        },
                        bodyFont: {
                            size: 11
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
</script>
@endpush
@endsection