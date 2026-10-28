@extends('layouts.app', ['activeNavigation' => 'tasks'])

@section('title', 'Tasks · Student Portal')
@section('mobile-title', 'Tasks')
@section('mobile-action')
    <a href="#new-task">Add</a>
@endsection

@section('content')
    @php
        $completedTasks = $tasks->where('completed', true)->count();
        $openTasks = $tasks->count() - $completedTasks;
        $completionRate = $tasks->isEmpty() ? 0 : (int) round(($completedTasks / $tasks->count()) * 100);
    @endphp

    <header class="page-header">
        <div>
            <h1>Tasks</h1>
            <p>{{ $openTasks === 0 ? 'Your queue is clear.' : $openTasks.' '.Str::plural('task', $openTasks).' still need your attention.' }}</p>
        </div>
        <a class="primary-action" href="#new-task">Add a task</a>
    </header>

    <section class="progress-panel" aria-labelledby="progress-title">
        <div class="progress-copy">
            <div>
                <h2 id="progress-title">Coursework progress</h2>
                <p>{{ $completedTasks }} of {{ $tasks->count() }} saved tasks completed</p>
            </div>
            <strong>{{ $completionRate }}<span>%</span></strong>
        </div>
        <div class="progress-track" role="progressbar" aria-label="Coursework completion" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $completionRate }}">
            <span style="width: {{ $completionRate }}%"></span>
        </div>
        <dl class="task-counts">
            <div><dt>Open</dt><dd>{{ $openTasks }}</dd></div>
            <div><dt>Completed</dt><dd>{{ $completedTasks }}</dd></div>
            <div><dt>Total</dt><dd>{{ $tasks->count() }}</dd></div>
        </dl>
    </section>

    <div class="work-grid">
        <section class="task-panel" aria-labelledby="saved-tasks-title">
            <div class="panel-heading">
                <div>
                    <h2 id="saved-tasks-title">Saved tasks</h2>
                    <p>Newest coursework appears first.</p>
                </div>
                <div class="task-filters" role="group" aria-label="Filter tasks">
                    <button type="button" class="is-active" data-filter="all">All</button>
                    <button type="button" data-filter="open">Open</button>
                    <button type="button" data-filter="completed">Done</button>
                </div>
            </div>
            <div class="task-list" aria-live="polite">
                @forelse ($tasks as $task)
                    <article class="task-row" data-status="{{ $task->completed ? 'completed' : 'open' }}">
                        <span class="status-mark" aria-hidden="true"></span>
                        <div class="task-content">
                            <h3>{{ $task->task_title }}</h3>
                            <p>{{ $task->description ?: 'No description provided.' }}</p>
                        </div>
                        <span class="task-status">{{ $task->completed ? 'Completed' : 'Not completed' }}</span>
                    </article>
                @empty
                    <div class="empty-state">
                        <h3>Your task list is ready.</h3>
                        <p>Add the next piece of coursework you want to keep in view.</p>
                        <a href="#new-task">Create your first task</a>
                    </div>
                @endforelse
                <p class="filter-empty" hidden>No tasks match this view.</p>
            </div>
        </section>

        <section class="composer" id="new-task" aria-labelledby="new-task-title">
            <div class="composer-heading">
                <h2 id="new-task-title">Add a task</h2>
                <p>Capture it now. You can return to the details later.</p>
            </div>
            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="field">
                    <label for="task_title">Task title</label>
                    <input type="text" id="task_title" name="task_title" value="{{ old('task_title') }}" placeholder="e.g. Submit database exercise" autocomplete="off" required @error('task_title') aria-invalid="true" aria-describedby="task-title-error" @enderror>
                    @error('task_title')<p class="field-error" id="task-title-error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label for="description">Description <span>Optional</span></label>
                    <textarea id="description" name="description" rows="5" placeholder="Add a helpful note or requirement">{{ old('description') }}</textarea>
                </div>
                <button class="submit-button" type="submit">Save task</button>
            </form>
        </section>
    </div>
@endsection
