<?php

use Budgetcontrol\Library\Model\Goal;
use Phinx\Seed\AbstractSeed;

class GoalSeeds extends AbstractSeed
{

    public function run(): void
    {


        \Budgetcontrol\Library\Model\Workspace::create([
            'name' => 'goal workspace',
            'description' => 'Workspace for goal management',
            'current' => 1,
            'user_id' => 1,
            'uuid' => "de4f2a9c-9c0a-4ee6-ab46-fb4536fd7461",
        ]);

        // set relation with user and workspace
        $user = \Budgetcontrol\Library\Model\User::find(1);
        $workspace = \Budgetcontrol\Library\Model\Workspace::where('uuid', "de4f2a9c-9c0a-4ee6-ab46-fb4536fd7461")->first();
    
        $workspace->users()->attach($user);
        $workspace->save();
        
        $goals = [
            [
                'workspace_id' => 2,
                'name' => 'Emergency Fund',
                'amount' => 10000.00,
                'description' => 'Build emergency fund for unexpected expenses',
                'due_date' => '2023-12-31',
                'status' => 'active',
                'category_icon' => 'savings',
                'uuid' => '9f47af2d-8c2b-4812-b164-5e58b731c106'
            ],
            [
                'workspace_id' => 2,
                'name' => 'New Car',
                'amount' => 25000.00,
                'description' => 'Save for a new vehicle',
                'due_date' => '2024-06-30',
                'status' => 'active',
                'category_icon' => 'car',
                'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString()
            ],
            [
                'workspace_id' => 2,
                'name' => 'Home Down Payment',
                'amount' => 50000.00,
                'description' => 'Save for home purchase',
                'due_date' => '2025-01-15',
                'status' => 'active',
                'category_icon' => 'home',
                'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString()
            ],
            [
                'workspace_id' => 2,
                'name' => 'Vacation',
                'amount' => 3000.00,
                'description' => 'Summer vacation fund',
                'due_date' => '2023-07-01',
                'status' => 'complete',
                'category_icon' => 'beach',
                'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString()
            ],
            [
                'workspace_id' => 2,
                'name' => 'Education',
                'amount' => 15000.00,
                'description' => 'College education savings',
                'due_date' => '2026-08-31',
                'status' => 'active',
                'category_icon' => 'school',
                'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString()
            ]
        ];

        foreach ($goals as $goalData) {
            $goal = new Goal();
            $goal->workspace_id = $goalData['workspace_id'];
            $goal->name = $goalData['name'];
            $goal->amount = $goalData['amount'];
            $goal->description = $goalData['description'] ?? null;
            $goal->due_date = $goalData['due_date'] ?? null;
            $goal->status = $goalData['status'] ?? 'active';
            $goal->category_icon = $goalData['category_icon'] ?? null;
            $goal->uuid = $goalData['uuid'];
            $goal->save();
        }

    }
}
