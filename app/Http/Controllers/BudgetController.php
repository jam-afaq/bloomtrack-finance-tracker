<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $budgets = $user->budgets()
            ->with('category')
            ->latest()
            ->get()
            ->map(function ($budget) {
                return [
                    'id' => $budget->id,
                    'category_name' => $budget->category->name,
                    'category_color' => $budget->category->color,
                    'amount' => $budget->amount,
                    'start_date' => $budget->start_date->format('M j, Y'),
                    'end_date' => $budget->end_date->format('M j, Y'),
                    'spent_amount' => $budget->spent_amount,
                    'remaining_amount' => $budget->remaining_amount,
                    'percentage_used' => round($budget->percentage_used, 1),
                    'is_active' => $budget->is_active,
                    'is_over_budget' => $budget->percentage_used > 100,
                    'is_warning' => $budget->percentage_used > 80 && $budget->percentage_used <= 100,
                ];
            });

        return Inertia::render('Budgets/Index', [
            'budgets' => $budgets,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $categories = $user->categories()
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        return Inertia::render('Budgets/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $user = auth()->user();
        
        // Verify category belongs to user
        $category = $user->categories()->findOrFail($request->category_id);
        
        // Check for overlapping budgets
        $overlapping = $user->budgets()
            ->where('category_id', $request->category_id)
            ->where('is_active', true)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })
            ->exists();

        if ($overlapping) {
            return redirect()->back()
                ->withErrors(['category_id' => 'You already have an active budget for this category during this period.']);
        }
        
        $budget = $user->budgets()->create([
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget created successfully!');
    }

    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);
        
        $budget->load('category');
        
        return Inertia::render('Budgets/Show', [
            'budget' => [
                'id' => $budget->id,
                'category_name' => $budget->category->name,
                'category_color' => $budget->category->color,
                'amount' => $budget->amount,
                'start_date' => $budget->start_date->format('M j, Y'),
                'end_date' => $budget->end_date->format('M j, Y'),
                'spent_amount' => $budget->spent_amount,
                'remaining_amount' => $budget->remaining_amount,
                'percentage_used' => round($budget->percentage_used, 1),
                'is_active' => $budget->is_active,
            ],
        ]);
    }

    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $user = auth()->user();
        $categories = $user->categories()
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Budgets/Edit', [
            'budget' => $budget,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $user = auth()->user();
        
        // Verify category belongs to user
        $category = $user->categories()->findOrFail($request->category_id);
        
        $budget->update([
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget updated successfully!');
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        
        $budget->delete();
        
        return redirect()->route('budgets.index')
            ->with('success', 'Budget deleted successfully!');
    }
}
