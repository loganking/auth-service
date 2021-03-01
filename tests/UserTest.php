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
            $response = $this->call('POST', '/user', $user);

            $this->assertEquals(422, $response->status());
        });

        $this->specify('Should respond with created user', function() {
            $user = User::factory()->make();
            $response = $this->call('POST', '/user', $user);

            $response->assertJsonFragment(201, $user);
            $this->assertEquals(201, $response->status());
        });
    }

    public function testUpdate()
    {
        $this->specify('Should 404 if not valid user', function() {
            $user = User::factory()->make();
            $response = $this->call('PUT', '/user/'+$user->id, $user);

            $this->assertEquals(404, $response->status());
        });

        $this->specify('Should validate request', function() {
        });

        $this->specify('Should respond with updated user', function() {
        });
    }

    public function testDestroy()
    {
        $this->specify('Must be authorized', function() {
            //
        });
        $this->specify('Should respond with deleted user', function() {
            //
        });
        $this->specify('Should 404 if not valid user', function() {
            //
        });
    }
}
