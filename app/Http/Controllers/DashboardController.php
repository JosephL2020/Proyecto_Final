<?php

namespace App\Http\Controllers;

use App\Models\{Ticket, Category, User};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->isManager(), 403);

        $stats = [
            'total'       => Ticket::count(),
            'open'        => Ticket::where('status','open')->count(),
            'assigned'    => Ticket::where('status','assigned')->count(),
            'in_progress' => Ticket::where('status','in_progress')->count(),
            'resolved'    => Ticket::where('status','resolved')->count(),
            'closed'      => Ticket::where('status','closed')->count(),
            'cancelled'   => Ticket::where('status','cancelled')->count(),
        ];

        $topCategories = Category::withCount('tickets')
            ->orderByDesc('tickets_count')->take(5)->get();

        $commonIssues = $topCategories->map(
            fn($c)=>['category'=>$c->name,'count'=>$c->tickets_count]
        );

        $its = User::whereRaw('LOWER(role) = ?', ['it'])
    ->orderBy('name')
    ->get();

        $itStats = Ticket::selectRaw("
            assigned_to,
            SUM(status = 'open')        as open_cnt,
            SUM(status = 'assigned')    as assigned_cnt,
            SUM(status = 'in_progress') as in_progress_cnt,
            SUM(status = 'resolved')    as resolved_cnt,
            SUM(status = 'closed')      as closed_cnt,
            SUM(status = 'cancelled')   as cancelled_cnt,
            ROUND(AVG(rating),2)        as avg_rating,
            SUM(rating IS NOT NULL)     as rated_count
        ")
        ->whereIn('assigned_to', $its->pluck('id'))
        ->groupBy('assigned_to')
        ->get()
        ->keyBy('assigned_to');

      return view('dashboard.index', compact('stats','commonIssues','its','itStats','topCategories'));

    }
}

