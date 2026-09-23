<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_payment_can_be_saved(): void
    {
        $user = User::create([
            'name' => 'Alice Example',
            'email' => 'alice@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-001',
            'invoice_date' => '2026-09-01',
            'due_date' => '2026-09-15',
            'amount' => 1000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 1000,
            'status' => 'draft',
            'notes' => 'Test invoice',
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/invoicepayment/save', [
            'invoice_id' => $invoice->id,
            'amount' => 250,
            'payment_method' => 'cash',
            'payment_date' => '2026-09-20',
            'status' => 'completed',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('msg', 'invoice payment saved successfully');
    }
}
