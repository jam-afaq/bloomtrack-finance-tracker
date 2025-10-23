<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Services\AIInsightService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InsightController extends Controller
{
    public function __construct(
        private AIInsightService $aiInsightService
    ) {}

    public function index()
    {
        $user = auth()->user();
        $insights = $user->insights()
            ->latest()
            ->paginate(10)
            ->through(function ($insight) {
                return [
                    'id' => $insight->id,
                    'title' => $insight->title,
                    'content' => $insight->content,
                    'type' => $insight->type,
                    'is_read' => $insight->is_read,
                    'created_at' => $insight->created_at->format('M j, Y'),
                ];
            });

        return Inertia::render('Insights/Index', [
            'insights' => $insights,
        ]);
    }

    public function show(Insight $insight)
    {
        $this->authorize('view', $insight);
        
        // Mark as read
        if (!$insight->is_read) {
            $insight->update(['is_read' => true]);
        }
        
        return Inertia::render('Insights/Show', [
            'insight' => [
                'id' => $insight->id,
                'title' => $insight->title,
                'content' => $insight->content,
                'type' => $insight->type,
                'data' => $insight->data,
                'created_at' => $insight->created_at->format('M j, Y'),
            ],
        ]);
    }

    public function generateWeekly()
    {
        $user = auth()->user();
        
        $this->aiInsightService->generateWeeklyInsights($user);
        
        return redirect()->route('insights.index')
            ->with('success', 'Weekly insights generated successfully!');
    }

    public function generateMonthly()
    {
        $user = auth()->user();
        
        $this->aiInsightService->generateMonthlyInsights($user);
        
        return redirect()->route('insights.index')
            ->with('success', 'Monthly insights generated successfully!');
    }

    public function markAsRead(Insight $insight)
    {
        $this->authorize('update', $insight);
        
        $insight->update(['is_read' => true]);
        
        return redirect()->back()
            ->with('success', 'Insight marked as read!');
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        
        $user->insights()->unread()->update(['is_read' => true]);
        
        return redirect()->back()
            ->with('success', 'All insights marked as read!');
    }

    public function destroy(Insight $insight)
    {
        $this->authorize('delete', $insight);
        
        $insight->delete();
        
        return redirect()->route('insights.index')
            ->with('success', 'Insight deleted successfully!');
    }
}
