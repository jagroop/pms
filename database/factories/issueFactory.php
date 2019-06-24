<?php
/*
|--------------------------------------------------------------------------
| Issue Factory
|--------------------------------------------------------------------------
*/

$factory->define(App\Models\Issue::class, function (Faker\Generator $faker) {
    return [
        'id' => '1',
		'project_id' => '1',
		'assigned_by' => '1',
		'assigned_to' => '1',
		'issue_name' => 'dolorum',
		'issue_desc' => 'illo expedita et voluptas',
		'issue_status' => 'voluptatem',
		'started_date' => '2018-09-09 11:08:07',
		'closed_date' => '2018-09-09 11:08:07',
		'created_at' => '2018-09-09 11:08:07',
		'updated_at' => '2018-09-09 11:08:07',
    ];
});
