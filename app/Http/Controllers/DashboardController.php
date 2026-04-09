<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Tasks involving the user (Creator OR Assignee)
        $baseQuery = Task::where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('assigned_to_id', $userId);
        });

        $stats = [
            'total'       => (clone $baseQuery)->count(),
            'pending'     => (clone $baseQuery)->where('status', Task::STATUS_PENDING)->count(),
            'in_progress' => (clone $baseQuery)->where('status', Task::STATUS_IN_PROGRESS)->count(),
            'completed'   => (clone $baseQuery)->where('status', Task::STATUS_COMPLETED)->count(),
        ];

        $recentTasks = (clone $baseQuery)
            ->with(['creator', 'assignee'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recentTasks'));
    }
}
