<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GoalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $goals = $user->goals()
            ->latest()
            ->get()
            ->map(function ($goal) {
                return [
                    'id' => $goal->id,
                    'title' => $goal->title,
                    'description' => $goal->description,
                    'target_amount' => $goal->target_amount,
                    'current_amount' => $goal->current_amount,
                    'target_date' => $goal->target_date->format('M j, Y'),
                    'status' => $goal->status,
                    'progress_percentage' => round($goal->progress_percentage, 1),
                    'remaining_amount' => $goal->remaining_amount,
                    'days_remaining' => $goal->days_remaining,
                    'is_overdue' => $goal->days_remaining < 0 && $goal->status === 'active',
                ];
            });

        return Inertia::render('Goals/Index', [
            'goals' => $goals,
        ]);
    }

    public function create()
    {
        return Inertia::render('Goals/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date' => 'required|date|after:today',
        ]);

        $user = auth()->user();
        
        $goal = $user->goals()->create([
            'title' => $request->title,
            'description' => $request->description,
            'target_amount' => $request->target_amount,
            'current_amount' => $request->current_amount ?? 0,
            'target_date' => $request->target_date,
        ]);

        return redirect()->route('goals.index')
            ->with('success', 'Goal created successfully!');
    }

    public function show(Goal $goal)
    {
        $this->authorize('view', $goal);
        
        return Inertia::render('Goals/Show', [
            'goal' => [
                'id' => $goal->id,
                'title' => $goal->title,
                'description' => $goal->description,
                'target_amount' => $goal->target_amount,
                'current_amount' => $goal->current_amount,
                'target_date' => $goal->target_date->format('M j, Y'),
                'status' => $goal->status,
                'progress_percentage' => round($goal->progress_percentage, 1),
                'remaining_amount' => $goal->remaining_amount,
                'days_remaining' => $goal->days_remaining,
                'created_at' => $goal->created_at->format('M j, Y'),
            ],
        ]);
    }

    public function edit(Goal $goal)
    {
        $this->authorize('update', $goal);
        
        return Inertia::render('Goals/Edit', [
            'goal' => $goal,
        ]);
    }

    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date' => 'required|date',
            'status' => 'required|in:active,completed,paused,cancelled',
        ]);

        $goal->update([
            'title' => $request->title,
            'description' => $request->description,
            'target_amount' => $request->target_amount,
            'current_amount' => $request->current_amount ?? 0,
            'target_date' => $request->target_date,
            'status' => $request->status,
        ]);

        return redirect()->route('goals.index')
            ->with('success', 'Goal updated successfully!');
    }

    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);
        
        $goal->delete();
        
        return redirect()->route('goals.index')
            ->with('success', 'Goal deleted successfully!');
    }

    public function addProgress(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);
        
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $goal->update([
            'current_amount' => $goal->current_amount + $request->amount,
        ]);

        // Auto-complete goal if target is reached
        if ($goal->current_amount >= $goal->target_amount && $goal->status === 'active') {
            $goal->update(['status' => 'completed']);
        }

        return redirect()->back()
            ->with('success', 'Progress added successfully!');
    }
}
