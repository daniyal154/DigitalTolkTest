<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;
use App\Helpers\TeHelper;

class TeHelperTest extends TestCase
{
    /**
     * Test willExpireAt for jobs that expire in less than or equal to 90 minutes.
     *
     * @return void
     */
    public function test_will_expire_in_90_minutes()
    {
        // Create the due_time and created_at timestamps
        $created_at = Carbon::now();
        $due_time = Carbon::now()->addMinutes(60); // 60 minutes from creation

        // Call the willExpireAt method
        $expirationTime = TeHelper::willExpireAt($due_time->toDateTimeString(), $created_at->toDateTimeString());

        // Assert that the expiration time is equal to the due time
        $this->assertEquals($due_time->format('Y-m-d H:i:s'), $expirationTime);
    }

    /**
     * Test willExpireAt for jobs that expire within 24 hours.
     *
     * @return void
     */
    public function test_will_expire_in_24_hours()
    {
        // Create the due_time and created_at timestamps
        $created_at = Carbon::now();
        $due_time = Carbon::now()->addHours(20); // 20 hours from creation

        // Call the willExpireAt method
        $expirationTime = TeHelper::willExpireAt($due_time->toDateTimeString(), $created_at->toDateTimeString());

        // Assert that the expiration time is 90 minutes after creation time
        $this->assertEquals($created_at->addMinutes(90)->format('Y-m-d H:i:s'), $expirationTime);
    }

    /**
     * Test willExpireAt for jobs that expire between 24 and 72 hours.
     *
     * @return void
     */
    public function test_will_expire_in_72_hours()
    {
        // Create the due_time and created_at timestamps
        $created_at = Carbon::now();
        $due_time = Carbon::now()->addHours(48); // 48 hours from creation

        // Call the willExpireAt method
        $expirationTime = TeHelper::willExpireAt($due_time->toDateTimeString(), $created_at->toDateTimeString());

        // Assert that the expiration time is 16 hours after creation time
        $this->assertEquals($created_at->addHours(16)->format('Y-m-d H:i:s'), $expirationTime);
    }

    /**
     * Test willExpireAt for jobs that expire after more than 72 hours.
     *
     * @return void
     */
    public function test_will_expire_after_72_hours()
    {
        // Create the due_time and created_at timestamps
        $created_at = Carbon::now();
        $due_time = Carbon::now()->addHours(100); // 100 hours from creation

        // Call the willExpireAt method
        $expirationTime = TeHelper::willExpireAt($due_time->toDateTimeString(), $created_at->toDateTimeString());

        // Assert that the expiration time is 48 hours before the due time
        $this->assertEquals($due_time->subHours(48)->format('Y-m-d H:i:s'), $expirationTime);
    }
}
