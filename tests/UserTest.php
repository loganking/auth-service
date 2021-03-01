<?php

use App\Models\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreate()
    {
        $this->specify('Should validate request', function() {
            $user = [];
            $response = $this->call('POST', '/api/v1/user', $user);

            $this->assertEquals(422, $response->status());
        });

        $this->specify('Should respond with created user', function() {
            $user = User::factory()->make()->toArray();
            $user['password'] = 'abc123';
            $response = $this->call('POST', '/api/v1/user', $user);

            $response->assertJsonFragment(['name'=>$user['name']]);
            $this->assertEquals(201, $response->status());
        });
    }

    public function testUpdate()
    {
        $user = User::factory()->create();
        $req = [];
        $this->specify('Must be authorized', function() {
            $response = $this->call('POST', '/user/1', []);
            $this->assertEquals(401, $response->status());
        });

        $this->specify('Should 404 if not valid, existing user', function() {
            $user = User::factory()->make();
            $response = $this->call('POST', '/user/123', []);

            $this->assertEquals(404, $response->status());
        });

        $this->specify('Should validate request', function() {
            $user = User::factory()->create();
            $response = $this->call('POST', '/user/123', []);

            $this->assertEquals(422, $response->status());
        });

        $this->specify('Should respond with updated user', function() {
        });
    }

    public function testDestroy()
    {
        $this->specify('Must be authorized', function() {
            $response = $this->call('POST', '/user', []);
            $this->assertEquals(401, $response->status());
        });
        $this->specify('Should respond with deleted user', function() {
            //
        });
        $this->specify('Should 404 if not valid user', function() {
            //
        });
    }
}
