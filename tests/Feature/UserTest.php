<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
  /**
   * A basic feature test example.
   */
  public function test_RegisterSuccess()
  {
    $this->post("/api/users", [
      "username" => "muhammadisa226",
      "password" => "muhammadisa",
      "name" => "Muhammad Isa",
    ])->assertStatus(201)->assertJson([
      "data" => [
        "username" => "muhammadisa226",
        "name" => "Muhammad Isa",
      ]
    ]);
  }
  public function test_RegisterFailed()
  {
    $this->post("/api/users", [
      "username" => "",
      "password" => "",
      "name" => "",
    ])->assertStatus(400)->assertJson([
      "errors" => [
        "username" => [
          "The username field is required."
        ],
        "password" => [
          "The password field is required."
        ],
        "name" => [
          "The name field is required."
        ],
      ]
    ]);
  }
  public function test_RegisterUsernameAlreadyExist()
  {
    $this->test_RegisterSuccess();
    $this->post("/api/users", [
      "username" => "muhammadisa226",
      "password" => "muhammadisa",
      "name" => "Muhammad Isa",
    ])->assertStatus(400)->assertJson([
      "errors" => [
        "username" => "username already registered"
      ]
    ]);
  }
  public function test_LoginSuccess()
  {
    $this->seed(UserSeeder::class);
    $this->post("/api/users/login", [
      "username" => "test",
      "password" => "test",
    ])->assertStatus(200)->assertJson([
      "data" => [
        "username" => "test",
        "name" => "test"
      ]
    ]);
    $user = User::where('username', 'test')->first();
    self::assertNotNull($user->token);
  }
  public function test_LoginFailedUsernameNotFound()
  {
    $this->seed(UserSeeder::class);
    $this->post("/api/users/login", [
      "username" => "test1",
      "password" => "test",
    ])->assertStatus(401)->assertJson([
      "errors" => [
        "message" => "username or password wrong"
      ]
    ]);
  }
  public function test_LoginFailedPasswordWrong()
  {
    $this->seed(UserSeeder::class);
    $this->post("/api/users/login", [
      "username" => "test",
      "password" => "test1",
    ])->assertStatus(401)->assertJson([
      "errors" => [
        "message" => "username or password wrong"
      ]
    ]);
  }
  public function test_GetSuccess()
  {
    $this->seed(UserSeeder::class);
    $this->get("/api/users/current", ['Authorization' => 'test'])->assertStatus(200)->assertJson([
      "data" => [
        "username" => "test",
        "name" => "test"
      ]
    ]);
  }
  public function test_GetUnauthorized()
  {
    $this->seed(UserSeeder::class);
    $this->get("/api/users/current")->assertStatus(401)->assertJson([
      "errors" => [
        "message" => ["Unauthorized"],
      ]
    ]);
  }
  public function test_GetInvalidToken()
  {
    $this->seed(UserSeeder::class);
    $this->get("/api/users/current", ['Authorization' => 'test1'])->assertStatus(401)->assertJson([
      "errors" => [
        "message" => ["Unauthorized"],
      ]
    ]);
  }
  public function test_UpdateNameSuccess()
  {
    $this->seed(UserSeeder::class);
    $oldUser = User::where('username', 'test')->first();
    $this->patch("/api/users/current", [
      "name" => 'muhisa'
    ], ['Authorization' => 'test'])->assertStatus(200)->assertJson([
      "data" => [
        "username" => "test",
        "name" => "muhisa"
      ]
    ]);
    $NewUser = User::where('username', 'test')->first();
    self::assertNotEquals($oldUser->name, $NewUser->name);
  }
  public function test_UpdatePasswordSuccess()
  {
    $this->seed(UserSeeder::class);
    $oldUser = User::where('username', 'test')->first();
    $this->patch("/api/users/current", [
      "password" => 'baru'
    ], ['Authorization' => 'test'])->assertStatus(200)->assertJson([
      "data" => [
        "username" => "test",
        "name" => "test"
      ]
    ]);
    $NewUser = User::where('username', 'test')->first();
    self::assertNotEquals($oldUser->password, $NewUser->password);
  }

  public function test_UpdateFailed()
  {
    $this->seed(UserSeeder::class);
    $this->patch("/api/users/current", [
      "password" => 'baru'
    ])->assertStatus(401)->assertJson([
      "errors" => [
        "message" => ["Unauthorized"],
      ]
    ]);
  }
  public function test_LogoutSuccess()
  {
    $this->seed(UserSeeder::class);
    $this->delete(uri: '/api/users/logout', headers: ['Authorization' => 'test'])->assertStatus(200)->assertJson([
      "data" => true
    ]);
    $user = User::where('username', 'test')->first();
    self::assertNull($user->token);
  }
  public function test_LogoutFailed()
  {
    $this->seed(UserSeeder::class);
    $this->delete("/api/users/logout", [], [
      "Authorization" => 'test1'
    ])->assertStatus(401)->assertJson([
      "errors" => [
        "message" => ["Unauthorized"],
      ]
    ]);
  }
}
