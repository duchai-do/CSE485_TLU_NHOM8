<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\UtilityReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with([
                'contract.allocation.student.user',
                'contract.allocation.bed.room.building',
            ])
            ->when(
                $request->filled('month'),
                fn ($query) => $query->where(
                    'billing_month',
                    $request->integer('month')
                )
            )
            ->when(
                $request->filled('year'),
                fn ($query) => $query->where(
                    'billing_year',
                    $request->integer('year')
                )
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->input('status')
                )
            )
            ->latest('billing_year')
            ->latest('billing_month')
            ->paginate(15)
            ->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        // Chỉ cho chọn hợp đồng của sinh viên đang ở thực tế.
        $contracts = Contract::with([
                'allocation.student.user',
                'allocation.bed.room.building',
            ])
            ->where('status', 'active')
            ->whereHas(
                'allocation',
                fn ($allocationQuery) => $allocationQuery
                    ->where('status', 'active')
                    ->whereHas(
                        'bed',
                        fn ($bedQuery) => $bedQuery->where(
                            'status',
                            'occupied'
                        )
                    )
            )
            ->orderByDesc('id')
            ->get();

        return view('invoices.create', compact('contracts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contract_id' => [
                'required',
                'integer',
                Rule::exists('contracts', 'id'),
            ],
            'billing_month' => [
                'required',
                'integer',
                'between:1,12',
            ],
            'billing_year' => [
                'required',
                'integer',
                'between:2000,2100',
            ],
            'due_date' => [
                'nullable',
                'date',
            ],
        ]);

        $contract = Contract::with([
                'allocation.student.user',
                'allocation.bed.room.building',
            ])
            ->findOrFail($data['contract_id']);

        $allocation = $contract->allocation;
        $bed = $allocation?->bed;
        $room = $bed?->room;

        // Chặn stale contract / phòng trống.
        if (
            $contract->status !== 'active'
            || ! $allocation
            || $allocation->status !== 'active'
            || ! $bed
            || $bed->status !== 'occupied'
            || ! $room
        ) {
            throw ValidationException::withMessages([
                'contract_id' =>
                    'Hợp đồng không thuộc sinh viên đang ở thực tế nên không được tạo hóa đơn.',
            ]);
        }

        $exists = Invoice::where('contract_id', $contract->id)
            ->where('billing_month', $data['billing_month'])
            ->where('billing_year', $data['billing_year'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'billing_month' =>
                    'Hợp đồng này đã có hóa đơn trong tháng/năm đã chọn.',
            ]);
        }

        $reading = UtilityReading::where('room_id', $room->id)
            ->where('reading_month', $data['billing_month'])
            ->where('reading_year', $data['billing_year'])
            ->first();

        if (! $reading) {
            throw ValidationException::withMessages([
                'billing_month' =>
                    'Chưa có chỉ số điện nước của phòng '
                    . $room->room_number
                    . ' trong tháng '
                    . $data['billing_month']
                    . '/'
                    . $data['billing_year']
                    . '.',
            ]);
        }

        // Đếm đúng số sinh viên đang ở thực tế trong phòng để chia điện nước.
        $occupantCount = Contract::where('status', 'active')
            ->whereHas(
                'allocation',
                function ($allocationQuery) use ($room) {
                    $allocationQuery
                        ->where('status', 'active')
                        ->whereHas(
                            'bed',
                            fn ($bedQuery) => $bedQuery
                                ->where('room_id', $room->id)
                                ->where('status', 'occupied')
                        );
                }
            )
            ->count();

        if ($occupantCount <= 0) {
            throw ValidationException::withMessages([
                'contract_id' =>
                    'Phòng hiện không có sinh viên đang ở nên không được tạo hóa đơn.',
            ]);
        }

        $electricityUsed = round(
            (float) $reading->current_electricity
            - (float) $reading->previous_electricity,
            2
        );

        $waterUsed = round(
            (float) $reading->current_water
            - (float) $reading->previous_water,
            2
        );

        $electricityShare = round(
            (
                $electricityUsed
                * (float) $reading->electricity_unit_price
            ) / $occupantCount,
            2
        );

        $waterShare = round(
            (
                $waterUsed
                * (float) $reading->water_unit_price
            ) / $occupantCount,
            2
        );

        $roomFee = (float) ($contract->monthly_price ?? 0);

        $invoice = DB::transaction(function () use (
            $data,
            $contract,
            $reading,
            $occupantCount,
            $electricityShare,
            $waterShare,
            $roomFee
        ) {
            $invoice = Invoice::create([
                'contract_id' => $contract->id,
                'invoice_code' => sprintf(
                    'HD-%04d%02d-C%04d',
                    $data['billing_year'],
                    $data['billing_month'],
                    $contract->id
                ),
                'billing_month' => $data['billing_month'],
                'billing_year' => $data['billing_year'],
                'due_date' => $data['due_date'] ?? null,
                'status' => 'unpaid',
                'created_by' => auth()->id(),
                'total_amount' => 0,
            ]);

            $total = 0;

            if ($roomFee > 0) {
                $invoice->items()->create([
                    'item_type' => 'room_fee',
                    'description' => 'Tiền phòng ký túc xá',
                    'quantity' => 1,
                    'unit_price' => $roomFee,
                    'amount' => $roomFee,
                ]);

                $total += $roomFee;
            }

            $invoice->items()->create([
                'item_type' => 'electricity',
                'description' =>
                    'Tiền điện phòng: '
                    . $reading->previous_electricity
                    . ' → '
                    . $reading->current_electricity
                    . ' kWh; chia '
                    . $occupantCount
                    . ' người',
                'quantity' => 1,
                'unit_price' => $electricityShare,
                'amount' => $electricityShare,
            ]);

            $total += $electricityShare;

            $invoice->items()->create([
                'item_type' => 'water',
                'description' =>
                    'Tiền nước phòng: '
                    . $reading->previous_water
                    . ' → '
                    . $reading->current_water
                    . ' m³; chia '
                    . $occupantCount
                    . ' người',
                'quantity' => 1,
                'unit_price' => $waterShare,
                'amount' => $waterShare,
            ]);

            $total += $waterShare;

            $invoice->update([
                'total_amount' => round($total, 2),
            ]);

            return $invoice;
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with(
                'success',
                'Đã tạo hóa đơn cho sinh viên đang ở thực tế.'
            );
    }

    public function show(Invoice $invoice)
    {
        $invoice->load([
            'items',
            'contract.allocation.student.user',
            'contract.allocation.bed.room.building',
        ]);

        return view('invoices.show', compact('invoice'));
    }

    public function markPaid(Invoice $invoice)
    {
        if ($invoice->status === 'cancelled') {
            return back()->withErrors([
                'status' => 'Không thể thanh toán hóa đơn đã hủy.',
            ]);
        }

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with(
            'success',
            'Đã cập nhật hóa đơn thành đã thanh toán.'
        );
    }

    public function print(Invoice $invoice)
    {
        $invoice->load([
            'items',
            'contract.allocation.student.user',
            'contract.allocation.bed.room.building',
        ]);

        return view('invoices.print', compact('invoice'));
    }

    public function revenue(Request $request)
    {
        $year = $request->integer('year') ?: now()->year;

        $revenue = Invoice::where('status', 'paid')
            ->where('billing_year', $year)
            ->selectRaw(
                'billing_month, SUM(total_amount) as total'
            )
            ->groupBy('billing_month')
            ->orderBy('billing_month')
            ->get();

        return view(
            'invoices.revenue',
            compact('revenue', 'year')
        );
    }
}