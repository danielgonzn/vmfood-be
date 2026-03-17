<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard_stats_when_authenticated(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => 'password',
        ]);

        $response = $this->actingAs($admin)->getJson('/api/v1/admin/dashboard/stats');

        $response->assertOk()->assertJsonStructure([
            'data' => ['products_total', 'products_published', 'inquiries_new', 'admins_total'],
        ]);
    }
}
