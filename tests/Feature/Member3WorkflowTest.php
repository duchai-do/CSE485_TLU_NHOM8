<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Member3WorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_member3_pages_can_be_opened_after_seeding(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/member3/registrations')->assertOk();
        $this->get('/member3/allocations')->assertOk();
        $this->get('/member3/contracts')->assertOk();
        $this->get('/member3/violations')->assertOk();
    }

    public function test_allocation_create_rejects_invalid_foreign_keys(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post('/member3/allocations', [
            'registration_id' => 999999,
            'bed_id' => 999999,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['registration_id', 'bed_id']);
    }
}
