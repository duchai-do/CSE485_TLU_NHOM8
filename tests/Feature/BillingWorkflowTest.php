<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_a_room_cannot_have_two_readings_for_the_same_month(): void
    {
        $room = Room::firstOrFail();

        $response = $this->post(route('utility-readings.store'), [
            'room_id' => $room->id,
            'reading_month' => 9,
            'reading_year' => 2026,
            'previous_electricity' => 120,
            'current_electricity' => 130,
            'previous_water' => 35,
            'current_water' => 40,
            'electricity_unit_price' => 3500,
            'water_unit_price' => 12000,
        ]);

        $response->assertSessionHasErrors('reading_month');
    }

    public function test_invoice_total_is_calculated_on_the_server_from_its_items(): void
    {
        $contract = Contract::firstOrFail();

        $response = $this->post(route('invoices.store'), [
            'contract_id' => $contract->id,
            'billing_month' => 10,
            'billing_year' => 2026,
            'items' => [
                ['item_type' => 'room_fee', 'quantity' => 1, 'unit_price' => 450000],
                ['item_type' => 'electricity', 'quantity' => 10, 'unit_price' => 3500],
            ],
        ]);

        $response->assertRedirect();
        $invoice = Invoice::where('contract_id', $contract->id)->where('billing_month', 10)->firstOrFail();
        $this->assertSame('485000.00', $invoice->total_amount);
        $this->assertSame('unpaid', $invoice->status);
        $this->assertCount(2, $invoice->items);
    }
}
