<?php

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('tasks page displays saved tasks', function () {
    Task::create([
        'task_title' => 'Finish Laravel tutorial',
        'description' => 'Complete the database section.',
    ]);

    $response = $this->get(route('tasks.index'));

    $response->assertOk()
        ->assertSee('Finish Laravel tutorial')
        ->assertSee('Complete the database section.');
});

test('tasks page is the website index', function () {
    $this->get('/')
        ->assertOk()
        ->assertViewIs('tasks');

    $this->get('/tasks')
        ->assertRedirect('/');
});

test('about page is removed', function () {
    $this->get('/about')->assertNotFound();
});

test('a task can be added', function () {
    $response = $this->post(route('tasks.store'), [
        'task_title' => 'Review migrations',
        'description' => 'Confirm the tasks table exists.',
    ]);

    $response->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success', 'Task added successfully.');

    $this->assertDatabaseHas('tasks', [
        'task_title' => 'Review migrations',
        'description' => 'Confirm the tasks table exists.',
        'completed' => false,
    ]);
});

test('a task title is required', function () {
    $response = $this->post(route('tasks.store'), [
        'description' => 'This task has no title.',
    ]);

    $response->assertSessionHasErrors('task_title');
    $this->assertDatabaseCount('tasks', 0);
});
