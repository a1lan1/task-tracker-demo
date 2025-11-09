<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $allUsers = User::all();
        $mainUser = $allUsers->first();
        $otherUsers = $allUsers->except(['id' => $mainUser->id]);

        Project::factory(20)
            ->for($mainUser, 'owner')
            ->has(Task::factory(10)
                ->state(new Sequence(
                    fn (Sequence $sequence): array => ['assignee_id' => $allUsers->random()->id]
                ))
                ->has(Comment::factory(20)
                    ->state(new Sequence(
                        fn (Sequence $sequence): array => ['user_id' => $allUsers->random()->id]
                    ))
                )
            )
            ->afterCreating(function (Project $project) use ($otherUsers): void {
                $project->members()->attach(
                    $otherUsers->random(3)->pluck('id')->toArray()
                );
            })
            ->create();
    }
}
