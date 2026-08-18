<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\UtilityReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UtilityReadingController extends Controller
{
    public function index(Request $request)
    {
        $readings = UtilityReading::with('room.building')
            ->when(
                $request->filled('month'),
                fn ($query) => $query->where(
                    'reading_month',
                    $request->integer('month')
                )
            )
            ->when(
                $request->filled('year'),
                fn ($query) => $query->where(
                    'reading_year',
                    $request->integer('year')
                )
            )
            ->latest('reading_year')
            ->latest('reading_month')
            ->paginate(15)
            ->withQueryString();

        return view('utility-readings.index', compact('readings'));
    }

    public function create()
    {
        $rooms = Room::with('building')
            ->orderBy('building_id')
            ->orderBy('room_number')
            ->get();

        return view('utility-readings.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => [
                'required',
                'integer',
                Rule::exists('rooms', 'id'),
            ],
            'reading_month' => [
                'required',
                'integer',
                'between:1,12',
            ],
            'reading_year' => [
                'required',
                'integer',
                'between:2000,2100',
            ],
            'previous_electricity' => [
                'required',
                'numeric',
                'min:0',
            ],
            'current_electricity' => [
                'required',
                'numeric',
                'gte:previous_electricity',
            ],
            'previous_water' => [
                'required',
                'numeric',
                'min:0',
            ],
            'current_water' => [
                'required',
                'numeric',
                'gte:previous_water',
            ],
            'electricity_unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'water_unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $exists = UtilityReading::where('room_id', $data['room_id'])
            ->where('reading_month', $data['reading_month'])
            ->where('reading_year', $data['reading_year'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'reading_month' =>
                    'Phòng này đã có chỉ số điện nước trong tháng/năm đã chọn.',
            ]);
        }

        $result = DB::transaction(function () use ($data) {
            $reading = UtilityReading::create([
                'room_id' => $data['room_id'],
                'reading_month' => $data['reading_month'],
                'reading_year' => $data['reading_year'],
                'previous_electricity' => $data['previous_electricity'],
                'current_electricity' => $data['current_electricity'],
                'previous_water' => $data['previous_water'],
                'current_water' => $data['current_water'],
                'electricity_unit_price' => $data['electricity_unit_price'],
                'water_unit_price' => $data['water_unit_price'],
                'recorded_by' => auth()->id(),
                'recorded_at' => now(),
            ]);

            // Chỉ lấy sinh viên đang ở thực tế trong đúng phòng:
            // contract active + allocation active + bed occupied.
            $contracts = Contract::query()
                ->with([
                    'allocation.student.user',
                    'allocation.bed.room.building',
                ])
                ->where('status', 'active')
                ->whereHas(
                    'allocation',
                    function ($allocationQuery) use ($reading) {
                        $allocationQuery
                            ->where('status', 'active')
                            ->whereHas(
                                'bed',
                                function ($bedQuery) use ($reading) {
                                    $bedQuery
                                        ->where('room_id', $reading->room_id)
                                        ->where('status', 'occupied');
                                }
                            );
                    }
                )
                ->get();

            $occupantCount = $contracts->count();

            // Phòng trống: chỉ lưu chỉ số, không sinh hóa đơn.
            if ($occupantCount === 0) {
                return [
                    'created_invoices' => 0,
                    'skipped_invoices' => 0,
                    'occupant_count' => 0,
                ];
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

            $electricityTotal = round(
                $electricityUsed
                * (float) $reading->electricity_unit_price,
                2
            );

            $waterTotal = round(
                $waterUsed
                * (float) $reading->water_unit_price,
                2
            );

            // Điện/nước là chi phí chung của phòng -> chia đều người đang ở.
            $electricityShare = round(
                $electricityTotal / $occupantCount,
                2
            );

            $waterShare = round(
                $waterTotal / $occupantCount,
                2
            );

            $created = 0;
            $skipped = 0;

            foreach ($contracts as $contract) {
                $alreadyExists = Invoice::where(
                        'contract_id',
                        $contract->id
                    )
                    ->where(
                        'billing_month',
                        $reading->reading_month
                    )
                    ->where(
                        'billing_year',
                        $reading->reading_year
                    )
                    ->exists();

                if ($alreadyExists) {
                    $skipped++;
                    continue;
                }

                $roomFee = (float) ($contract->monthly_price ?? 0);

                $invoice = Invoice::create([
                    'contract_id' => $contract->id,
                    'invoice_code' => sprintf(
                        'HD-%04d%02d-C%04d',
                        $reading->reading_year,
                        $reading->reading_month,
                        $contract->id
                    ),
                    'billing_month' => $reading->reading_month,
                    'billing_year' => $reading->reading_year,
                    'total_amount' => 0,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'paid_at' => null,
                    'status' => 'unpaid',
                    'created_by' => auth()->id(),
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

                $created++;
            }

            return [
                'created_invoices' => $created,
                'skipped_invoices' => $skipped,
                'occupant_count' => $occupantCount,
            ];
        });

        if ($result['occupant_count'] === 0) {
            return redirect()
                ->route('utility-readings.index')
                ->with(
                    'success',
                    'Đã lưu chỉ số điện nước. Phòng không có sinh viên đang ở/hợp đồng active nên không tạo hóa đơn.'
                );
        }

        return redirect()
            ->route('invoices.index')
            ->with(
                'success',
                'Đã lưu chỉ số và tự động tạo '
                . $result['created_invoices']
                . ' hóa đơn cho '
                . $result['occupant_count']
                . ' sinh viên đang ở phòng.'
                . (
                    $result['skipped_invoices'] > 0
                    ? ' Bỏ qua '
                        . $result['skipped_invoices']
                        . ' hóa đơn đã tồn tại.'
                    : ''
                )
            );
    }
}