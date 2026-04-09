<x-app-layout>
    <x-slot name="title">Dashboard — Task Manager</x-slot>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="stat-icon" style="background:#f0f9ff;">
                        <i class="bi bi-stack" style="color:#0284c7;"></i>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">All</span>
                </div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Tasks</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="stat-icon" style="background:#fffbeb;">
                        <i class="bi bi-hourglass-split" style="color:#d97706;"></i>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">Open</span>
                </div>
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="stat-icon" style="background:#eff6ff;">
                        <i class="bi bi-arrow-repeat" style="color:#2563eb;"></i>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">Active</span>
                </div>
                <div class="stat-value">{{ $stats['in_progress'] }}</div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="stat-icon" style="background:#f0fdf4;">
                        <i class="bi bi-check2-all" style="color:#16a34a;"></i>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">Done</span>
                </div>
                <div class="stat-value">{{ $stats['completed'] }}</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    @if($stats['total'] > 0)
    <div class="task-card mb-4">
        <div class="card-header-custom">
            <h2>Overall Progress</h2>
            <span class="text-sm text-slate-500">
                {{ $stats['completed'] }} of {{ $stats['total'] }} tasks done
            </span>
        </div>
        <div class="p-4">
            @php
                $pct = round(($stats['completed'] / $stats['total']) * 100);
            @endphp
            <div class="d-flex justify-content-between mb-1">
                <small class="text-muted">Completion rate</small>
                <small class="fw-semibold">{{ $pct }}%</small>
            </div>
            <div class="progress" style="height: 8px; border-radius: 999px;">
                <div class="progress-bar bg-success" role="progressbar"
                     style="width: {{ $pct }}%; border-radius: 999px;"
                     aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
            <div class="d-flex gap-3 mt-3">
                <span class="badge-pending">⬤ Pending: {{ $stats['pending'] }}</span>
                <span class="badge-in_progress">⬤ In Progress: {{ $stats['in_progress'] }}</span>
                <span class="badge-completed">⬤ Completed: {{ $stats['completed'] }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Tasks -->
    <div class="task-card">
        <div class="card-header-custom">
            <h2>Recent Tasks</h2>
            <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary" id="btn-view-all-tasks">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        @if($recentTasks->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                <p class="mt-3 text-muted" style="font-size: 0.875rem;">No tasks yet. Create your first task!</p>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm mt-1" id="btn-create-first">
                    <i class="bi bi-plus-lg me-1"></i>Create Task
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTasks as $task)
                        <tr>
                            <td>
                                <div class="fw-medium" style="font-size: 0.875rem;">{{ $task->title }}</div>
                                @if($task->description)
                                    <div class="text-muted" style="font-size: 0.75rem; margin-top: 2px;">
                                        {{ Str::limit($task->description, 50) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge-{{ $task->status }}">
                                    {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-{{ $task->priority }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td>
                                @if($task->due_date)
                                    <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger' : 'text-muted' }}"
                                          style="font-size: 0.8rem;">
                                        {{ $task->due_date->format('M d, Y') }}
                                        @if($task->due_date->isPast() && $task->status !== 'completed')
                                            <i class="bi bi-exclamation-circle ms-1"></i>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 0.8rem;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($task->user_id === Auth::id())
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @else
                                    <span class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-lock-fill"></i></span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
