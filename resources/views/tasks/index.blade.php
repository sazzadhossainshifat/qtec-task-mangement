<x-app-layout>
    <x-slot name="title">My Tasks — Task Manager</x-slot>

    <!-- Filter Bar -->
    <div class="task-card mb-3">
        <div class="p-3">
            <form method="GET" action="{{ route('tasks.index') }}" class="row g-2 align-items-end" id="filter-form">
                <div class="col-auto">
                    <label class="form-label mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm" id="filter-status" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-1">Priority</label>
                    <select name="priority" class="form-select form-select-sm" id="filter-priority" onchange="this.form.submit()">
                        <option value="">All Priorities</option>
                        @foreach($priorities as $key => $label)
                            <option value="{{ $key }}" {{ request('priority') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @if(request('status') || request('priority'))
                <div class="col-auto">
                    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary" id="btn-clear-filters">
                        <i class="bi bi-x-lg me-1"></i>Clear
                    </a>
                </div>
                @endif
                <div class="col-auto ms-auto">
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm" id="btn-add-task">
                        <i class="bi bi-plus-lg me-1"></i>Add Task
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Task Table -->
    <div class="task-card">
        <div class="card-header-custom">
            <h2>
                Tasks
                @if(request('status') || request('priority'))
                    <span class="ms-2 badge-in_progress" style="font-size: 0.7rem;">Filtered</span>
                @endif
            </h2>
            <span class="text-sm text-slate-500">{{ $tasks->total() }} task(s)</span>
        </div>

        @if($tasks->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-clipboard-check" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                <p class="mt-3 text-muted" style="font-size: 0.875rem;">
                    {{ request('status') || request('priority') ? 'No tasks match your filters.' : 'No tasks yet. Create your first one!' }}
                </p>
                @if(!request('status') && !request('priority'))
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm mt-1">
                        <i class="bi bi-plus-lg me-1"></i>Create Task
                    </a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Team</th>
                            <th>Due Date</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $i => $task)
                        <tr id="task-row-{{ $task->id }}">
                            <td class="text-muted" style="font-size: 0.8rem;">
                                {{ ($tasks->currentPage() - 1) * $tasks->perPage() + $i + 1 }}
                            </td>
                            <td>
                                <div class="fw-medium">{{ $task->title }}</div>
                                @if($task->description)
                                    <div class="text-muted" style="font-size: 0.75rem; margin-top: 2px;">
                                        {{ Str::limit($task->description, 60) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <!-- Quick status toggle -->
                                <div class="dropdown">
                                    <span class="badge-{{ $task->status }} cursor-pointer"
                                          data-bs-toggle="dropdown"
                                          style="cursor: pointer;"
                                          id="status-badge-{{ $task->id }}">
                                        {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                        <i class="bi bi-chevron-down ms-1" style="font-size: 0.6rem;"></i>
                                    </span>
                                    <ul class="dropdown-menu dropdown-menu-sm shadow-sm border-0"
                                        style="min-width: 130px; font-size: 0.8rem;">
                                        @foreach($statuses as $key => $label)
                                            @if($key !== $task->status)
                                            <li>
                                                <a class="dropdown-item py-1 status-change-btn"
                                                   href="#"
                                                   data-task-id="{{ $task->id }}"
                                                   data-status="{{ $key }}"
                                                   id="status-{{ $task->id }}-{{ $key }}">
                                                    {{ $label }}
                                                </a>
                                            </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <span class="badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column" style="font-size: 0.75rem;">
                                    <span class="text-muted d-flex align-items-center gap-1">
                                        <i class="bi bi-person-fill"></i>
                                        By: {{ $task->user_id === Auth::id() ? 'You' : $task->creator->name }}
                                    </span>
                                    @if($task->assigned_to_id)
                                    <span class="text-blue-600 d-flex align-items-center gap-1 mt-1">
                                        <i class="bi bi-person-check-fill"></i>
                                        For: {{ $task->assigned_to_id === Auth::id() ? 'You' : $task->assignee->name }}
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($task->due_date)
                                    <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger fw-medium' : 'text-muted' }}"
                                          style="font-size: 0.8rem;">
                                        {{ $task->due_date->format('M d, Y') }}
                                        @if($task->due_date->isToday())
                                            <span class="badge-medium ms-1">Today</span>
                                        @elseif($task->due_date->isPast() && $task->status !== 'completed')
                                            <i class="bi bi-exclamation-circle ms-1" title="Overdue"></i>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 0.8rem;">—</span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size: 0.8rem;">
                                {{ $task->created_at->format('M d') }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    @if($task->user_id === Auth::id())
                                        <a href="{{ route('tasks.edit', $task) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit"
                                           id="btn-edit-{{ $task->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                              onsubmit="return confirm('Delete this task?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete"
                                                    id="btn-delete-{{ $task->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-lock-fill me-1"></i>Read Only</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($tasks->hasPages())
            <div class="px-4 py-3 border-top d-flex justify-content-end">
                {{ $tasks->links() }}
            </div>
            @endif
        @endif
    </div>

    @push('scripts')
    <script>
        // AJAX Quick Status Change
        document.querySelectorAll('.status-change-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                const taskId = this.dataset.taskId;
                const newStatus = this.dataset.status;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status: newStatus }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update badge text and class
                        const badge = document.getElementById(`status-badge-${taskId}`);
                        const labelMap = { pending: 'Pending', in_progress: 'In Progress', completed: 'Completed' };
                        // Remove old badge class
                        badge.className = `badge-${newStatus} cursor-pointer`;
                        badge.style.cursor = 'pointer';
                        badge.setAttribute('data-bs-toggle', 'dropdown');
                        badge.innerHTML = `${labelMap[newStatus]} <i class="bi bi-chevron-down ms-1" style="font-size: 0.6rem;"></i>`;

                        // Show a small toast
                        showToast('Status updated to ' + labelMap[newStatus]);
                    }
                })
                .catch(err => console.error('Status update failed', err));
            });
        });

        function showToast(message) {
            const toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;background:#1e293b;color:#fff;padding:0.65rem 1.1rem;border-radius:0.5rem;font-size:0.8rem;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.15);transition:opacity 0.3s;';
            toast.innerHTML = `<i class="bi bi-check-circle-fill me-2" style="color:#4ade80;"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
        }
    </script>
    @endpush
</x-app-layout>
