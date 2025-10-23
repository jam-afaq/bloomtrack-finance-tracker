<?php

namespace App\Services;

use App\Models\Insight;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class AIInsightService
{
    public function generateWeeklyInsights(User $user): Insight
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $transactions = $user->transactions()
            ->with('category')
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->get();

        $previousWeekStart = $startOfWeek->copy()->subWeek();
        $previousWeekEnd = $endOfWeek->copy()->subWeek();
        
        $previousWeekTransactions = $user->transactions()
            ->with('category')
            ->whereBetween('date', [$previousWeekStart, $previousWeekEnd])
            ->get();

        $spendingData = $this->analyzeSpendingPatterns($transactions, $previousWeekTransactions);
        
        // Generate intelligent insights without OpenAI
        return Insight::create([
            'user_id' => $user->id,
            'title' => 'Weekly Financial Insights',
            'content' => $this->generateIntelligentInsight($spendingData, 'weekly'),
            'type' => 'weekly',
            'data' => $spendingData,
        ]);
    }

    public function generateMonthlyInsights(User $user): Insight
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $transactions = $user->transactions()
            ->with('category')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $previousMonthStart = $startOfMonth->copy()->subMonth();
        $previousMonthEnd = $endOfMonth->copy()->subMonth();
        
        $previousMonthTransactions = $user->transactions()
            ->with('category')
            ->whereBetween('date', [$previousMonthStart, $previousMonthEnd])
            ->get();

        $spendingData = $this->analyzeSpendingPatterns($transactions, $previousMonthTransactions);
        
        // Generate intelligent insights without OpenAI
        return Insight::create([
            'user_id' => $user->id,
            'title' => 'Monthly Financial Report',
            'content' => $this->generateIntelligentInsight($spendingData, 'monthly'),
            'type' => 'monthly',
            'data' => $spendingData,
        ]);
    }

    private function analyzeSpendingPatterns($currentTransactions, $previousTransactions)
    {
        $currentIncome = $currentTransactions->where('type', 'income')->sum('amount');
        $currentExpenses = $currentTransactions->where('type', 'expense')->sum('amount');
        $previousIncome = $previousTransactions->where('type', 'income')->sum('amount');
        $previousExpenses = $previousTransactions->where('type', 'expense')->sum('amount');

        $currentByCategory = $currentTransactions->where('type', 'expense')
            ->groupBy('category.name')
            ->map->sum('amount');

        $previousByCategory = $previousTransactions->where('type', 'expense')
            ->groupBy('category.name')
            ->map->sum('amount');

        return [
            'current_period' => [
                'income' => $currentIncome,
                'expenses' => $currentExpenses,
                'net' => $currentIncome - $currentExpenses,
                'by_category' => $currentByCategory,
            ],
            'previous_period' => [
                'income' => $previousIncome,
                'expenses' => $previousExpenses,
                'net' => $previousIncome - $previousExpenses,
                'by_category' => $previousByCategory,
            ],
            'changes' => [
                'income_change' => $previousIncome > 0 ? (($currentIncome - $previousIncome) / $previousIncome) * 100 : 0,
                'expense_change' => $previousExpenses > 0 ? (($currentExpenses - $previousExpenses) / $previousExpenses) * 100 : 0,
                'category_changes' => $this->calculateCategoryChanges($currentByCategory, $previousByCategory),
            ]
        ];
    }

    private function calculateCategoryChanges($current, $previous)
    {
        $changes = [];
        $allCategories = array_unique(array_merge($current->keys()->toArray(), $previous->keys()->toArray()));
        
        foreach ($allCategories as $category) {
            $currentAmount = $current[$category] ?? 0;
            $previousAmount = $previous[$category] ?? 0;
            
            if ($previousAmount > 0) {
                $changes[$category] = (($currentAmount - $previousAmount) / $previousAmount) * 100;
            } else {
                $changes[$category] = $currentAmount > 0 ? 100 : 0;
            }
        }
        
        return $changes;
    }

    private function generateIntelligentInsight($spendingData, $period = 'weekly')
    {
        $current = $spendingData['current_period'];
        $previous = $spendingData['previous_period'];
        $changes = $spendingData['changes'];
        
        $insights = [];
        
        // Header
        $periodName = $period === 'weekly' ? 'Week' : 'Month';
        $insights[] = "📊 **Your {$periodName}ly Financial Summary**\n";
        
        // Overview
        $insights[] = "**Overview:**";
        $insights[] = "• Income: $" . number_format($current['income'], 2);
        $insights[] = "• Expenses: $" . number_format($current['expenses'], 2);
        $insights[] = "• Net Savings: $" . number_format($current['net'], 2);
        
        // Savings rate
        if ($current['income'] > 0) {
            $savingsRate = ($current['net'] / $current['income']) * 100;
            $insights[] = "• Savings Rate: " . number_format($savingsRate, 1) . "%\n";
        } else {
            $insights[] = "";
        }
        
        // Trend analysis
        $insights[] = "**Trends:**";
        
        // Income trend
        if ($changes['income_change'] > 5) {
            $insights[] = "✅ Income increased by " . number_format($changes['income_change'], 1) . "% - Great work!";
        } elseif ($changes['income_change'] < -5) {
            $insights[] = "⚠️ Income decreased by " . number_format(abs($changes['income_change']), 1) . "% - Consider additional income sources.";
        }
        
        // Expense trend
        if ($changes['expense_change'] > 15) {
            $insights[] = "⚠️ Expenses increased by " . number_format($changes['expense_change'], 1) . "% - Review your spending habits.";
        } elseif ($changes['expense_change'] > 5) {
            $insights[] = "⚡ Expenses increased by " . number_format($changes['expense_change'], 1) . "% - Monitor closely.";
        } elseif ($changes['expense_change'] < -5) {
            $insights[] = "✅ Expenses decreased by " . number_format(abs($changes['expense_change']), 1) . "% - Excellent progress!";
        } else {
            $insights[] = "📈 Expenses remained stable compared to last period.";
        }
        
        // Category insights
        if ($current['by_category']->isNotEmpty()) {
            $insights[] = "\n**Spending Breakdown:**";
            $topCategories = $current['by_category']->sortDesc()->take(3);
            foreach ($topCategories as $category => $amount) {
                $percentage = $current['expenses'] > 0 ? ($amount / $current['expenses']) * 100 : 0;
                $insights[] = "• {$category}: $" . number_format($amount, 2) . " (" . number_format($percentage, 1) . "%)";
            }
        }
        
        // Recommendations
        $insights[] = "\n**Recommendations:**";
        
        if ($current['net'] < 0) {
            $insights[] = "🎯 Your expenses exceed income. Focus on reducing discretionary spending.";
        } elseif ($current['net'] > 0 && $current['income'] > 0) {
            $savingsRate = ($current['net'] / $current['income']) * 100;
            if ($savingsRate < 10) {
                $insights[] = "🎯 Try to increase your savings rate to at least 20% of income.";
            } elseif ($savingsRate >= 20) {
                $insights[] = "🌟 Excellent savings rate! Consider investing your surplus.";
            }
        }
        
        // Category-specific recommendations
        if ($current['by_category']->isNotEmpty()) {
            $topCategory = $current['by_category']->sortDesc()->first();
            $topCategoryName = $current['by_category']->sortDesc()->keys()->first();
            $topCategoryPercentage = $current['expenses'] > 0 ? ($topCategory / $current['expenses']) * 100 : 0;
            
            if ($topCategoryPercentage > 40) {
                $insights[] = "💡 {$topCategoryName} represents " . number_format($topCategoryPercentage, 1) . "% of spending. Look for ways to optimize this category.";
            }
        }
        
        // Check for significant category changes
        foreach ($changes['category_changes'] as $category => $change) {
            if ($change > 50) {
                $insights[] = "⚠️ Spending in {$category} increased significantly. Review recent transactions.";
                break;
            }
        }
        
        return implode("\n", $insights);
    }
}
