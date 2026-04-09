<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_tasks(): void
    {
        $response = $this->get(route('tasks.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_create_task_and_assign_to_other(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user1)->post(route('tasks.store'), [
            'title' => 'Assigned Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'priority' => 'medium',
            'assigned_to_id' => $user2->id,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Assigned Task',
            'user_id' => $user1->id,
            'assigned_to_id' => $user2->id,
        ]);
    }

    public function test_assignee_can_see_assigned_task(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user1->id, 'assigned_to_id' => $user2->id, 'title' => 'Secret Project']);

        $response = $this->actingAs($user2)->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertSee('Secret Project');
    }

    public function test_user_cannot_see_unrelated_task(): void
    {
        $user1 = User::factory()->create(); // Creator
        $user2 = User::factory()->create(); // Assignee
        $user3 = User::factory()->create(); // Outsider

        $task = Task::factory()->create(['user_id' => $user1->id, 'assigned_to_id' => $user2->id, 'title' => 'Private Task']);

        $response = $this->actingAs($user3)->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Private Task');
    }

    public function test_assignee_can_update_status(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user1->id, 'assigned_to_id' => $user2->id, 'status' => 'pending']);

        $response = $this->actingAs($user2)->patchJson(route('tasks.updateStatus', $task), [
            'status' => 'in_progress',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_only_creator_can_delete_task(): void
    {
        $user1 = User::factory()->create(); // Creator
        $user2 = User::factory()->create(); // Assignee
        $task = Task::factory()->create(['user_id' => $user1->id, 'assigned_to_id' => $user2->id]);

        // Assignee attempts delete
        $response = $this->actingAs($user2)->delete(route('tasks.destroy', $task));
        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);

        // Creator attempts delete
        $response = $this->actingAs($user1)->delete(route('tasks.destroy', $task));
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
