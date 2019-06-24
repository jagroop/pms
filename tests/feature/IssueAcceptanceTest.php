<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class IssueAcceptanceTest extends TestCase
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
		'issue_name' => 'eos',
		'issue_desc' => 'odio voluptates dignissimos enim',
		'issue_status' => 'ullam',
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
		'issue_name' => 'eos',
		'issue_desc' => 'odio voluptates dignissimos enim',
		'issue_status' => 'ullam',
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
        $response = $this->actor->call('GET', 'issues');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('issues');
    }

    public function testCreate()
    {
        $response = $this->actor->call('GET', 'issues/create');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testStore()
    {
        $response = $this->actor->call('POST', 'issues', $this->Issue->toArray());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedTo('issues/'.$this->Issue->id.'/edit');
    }

    public function testEdit()
    {
        $this->actor->call('POST', 'issues', $this->Issue->toArray());

        $response = $this->actor->call('GET', '/issues/'.$this->Issue->id.'/edit');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertViewHas('issue');
    }

    public function testUpdate()
    {
        $this->actor->call('POST', 'issues', $this->Issue->toArray());
        $response = $this->actor->call('PATCH', 'issues/1', $this->IssueEdited->toArray());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertDatabaseHas('issues', $this->IssueEdited->toArray());
        $this->assertRedirectedTo('/');
    }

    public function testDelete()
    {
        $this->actor->call('POST', 'issues', $this->Issue->toArray());

        $response = $this->call('DELETE', 'issues/'.$this->Issue->id);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedTo('issues');
    }

}
