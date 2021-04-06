<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class APITest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSinglePostReq()
    {
        $response = $this->postJson('/api', ['u1' => 'Test1']);
        $response
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    public function testHeavyLoadReq()
    {
        for($i = 1; $i < 51; $i++){
            $response = $this->postJson('/api', ['u'.$i => '"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?"']);
        }
        $response->assertStatus(201);
    }

    public function testGetAllObjects(){
        $response = $this->get('/api/get_all_records');
        $response->assertJsonCount(50);
    }
}
