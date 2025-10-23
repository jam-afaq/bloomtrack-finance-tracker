<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = $user->transactions()->with('category');
        
        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('note', 'like', '%' . $request->search . '%')
                  ->orWhereHas('category', function ($categoryQuery) use ($request) {
                      $categoryQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $transactions = $query->latest()->paginate(20);
        
        $categories = $user->categories()->orderBy('name')->get();
        
        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'filters' => $request->only(['type', 'category_id', 'date_from', 'date_to', 'search']),
        ]);
    }
    
    public function create()
    {
        $user = auth()->user();
        $categories = $user->categories()->orderBy('name')->get();
        
        return Inertia::render('Transactions/Create', [
            'categories' => $categories,
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'note' => 'nullable|string|max:255',
            'date' => 'required|date',
            'recurring' => 'boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurring_end_date' => 'nullable|date|after:date',
            'tags' => 'nullable|array',
        ]);
        
        $user = auth()->user();
        
        // Verify category belongs to user
        $category = $user->categories()->findOrFail($request->category_id);
        
        $transaction = $user->transactions()->create([
            'type' => $request->type,
            'amount' => $request->amount,
            'category_id' => $request->category_id,
            'note' => $request->note,
            'date' => $request->date,
            'recurring' => $request->boolean('recurring'),
            'recurring_frequency' => $request->recurring_frequency,
            'recurring_end_date' => $request->recurring_end_date,
            'tags' => $request->tags,
        ]);
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully!');
    }
    
    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        
        $transaction->load('category');
        
        return Inertia::render('Transactions/Show', [
            'transaction' => $transaction,
        ]);
    }
    
    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $user = auth()->user();
        $categories = $user->categories()->orderBy('name')->get();
        
        return Inertia::render('Transactions/Edit', [
            'transaction' => $transaction,
            'categories' => $categories,
        ]);
    }
    
    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'note' => 'nullable|string|max:255',
            'date' => 'required|date',
            'recurring' => 'boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurring_end_date' => 'nullable|date|after:date',
            'tags' => 'nullable|array',
        ]);
        
        $user = auth()->user();
        
        // Verify category belongs to user
        $category = $user->categories()->findOrFail($request->category_id);
        
        $transaction->update([
            'type' => $request->type,
            'amount' => $request->amount,
            'category_id' => $request->category_id,
            'note' => $request->note,
            'date' => $request->date,
            'recurring' => $request->boolean('recurring'),
            'recurring_frequency' => $request->recurring_frequency,
            'recurring_end_date' => $request->recurring_end_date,
            'tags' => $request->tags,
        ]);
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully!');
    }
    
    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        
        $transaction->delete();
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully!');
    }
    
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);
        
        $user = auth()->user();
        $file = $request->file('file');
        $csvData = array_map('str_getcsv', file($file->getPathname()));
        $header = array_shift($csvData);
        
        $imported = 0;
        $errors = [];
        
        foreach ($csvData as $row) {
            $data = array_combine($header, $row);
            
            try {
                // Find or create category
                $category = $user->categories()->firstOrCreate(
                    ['name' => $data['category'] ?? 'Imported'],
                    [
                        'type' => $data['type'] ?? 'expense',
                        'color' => '#3B82F6',
                        'is_default' => false,
                    ]
                );
                
                $user->transactions()->create([
                    'type' => $data['type'] ?? 'expense',
                    'amount' => $data['amount'] ?? 0,
                    'category_id' => $category->id,
                    'note' => $data['note'] ?? null,
                    'date' => $data['date'] ?? now()->format('Y-m-d'),
                ]);
                
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($imported + count($errors) + 1) . ": " . $e->getMessage();
            }
        }
        
        $message = "Imported {$imported} transactions successfully.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " errors occurred.";
        }
        
        return redirect()->route('transactions.index')
            ->with('success', $message)
            ->with('errors', $errors);
    }
}
