<x-app-layout>
    <x-slot name="title">Create Task — Task Manager</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="task-card">
                <div class="card-header-custom">
                    <h2><i class="bi bi-plus-circle me-2 text-blue-500"></i>Create New Task</h2>
                    <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-secondary" id="btn-back-from-create">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>

                <div class="p-4">
                    <form method="POST" action="{{ route('tasks.store') }}" id="create-task-form">
                        @csrf

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Task Title <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}"
                                   placeholder="e.g. Review pull request #42"
                                   autofocus>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description"
                                      id="description"
                                      rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Optional — add more details about this task">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status & Priority -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    @foreach($statuses as $key => $label)
                                        <option value="{{ $key }}" {{ old('status', 'pending') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror">
                                    @foreach($priorities as $key => $label)
                                        <option value="{{ $key }}" {{ old('priority', 'medium') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date"
                                       name="due_date"
                                       id="due_date"
                                       class="form-control @error('due_date') is-invalid @enderror"
                                       value="{{ old('due_date') }}"
                                       min="{{ now()->toDateString() }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="assigned_to_id" class="form-label">Assign To</label>
                                <select name="assigned_to_id" id="assigned_to_id" class="form-select @error('assigned_to_id') is-invalid @enderror">
                                    <option value="">Myself</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btn-save-task">
                                <i class="bi bi-check-lg me-1"></i>Create Task
                            </button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary" id="btn-cancel-create">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
