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

    // use RefreshDatabase;

    public function testSinglePostReq()
    {
        $response = $this->postJson('/api', ['u1' => 'Test1']);
        $response
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    public function testEmptyKey(){
        $response = $this->postJson('/api', ['' => 'Test1a']);
        $response->assertStatus(400);
    }

    public function testSymbolsInKey(){
        $response = $this->postJson('/api', ['Nw6!7&%oV4Ds46' => 'Test1a']);
        $response->assertStatus(201);
    }

    public function testSymbolsInValue(){
        $response = $this->postJson('/api', ['user2' => '!o7wft8TBx6wZufqsVF7CX5QHhr#39PP&7Dnw$c*^o7bj#v$3SZ%Q&AEXVwJ4%%2#!3fh&d^5Vfje8PgTY@%cLQh2#73dQw@Dvc@']);
        $response->assertStatus(201);
    }

    public function testSymbolsInKeyValue(){
        $response = $this->postJson('/api', ['Nw6!7&%oV4Ds46' => 'Nc8eE8Yk9siSRKVH!LRzr3eMvwMxMZpg%7N5HYY&^^Wmy7LdM!D7zeYbxW*2iJRhijE$jYrgnPN2!v3xNC@XRc$W583XbY4t%@Ln']);
        $response->assertStatus(201);
    }

    public function testWhitespaceInKey(){
        $response = $this->postJson('/api', ['z8    z8D 92#7rW   d*L' => '^upEc^!8ziei5Jk3igW6#ETv%w95v9CjJ$PX%3hphco4XzCjXQ8wgF4TPkSD%2ehFMNmjkEcYr%7XgL^&oCU7w*udY3Ar2E6RHYT']);
        $response->assertStatus(201);
    }

    public function testGetAllObjects(){
        $response = $this->get('/api/get_all_records');
        $response->assertStatus(200);
    }
    
    // Dirty data for testing. Not planning to use this without DatabaseRefresh
    // public function testHeavyLoadReq()
    // {
    //     for($i = 1; $i < 51; $i++){
    //         $response = $this->postJson('/api', ['u'.$i => '"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?"']);
    //     }
    //     $response->assertStatus(201);
    // }
}
