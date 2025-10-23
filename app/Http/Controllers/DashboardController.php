<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Budget;
use App\Models\Goal;
use App\Models\Insight;
use App\Services\AIInsightService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(
        private AIInsightService $aiInsightService
    ) {}

    public function index()
    {
        $user = auth()->user();
        $now = Carbon::now();
        
        // Get current month data
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        // Get current week data
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        
        // Calculate totals
        $totalIncome = $user->transactions()
            ->income()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
            
        $totalExpenses = $user->transactions()
            ->expense()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
            
        $netSavings = $totalIncome - $totalExpenses;
        
        // Active budgets
        $activeBudgets = $user->budgets()
            ->with('category')
            ->current()
            ->active()
            ->get()
            ->map(function ($budget) {
                return [
                    'id' => $budget->id,
                    'category_name' => $budget->category->name,
                    'amount' => $budget->amount,
                    'spent_amount' => $budget->spent_amount,
                    'remaining_amount' => $budget->remaining_amount,
                    'percentage_used' => round($budget->percentage_used, 1),
                    'color' => $budget->category->color,
                ];
            });
        
        // Active goals
        $activeGoals = $user->goals()
            ->active()
            ->get()
            ->map(function ($goal) {
                return [
                    'id' => $goal->id,
                    'title' => $goal->title,
                    'target_amount' => $goal->target_amount,
                    'current_amount' => $goal->current_amount,
                    'progress_percentage' => round($goal->progress_percentage, 1),
                    'remaining_amount' => $goal->remaining_amount,
                    'days_remaining' => $goal->days_remaining,
                ];
            });
        
        // Recent transactions
        $recentTransactions = $user->transactions()
            ->with('category')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'note' => $transaction->note,
                    'date' => $transaction->date->format('M j, Y'),
                    'category_name' => $transaction->category->name,
                    'category_color' => $transaction->category->color,
                ];
            });
        
        // Latest insights
        $latestInsights = $user->insights()
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($insight) {
                return [
                    'id' => $insight->id,
                    'title' => $insight->title,
                    'content' => $insight->content,
                    'type' => $insight->type,
                    'created_at' => $insight->created_at->format('M j, Y'),
                    'is_read' => $insight->is_read,
                ];
            });
        
        // Chart data for spending by category
        $spendingByCategory = $user->transactions()
            ->expense()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function ($transactions, $categoryName) {
                return [
                    'category' => $categoryName,
                    'amount' => $transactions->sum('amount'),
                    'color' => $transactions->first()->category->color,
                ];
            })
            ->values();
        
        // Monthly trend data (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $income = $user->transactions()
                ->income()
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');
                
            $expenses = $user->transactions()
                ->expense()
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $monthlyTrend[] = [
                'month' => $month->format('M Y'),
                'income' => $income,
                'expenses' => $expenses,
                'net' => $income - $expenses,
            ];
        }
        
        return Inertia::render('Dashboard', [
            'overview' => [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'net_savings' => $netSavings,
                'active_budgets_count' => $activeBudgets->count(),
            ],
            'active_budgets' => $activeBudgets,
            'active_goals' => $activeGoals,
            'recent_transactions' => $recentTransactions,
            'latest_insights' => $latestInsights,
            'chart_data' => [
                'spending_by_category' => $spendingByCategory,
                'monthly_trend' => $monthlyTrend,
            ],
        ]);
    }
    
    public function generateInsights()
    {
        $user = auth()->user();
        
        // Generate weekly insights
        $this->aiInsightService->generateWeeklyInsights($user);
        
        return redirect()->back()->with('success', 'AI insights generated successfully!');
    }
}
