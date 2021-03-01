<?php

use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
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
            $response = $this->call('POST', '/api/v1/user', []);

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
        $this->specify('Should 401 if not authorized', function() {
            $response = $this->call('PUT', '/api/v1/user/123', []);
            $this->assertEquals(401, $response->status());
        });

        $this->specify('Should 404 if not valid, existing user', function() use ($user) {
            $user = User::factory()->make();
            $response = $this->actingAs($user)->call('PUT', '/api/v1/user/123', []);

            $this->assertEquals(404, $response->status());
        });

        $this->specify('Should validate request', function() use ($user) {
            $response = $this->actingAs($user)->call('PUT', '/api/v1/user/'.$user->id, []);

            $this->assertEquals(422, $response->status());
        });

        $this->specify('Should respond with updated user', function() use ($user) {
            $req = [
                'name' => 'Arty Fischel',
            ];
            $response = $this->actingAs($user)->call('PUT', '/api/v1/user/'.$user->id, $req);

            $response->assertJsonFragment(['name'=>$req['name']]);
            $this->assertEquals(200, $response->status());
        });
    }

    public function testDestroy()
    {
        $user = User::factory()->create();
        $this->specify('Should 401 if not authorized', function() {
            $response = $this->call('DELETE', '/api/v1/user/123');
            $this->assertEquals(401, $response->status());
        });

        $this->specify('Should 404 if not valid user', function() use ($user) {
            $response = $this->actingAs($user)->call('DELETE', '/api/v1/user/123');
            $this->assertEquals(404, $response->status());
        });

        $this->specify('Should respond with deleted user', function() use ($user) {
            $response = $this->actingAs($user)->call('DELETE', '/api/v1/user/'.$user->id);

            $response->assertJsonFragment(['name'=>$user->name]);
            $this->assertEquals(200, $response->status());
        });
    }

    public function testLogin()
    {
        $password = Str::random(10);
        $user = User::factory()->create([
            'password' => app('hash')->make($password),
        ]);

        $this->specify('Should validate request', function() {
            $response = $this->call('POST', '/api/v1/login', []);
            $this->assertEquals(422, $response->status());
        });

        $this->specify('Should 401 if password is invalid', function() use ($user) {
            $response = $this->call('POST', '/api/v1/login', [
                'email' => $user->email,
                'password' => 'bad-password',
            ]);
            $this->assertEquals(401, $response->status());
        });

        $this->specify('Responds with valid token', function() use ($user, $password) {
            $response = $this->call('POST', '/api/v1/login', [
                'email' => $user->email,
                'password' => $password,
            ]);
            $response->assertSee('token');
            $this->assertEquals(200, $response->status());
        });
    }

    public function testLogout()
    {
        $user = User::factory()->create();
        $this->specify('Should 401 if not authorized', function() {
            $response = $this->call('GET', '/api/v1/logout');
            $this->assertEquals(401, $response->status());
        });

        $this->specify('Should 204 on success', function() use ($user) {
            $response = $this->actingAs($user)->call('GET', '/api/v1/logout');
            $this->assertEquals(204, $response->status());
        });
    }

    public function testAuth()
    {
        $token = Token::factory()->create();
        $oldToken = Token::factory()->create([
            'expires_at' => Carbon::now()->add('-1 hour'),
        ]);

        $this->specify('Should 401 if not authorized', function() {
            $response = $this->call('GET', '/api/v1/logout');
            $this->assertEquals(401, $response->status());
        });

        $this->specify('Should 401 if no valid auth header', function() {
            $response = $this->call('GET', '/api/v1/logout');
            $this->assertEquals(401, $response->status());
        });

        $this->specify('Should 401 if token is old', function() use ($oldToken) {
            $response = $this->call('GET', '/api/v1/logout', [], [], [], [
                'HTTP_x-api-token' => $oldToken->token,
            ]);
            $this->assertEquals(401, $response->status());
        });

        $this->specify('Succeeds if valid auth header', function() use ($token) {
            $response = $this->call('GET', '/api/v1/logout', [], [], [], [
                'HTTP_x-api-token' => $token->token,
            ]);
            $this->assertEquals(204, $response->status());
        });
    }
}
