<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_detail_endpoint_loads_quotations_relation(): void
    {
        $user = User::factory()->create();

        $lead = Lead::create([
            'name' => 'Alpha Lead',
            'email' => 'alpha@example.com',
            'company' => 'Example Co',
            'status' => 'new',
            'assigned_to' => $user->id,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/lead/detail', ['id' => $lead->id]);

        $response->assertStatus(200)
            ->assertJsonPath('msg', 'lead detail')
            ->assertJsonPath('data.id', $lead->id)
            ->assertJsonPath('data.quotations', []);
    }
}
