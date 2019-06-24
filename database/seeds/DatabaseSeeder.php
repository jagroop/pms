<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $faker = Faker::create();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::table('users')->insert([
            'name' => 'Jagroop Singh',
            'email' => 'jagroop@test.com',
            'role' => 'admin',
            'password' => bcrypt('123456'),
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        DB::table('projects')->truncate();
        foreach (range(1,50) as $index) {
            DB::table('projects')->insert([
                'name' => $faker->name,
                'user_id' => 1,
                'status' => array_random(['done', 'pending', 'in_progress']),
                'started_date' => now()->toDateTimeString(),
                'closed_date' => now()->toDateTimeString(),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }

        DB::table('tasks')->truncate();
        foreach (range(1,50) as $index) {
            DB::table('tasks')->insert([
                'task_name' => $faker->name,
                'task_desc' => $faker->name,
                'project_id' => array_random([1,2,3,4,5,6,7,8,9,10]),
                'task_status' => array_random(['done', 'closed', 'in_progress']),
                'assigned_by' => 1,
                'assigned_to' => 1,
                'started_date' => now()->toDateTimeString(),
                'closed_date' => now()->toDateTimeString(),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }

        DB::table('issues')->truncate();
        foreach (range(1,50) as $index) {
            DB::table('issues')->insert([
                'issue_name' => $faker->name,
                'issue_desc' => $faker->name,
                'project_id' => array_random([1,2,3,4,5,6,7,8,9,10]),
                'issue_status' => array_random(['done', 'closed', 'in_progress']),
                'assigned_by' => 1,
                'assigned_to' => 1,
                'started_date' => now()->toDateTimeString(),
                'closed_date' => now()->toDateTimeString(),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
