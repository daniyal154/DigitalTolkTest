<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repository\UserRepository;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\Company;
use App\Models\Department;
use App\Models\UsersBlacklist;
use App\Models\UserLanguages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new user.
     */
    public function test_create_user()
    {
        $repository = new UserRepository();

        // Mock the request
        $request = [
            'role' => 'customer',
            'name' => 'John Doe',
            'company_id' => '',
            'department_id' => '',
            'email' => 'johndoe@example.com',
            'dob_or_orgid' => '01-01-1980',
            'phone' => '123456789',
            'mobile' => '987654321',
            'password' => 'password123',
        ];

        // Call the createOrUpdate method with null for new user creation
        $user = $repository->createOrUpdate(null, $request);

        // Assert that the user was created
        $this->assertNotNull($user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Test updating an existing user.
     */
    public function test_update_user()
    {
        $repository = new UserRepository();

        // Create a user
        $user = User::factory()->create();

        // Mock the request with updated information
        $request = [
            'role' => 'customer',
            'name' => 'Jane Doe',
            'company_id' => '',
            'department_id' => '',
            'email' => 'janedoe@example.com',
            'dob_or_orgid' => '01-01-1990',
            'phone' => '123456789',
            'mobile' => '987654321',
        ];

        // Call the createOrUpdate method with the existing user's ID
        $updatedUser = $repository->createOrUpdate($user->id, $request);

        // Assert that the user was updated
        $this->assertEquals('Jane Doe', $updatedUser->name);
        $this->assertEquals('janedoe@example.com', $updatedUser->email);
    }

    /**
     * Test creating a user with CUSTOMER_ROLE_ID and consumer type 'paid'.
     */
    public function test_create_customer_with_paid_consumer_type()
    {
        $repository = new UserRepository();

        // Mock the request
        $request = [
            'role' => env('CUSTOMER_ROLE_ID'),
            'name' => 'John Doe',
            'company_id' => '',
            'department_id' => '',
            'email' => 'johndoe@example.com',
            'dob_or_orgid' => '01-01-1980',
            'phone' => '123456789',
            'mobile' => '987654321',
            'consumer_type' => 'paid',
            'customer_type' => 'type1',
            'username' => 'johndoe',
            'post_code' => '12345',
            'address' => '123 Street',
            'city' => 'CityName',
            'town' => 'TownName',
            'country' => 'CountryName',
        ];

        // Call the createOrUpdate method with null for new user creation
        $user = $repository->createOrUpdate(null, $request);

        // Assert that the user and user_meta were created
        $this->assertNotNull($user);
        $this->assertNotNull($user->meta);
        $this->assertEquals('paid', $user->meta->consumer_type);
    }

    /**
     * Test updating a user with TRANSLATOR_ROLE_ID.
     */
    public function test_update_translator_user()
    {
        $repository = new UserRepository();

        // Create a user
        $user = User::factory()->create();

        // Mock the request for translator role
        $request = [
            'role' => env('TRANSLATOR_ROLE_ID'),
            'name' => 'Jane Doe',
            'translator_type' => 'senior',
            'worked_for' => 'yes',
            'organization_number' => '123456789',
            'gender' => 'female',
            'translator_level' => 'level1',
            'additional_info' => 'Test info',
        ];

        // Call the createOrUpdate method
        $updatedUser = $repository->createOrUpdate($user->id, $request);

        // Assert that the translator-related fields were updated
        $this->assertEquals('senior', $updatedUser->meta->translator_type);
        $this->assertEquals('yes', $updatedUser->meta->worked_for);
    }
}
