<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // Visibility: Creator OR Assignee
        $query = Task::where(function($q) {
                $q->where('user_id', Auth::id())
                  ->orWhere('assigned_to_id', Auth::id());
            })
            ->with(['creator', 'assignee'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks    = $query->paginate(10)->withQueryString();
        $statuses = Task::statusOptions();
        $priorities = Task::priorityOptions();

        return view('tasks.index', compact('tasks', 'statuses', 'priorities'));
    }

    public function create()
    {
        $statuses   = Task::statusOptions();
        $priorities = Task::priorityOptions();
        $users      = User::where('id', '!=', Auth::id())->get();

        return view('tasks.create', compact('statuses', 'priorities', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'status'         => 'required|in:pending,in_progress,completed',
            'priority'       => 'required|in:low,medium,high',
            'due_date'       => 'nullable|date',
            'assigned_to_id' => 'nullable|exists:users,id',
        ]);

        Auth::user()->tasks()->create($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully!');
    }

    public function show(Task $task)
    {
        $this->authorizeAccess($task);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        // ONLY creator can edit core details
        $this->authorizeOwner($task);

        $statuses   = Task::statusOptions();
        $priorities = Task::priorityOptions();
        $users      = User::where('id', '!=', Auth::id())->get();

        return view('tasks.edit', compact('task', 'statuses', 'priorities', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        // ONLY creator can update core details
        $this->authorizeOwner($task);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'status'         => 'required|in:pending,in_progress,completed',
            'priority'       => 'required|in:low,medium,high',
            'due_date'       => 'nullable|date',
            'assigned_to_id' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        // ONLY creator can delete
        $this->authorizeOwner($task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully!');
    }

    public function updateStatus(Request $request, Task $task)
    {
        // Creator OR Assignee can update status
        $this->authorizeAccess($task);

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'status'  => $task->status,
            'message' => 'Status updated!',
        ]);
    }

    /**
     * General access: Creator OR Assignee
     */
    private function authorizeAccess(Task $task): void
    {
        $isAllowed = $task->user_id === Auth::id() || $task->assigned_to_id === Auth::id();
        abort_if(!$isAllowed, 403, 'Unauthorized action.');
    }

    /**
     * Ownership: Only Creator
     */
    private function authorizeOwner(Task $task): void
    {
        abort_if($task->user_id !== Auth::id(), 403, 'Unauthorized. Only the creator can perform this action.');
    }
}
