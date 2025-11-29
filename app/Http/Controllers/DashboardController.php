<?php

namespace App\Http\Controllers;

use App\Models\{Ticket, Category, User};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Si NO es manager → lo mandamos al listado con mensaje amigable
        if (!$user || !$user->isManager()) {
            return redirect()
                ->route('tickets.index')
                ->with('error', 'No tienes permisos para ver el Panel de Control.');
        }

        // Estadísticas básicas de tickets por estado
        $stats = [
            'total'       => Ticket::count(),
            'open'        => Ticket::where('status', 'open')->count(),
            'assigned'    => Ticket::where('status', 'assigned')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'resolved'    => Ticket::where('status', 'resolved')->count(),
            'closed'      => Ticket::where('status', 'closed')->count(),
            'cancelled'   => Ticket::where('status', 'cancelled')->count(),
        ];

        // Volumen reciente
        $today     = Carbon::today();
        $last7Days = Carbon::now()->subDays(7);

        $volumeKpis = [
            'today_created' => Ticket::whereDate('created_at', $today)->count(),
            'last7_created' => Ticket::where('created_at', '>=', $last7Days)->count(),
        ];

        // Tickets resueltos/cerrados (para KPIs de tiempo globales)
        $resolvedTickets = Ticket::whereIn('status', ['resolved', 'closed'])
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at')
            ->get(['id', 'created_at', 'updated_at', 'assigned_to']);

        $globalDurations = $resolvedTickets->map(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->updated_at) / 60; // horas
        });

        // KPIs globales
        $timeKpis = [
            'global' => [
                'avg_resolution_hours'     => $globalDurations->count() > 0
                    ? round($globalDurations->avg(), 1)
                    : null,
                'fastest_resolution_hours' => $globalDurations->count() > 0
                    ? round($globalDurations->min(), 1)
                    : null,
                'slowest_resolution_hours' => $globalDurations->count() > 0
                    ? round($globalDurations->max(), 1)
                    : null,
                'resolved_count'           => $resolvedTickets->count(),
            ],
            'open' => [
                'avg_open_age_hours' => null,
            ],
        ];

        // Edad promedio de tickets abiertos
        $openTickets = Ticket::whereIn('status', ['open', 'assigned', 'in_progress'])
            ->whereNotNull('created_at')
            ->get(['id', 'created_at']);

        $openDurations = $openTickets->map(function ($ticket) {
            return $ticket->created_at->diffInMinutes(now()) / 60;
        });

        if ($openDurations->count() > 0) {
            $timeKpis['open']['avg_open_age_hours'] = round($openDurations->avg(), 1);
        }

        // Resumen del mes (Hero)
        $now        = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd   = $now->copy()->endOfMonth();

        $monthTickets = Ticket::whereBetween('created_at', [$monthStart, $monthEnd]);

        $monthResolvedTickets = Ticket::whereBetween('created_at', [$monthStart, $monthEnd])
            ->whereIn('status', ['resolved', 'closed'])
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at')
            ->get(['id', 'created_at', 'updated_at']);

        $monthDurations = $monthResolvedTickets->map(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->updated_at) / 60;
        });

        $monthSummary = [
            'label'                => $monthStart->translatedFormat('F Y') ?? $monthStart->format('F Y'),
            'created'              => $monthTickets->count(),
            'resolved'             => $monthResolvedTickets->count(),
            'avg_resolution_hours' => $monthDurations->count() > 0
                ? round($monthDurations->avg(), 1)
                : null,
            'open_now'             => Ticket::whereIn('status', ['open', 'assigned', 'in_progress'])->count(),
        ];

        // GRÁFICA: tickets por día (últimos 14 días)
        $startDate = Carbon::now()->subDays(13)->startOfDay();
        $endDate   = Carbon::now()->endOfDay();

        $dailyRaw = Ticket::selectRaw("DATE(created_at) as day, COUNT(*) as total")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $labelsDaily = [];
        $dataDaily   = [];

        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            $key           = $cursor->toDateString();
            $labelsDaily[] = $cursor->format('d/m');
            $dataDaily[]   = isset($dailyRaw[$key]) ? $dailyRaw[$key]->total : 0;
            $cursor->addDay();
        }

        $chartDaily = [
            'labels' => $labelsDaily,
            'data'   => $dataDaily,
        ];

        // GRÁFICA: tickets por categoría (Top 5)
        $topCategories = Category::withCount('tickets')
            ->orderByDesc('tickets_count')
            ->take(5)
            ->get();

        $commonIssues = $topCategories->map(function ($category) {
            return [
                'category' => $category->name,
                'count'    => $category->tickets_count,
            ];
        });

        $categoryChart = [
            'labels' => $topCategories->pluck('name')->values(),
            'data'   => $topCategories->pluck('tickets_count')->values(),
        ];

        // Usuarios IT
        $its = User::whereRaw('LOWER(role) = ?', ['it'])
            ->orderBy('name')
            ->get();

        // Estadísticas por técnico
        $itStats = Ticket::selectRaw("
                assigned_to,
                SUM(status = 'open')        as open_cnt,
                SUM(status = 'assigned')    as assigned_cnt,
                SUM(status = 'in_progress') as in_progress_cnt,
                SUM(status = 'resolved')    as resolved_cnt,
                SUM(status = 'closed')      as closed_cnt,
                SUM(status = 'cancelled')   as cancelled_cnt,
                ROUND(AVG(rating), 2)       as avg_rating,
                SUM(rating IS NOT NULL)     as rated_count
            ")
            ->whereIn('assigned_to', $its->pluck('id'))
            ->groupBy('assigned_to')
            ->get()
            ->keyBy('assigned_to');

        // KPIs de tiempo de resolución por técnico
        $perUserTimeKpis = [];
        $resolvedByUser  = $resolvedTickets
            ->whereNotNull('assigned_to')
            ->groupBy('assigned_to');

        foreach ($resolvedByUser as $userId => $ticketsGroup) {
            $durations = $ticketsGroup->map(function ($ticket) {
                return $ticket->created_at->diffInMinutes($ticket->updated_at) / 60;
            });

            $perUserTimeKpis[$userId] = [
                'avg_resolution_hours'     => $durations->count() > 0
                    ? round($durations->avg(), 1)
                    : null,
                'fastest_resolution_hours' => $durations->count() > 0
                    ? round($durations->min(), 1)
                    : null,
                'slowest_resolution_hours' => $durations->count() > 0
                    ? round($durations->max(), 1)
                    : null,
                'resolved_count'           => $ticketsGroup->count(),
            ];
        }

        return view('dashboard.index', [
            'stats'           => $stats,
            'commonIssues'    => $commonIssues,
            'its'             => $its,
            'itStats'         => $itStats,
            'topCategories'   => $topCategories,
            'timeKpis'        => $timeKpis,
            'volumeKpis'      => $volumeKpis,
            'perUserTimeKpis' => $perUserTimeKpis,
            'chartDaily'      => $chartDaily,
            'categoryChart'   => $categoryChart,
            'monthSummary'    => $monthSummary,
        ]);
    }
}

// LISTO