<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a Primary Client Account (Manager)
        $client = User::factory()->create([
            'name' => 'Client Account',
            'email' => 'client@example.com',
            'password' => bcrypt('password'),
        ]);

        // 2. Create a Team Member (Assignee)
        $developer = User::factory()->create([
            'name' => 'John Developer',
            'email' => 'dev@example.com',
            'password' => bcrypt('password'),
        ]);

        // 3. Collaborative Tasks

        // Task created by Client, assigned to Developer
        Task::factory()->create([
            'user_id' => $client->id,
            'assigned_to_id' => $developer->id,
            'title' => 'Implement Assignment Module',
            'description' => 'Build the system that allows users to assign tasks to other team members.',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => now()->addDays(3),
        ]);

        // Task created by Client, assigned to Developer
        Task::factory()->create([
            'user_id' => $client->id,
            'assigned_to_id' => $developer->id,
            'title' => 'Fix CSS alignment in Dashboard',
            'description' => 'The progress bar needs to be perfectly aligned with the stats text.',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => now()->addDay(),
        ]);

        // Task created by Developer, assigned to Client (for review)
        Task::factory()->create([
            'user_id' => $developer->id,
            'assigned_to_id' => $client->id,
            'title' => 'Review API Documentation',
            'description' => 'I have finished the first draft of the API docs. Please review.',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => now()->addDays(2),
        ]);

        // Personal tasks
        Task::factory(2)->create(['user_id' => $client->id, 'assigned_to_id' => null, 'title' => 'Personal: ' . fake()->words(2, true)]);
        Task::factory(2)->create(['user_id' => $developer->id, 'assigned_to_id' => null, 'title' => 'Personal: ' . fake()->words(2, true)]);
    }
}
