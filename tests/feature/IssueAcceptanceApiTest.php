<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class IssueAcceptanceApiTest extends TestCase
{
    use DatabaseMigrations;
    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();

        $this->Issue = factory(App\Models\Issue::class)->make([
            'id' => '1',
		'project_id' => '1',
		'assigned_by' => '1',
		'assigned_to' => '1',
		'issue_name' => 'officiis',
		'issue_desc' => 'iure laboriosam impedit provident',
		'issue_status' => 'sed',
		'started_date' => '2018-09-09 11:08:07',
		'closed_date' => '2018-09-09 11:08:07',
		'created_at' => '2018-09-09 11:08:07',
		'updated_at' => '2018-09-09 11:08:07',

        ]);
        $this->IssueEdited = factory(App\Models\Issue::class)->make([
            'id' => '1',
		'project_id' => '1',
		'assigned_by' => '1',
		'assigned_to' => '1',
		'issue_name' => 'officiis',
		'issue_desc' => 'iure laboriosam impedit provident',
		'issue_status' => 'sed',
		'started_date' => '2018-09-09 11:08:07',
		'closed_date' => '2018-09-09 11:08:07',
		'created_at' => '2018-09-09 11:08:07',
		'updated_at' => '2018-09-09 11:08:07',

        ]);
        $user = factory(App\Models\User::class)->make();
        $this->actor = $this->actingAs($user);
    }

    public function testIndex()
    {
        $response = $this->actor->call('GET', 'api/v1/issues');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testStore()
    {
        $response = $this->actor->call('POST', 'api/v1/issues', $this->Issue->toArray());
        $this->assertEquals(200, $response->getStatusCode());
        $this->seeJson(['id' => 1]);
    }

    public function testUpdate()
    {
        $this->actor->call('POST', 'api/v1/issues', $this->Issue->toArray());
        $response = $this->actor->call('PATCH', 'api/v1/issues/1', $this->IssueEdited->toArray());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('issues', $this->IssueEdited->toArray());
    }

    public function testDelete()
    {
        $this->actor->call('POST', 'api/v1/issues', $this->Issue->toArray());
        $response = $this->call('DELETE', 'api/v1/issues/'.$this->Issue->id);
        $this->assertEquals(200, $response->getStatusCode());
        $this->seeJson(['success' => 'issue was deleted']);
    }

}
