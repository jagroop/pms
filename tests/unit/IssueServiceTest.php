<?php

use Tests\TestCase;
use App\Services\IssueService;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class IssueServiceTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->service = $this->app->make(IssueService::class);
        $this->originalArray = [
            'id' => '1',
		'project_id' => '1',
		'assigned_by' => '1',
		'assigned_to' => '1',
		'issue_name' => 'aut',
		'issue_desc' => 'necessitatibus incidunt expedita perspiciatis',
		'issue_status' => 'aut',
		'started_date' => '2018-09-09 11:08:07',
		'closed_date' => '2018-09-09 11:08:07',
		'created_at' => '2018-09-09 11:08:07',
		'updated_at' => '2018-09-09 11:08:07',

        ];
        $this->editedArray = [
            'id' => '1',
		'project_id' => '1',
		'assigned_by' => '1',
		'assigned_to' => '1',
		'issue_name' => 'aut',
		'issue_desc' => 'necessitatibus incidunt expedita perspiciatis',
		'issue_status' => 'aut',
		'started_date' => '2018-09-09 11:08:07',
		'closed_date' => '2018-09-09 11:08:07',
		'created_at' => '2018-09-09 11:08:07',
		'updated_at' => '2018-09-09 11:08:07',

        ];
        $this->searchTerm = '';
    }

    public function testAll()
    {
        $response = $this->service->all();
        $this->assertEquals(get_class($response), 'Illuminate\Database\Eloquent\Collection');
        $this->assertTrue(is_array($response->toArray()));
        $this->assertEquals(0, count($response->toArray()));
    }

    public function testPaginated()
    {
        $response = $this->service->paginated(25);
        $this->assertEquals(get_class($response), 'Illuminate\Pagination\LengthAwarePaginator');
        $this->assertEquals(0, $response->total());
    }

    public function testSearch()
    {
        $response = $this->service->search($this->searchTerm, 25);
        $this->assertEquals(get_class($response), 'Illuminate\Pagination\LengthAwarePaginator');
        $this->assertEquals(0, $response->total());
    }

    public function testCreate()
    {
        $response = $this->service->create($this->originalArray);
        $this->assertEquals(get_class($response), 'App\Models\Issue');
        $this->assertEquals(1, $response->id);
    }

    public function testFind()
    {
        // create the item
        $item = $this->service->create($this->originalArray);

        $response = $this->service->find($item->id);
        $this->assertEquals($item->id, $response->id);
    }

    public function testUpdate()
    {
        // create the item
        $item = $this->service->create($this->originalArray);

        $response = $this->service->update($item->id, $this->editedArray);

        $this->assertDatabaseHas('issues', $this->editedArray);
    }

    public function testDestroy()
    {
        // create the item
        $item = $this->service->create($this->originalArray);

        $response = $this->service->destroy($item->id);
        $this->assertTrue($response);
    }
}
