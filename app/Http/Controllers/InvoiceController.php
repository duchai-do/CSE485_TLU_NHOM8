<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('contract.allocation.student.user')
            ->when($request->filled('month'), fn ($query) => $query->where('billing_month', $request->integer('month')))
            ->when($request->filled('year'), fn ($query) => $query->where('billing_year', $request->integer('year')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest('billing_year')->latest('billing_month')->paginate(15)->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('invoices.create', ['contracts' => Contract::with('allocation.student.user')->where('status', 'active')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contract_id' => ['required', 'integer', Rule::exists('contracts', 'id')],
            'invoice_code' => ['nullable', 'string', 'max:50', Rule::unique('invoices', 'invoice_code')],
            'billing_month' => ['required', 'integer', 'between:1,12'],
            'billing_year' => ['required', 'integer', 'between:2000,2100'],
            'due_date' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', 'string', 'max:50'],
            'items.*.description' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        if (Invoice::where('contract_id', $data['contract_id'])->where('billing_month', $data['billing_month'])->where('billing_year', $data['billing_year'])->exists()) {
            throw ValidationException::withMessages(['billing_month' => 'Hợp đồng này đã có hóa đơn trong tháng/năm đã chọn.']);
        }

        $invoice = DB::transaction(function () use ($data) {
            $code = ($data['invoice_code'] ?? null) ?: sprintf('HD-%04d%02d-%04d', $data['billing_year'], $data['billing_month'], Invoice::count() + 1);
            $invoice = Invoice::create([
                'contract_id' => $data['contract_id'], 'invoice_code' => $code,
                'billing_month' => $data['billing_month'], 'billing_year' => $data['billing_year'],
                'due_date' => $data['due_date'] ?? null, 'status' => 'unpaid', 'created_by' => auth()->id(),
            ]);
            $total = 0;
            foreach ($data['items'] as $item) {
                $amount = round($item['quantity'] * $item['unit_price'], 2);
                $invoice->items()->create($item + ['amount' => $amount]);
                $total += $amount;
            }
            $invoice->update(['total_amount' => $total]);

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Đã tạo hóa đơn và tính tổng tiền.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items', 'contract.allocation.student.user');

        return view('invoices.show', compact('invoice'));
    }

    public function markPaid(Invoice $invoice)
    {
        if ($invoice->status === 'cancelled') {
            return back()->withErrors(['status' => 'Không thể thanh toán hóa đơn đã hủy.']);
        }
        $invoice->update(['status' => 'paid', 'paid_at' => now()]);

        return back()->with('success', 'Đã cập nhật hóa đơn thành đã thanh toán.');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('items', 'contract.allocation.student.user');

        return view('invoices.print', compact('invoice'));
    }

    public function revenue(Request $request)
    {
        $year = $request->integer('year') ?: now()->year;
        $revenue = Invoice::where('status', 'paid')->where('billing_year', $year)
            ->selectRaw('billing_month, SUM(total_amount) as total')->groupBy('billing_month')->orderBy('billing_month')->get();

        return view('invoices.revenue', compact('revenue', 'year'));
    }
}
