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
            $response = $this->call('POST', '/user/123', []);
            $this->assertEquals(401, $response->status());
        });

        $this->specify('Should 404 if not valid, existing user', function() {
            $user = User::factory()->make();
            $response = $this->call('POST', '/user/123', []);

            $this->assertEquals(404, $response->status());
        });

        $user = User::factory()->create();
        var_dump($user->id);
        $this->specify('Should validate request', function() use ($user) {
            $response = $this->call('POST', '/user/'+$user->id, []);

            $this->assertEquals(422, $response->status());
        });

        $this->specify('Should respond with updated user', function() use ($user) {
            $req = [
                'name' => 'Arty Fischel',
            ];
            $response = $this->call('POST', '/user/'+$user->id, $req);

            $response->assertJsonFragment(['name'=>$req->name]);
            $this->assertEquals(200, $response->status());
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
