<?php

namespace App\Http\Controllers;

use App\Models\{Ticket, Category};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        abort_unless(Auth::user()->isManager(), 403);

        $stats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status','open')->count(),
            'assigned' => Ticket::where('status','assigned')->count(),
            'in_progress' => Ticket::where('status','in_progress')->count(),
            'resolved' => Ticket::where('status','resolved')->count(),
            'closed' => Ticket::where('status','closed')->count(),
        ];

        $topCategories = Category::withCount('tickets')
            ->orderByDesc('tickets_count')->take(5)->get();

        $commonIssues = $topCategories->map(
            fn($c)=>['category'=>$c->name,'count'=>$c->tickets_count]
        );

        return view('dashboard.index', compact('stats','commonIssues'));
    }
}