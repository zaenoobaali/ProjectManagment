<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // create roles
        $this->call([
            RoleSeeder::class,
        ]);

        // get roles
        $adminRole = Role::where('role_name', 'admin')->first();
        $memberRole = Role::where('role_name', 'member')->first();

        // create users
        $users = User::factory(5)->create();

        // using attach to assign roles to users
        foreach ($users as $index => $user) {
        
            if ($index < 2 && $adminRole) {
                $user->roles()->attach($adminRole->id);
            } elseif ($memberRole) {
                $user->roles()->attach($memberRole->id);
            }
        }

        $adminUser = $users->where('roles.role_name', 'admin')->first() ?? $users->first();

        
        $projects = Project::factory(3)->create([
            'created_by' => $adminUser->id
        ]);
        // create projects and tasks and comments
        foreach ($projects as $project) {
            
            $project->users()->attach($users->pluck('id')->toArray());

            $tasks = Task::factory(3)->create([
                'project_id' => $project->id
            ]);

            foreach ($tasks as $task) {
                
                $task->users()->attach($users->random()->id);

                Comment::factory(2)->create([
                    'task_id' => $task->id,
                    'user_id' => $users->random()->id
                ]);
            }
        }

    }
}
