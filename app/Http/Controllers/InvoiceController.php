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
        $this->markOverdueInvoices();
        $contracts = $this->activeContracts();
        $invoices = Invoice::with('contract.allocation.student.user', 'contract.allocation.bed.room')
            ->when($request->filled('month'), fn ($q) => $q->where('billing_month', $request->integer('month')))
            ->when($request->filled('year'), fn ($q) => $q->where('billing_year', $request->integer('year')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('room_id'), fn ($q) => $q->whereHas('contract.allocation.bed.room', fn ($room) => $room->whereKey($request->integer('room_id'))))
            ->when($request->filled('student_id'), fn ($q) => $q->whereHas('contract.allocation.student', fn ($student) => $student->whereKey($request->integer('student_id'))))
            ->latest('billing_year')->latest('billing_month')->paginate(15)->withQueryString();
        return view('invoices.index', compact('invoices', 'contracts'));
    }

    public function create()
    {
        return view('invoices.create', ['invoice' => new Invoice(), 'contracts' => $this->activeContracts(), 'itemTypes' => $this->itemTypes()]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $this->ensureUniquePeriod($data);
        $invoice = $this->saveInvoice(new Invoice(), $data);
        return redirect()->route('invoices.show', $invoice)->with('success', 'Đã tạo hóa đơn và tính tổng tiền.');
    }

    public function show(Invoice $invoice)
    {
        $this->markOverdueInvoices();
        $invoice->refresh()->load('items', 'contract.allocation.student.user', 'contract.allocation.bed.room');
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $this->ensureEditable($invoice);
        return view('invoices.create', ['invoice' => $invoice, 'contracts' => $this->activeContracts(), 'itemTypes' => $this->itemTypes()]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->ensureEditable($invoice);
        $data = $this->validatedData($request, $invoice);
        $this->ensureUniquePeriod($data, $invoice);
        $this->saveInvoice($invoice, $data);
        return redirect()->route('invoices.show', $invoice)->with('success', 'Đã cập nhật hóa đơn.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->ensureEditable($invoice);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Đã xóa hóa đơn.');
    }

    public function markPaid(Invoice $invoice)
    {
        if ($invoice->status === 'cancelled') {
            return back()->withErrors(['status' => 'Không thể thanh toán hóa đơn đã hủy.']);
        }
        $invoice->update(['status' => 'paid', 'paid_at' => now()]);
        return back()->with('success', 'Đã cập nhật hóa đơn thành đã thanh toán.');
    }

    public function cancel(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->withErrors(['status' => 'Không thể hủy hóa đơn đã thanh toán.']);
        }
        $invoice->update(['status' => 'cancelled']);
        return back()->with('success', 'Đã hủy hóa đơn.');
    }

    public function print(Invoice $invoice)
    {
        $this->markOverdueInvoices();
        $invoice->refresh()->load('items', 'contract.allocation.student.user', 'contract.allocation.bed.room');
        return view('invoices.print', compact('invoice'));
    }

    public function revenue(Request $request)
    {
        $this->markOverdueInvoices();
        $year = $request->integer('year') ?: now()->year;
        $revenue = Invoice::where('status', 'paid')->where('billing_year', $year)
            ->selectRaw('billing_month, SUM(total_amount) as total')->groupBy('billing_month')->orderBy('billing_month')->get();
        $unpaidTotal = Invoice::whereIn('status', ['unpaid', 'overdue'])->where('billing_year', $year)->sum('total_amount');
        return view('invoices.revenue', compact('revenue', 'year', 'unpaidTotal'));
    }

    private function validatedData(Request $request, ?Invoice $invoice = null): array
    {
        return $request->validate([
            'contract_id' => ['required', 'integer', Rule::exists('contracts', 'id')],
            'invoice_code' => ['nullable', 'string', 'max:50', Rule::unique('invoices', 'invoice_code')->ignore($invoice)],
            'billing_month' => ['required', 'integer', 'between:1,12'],
            'billing_year' => ['required', 'integer', 'between:2000,2100'],
            'due_date' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', Rule::in(array_keys($this->itemTypes()))],
            'items.*.description' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    private function saveInvoice(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            $code = ($data['invoice_code'] ?? null) ?: sprintf('HD-%04d%02d-%04d', $data['billing_year'], $data['billing_month'], Invoice::count() + 1);
            $invoice->fill([
                'contract_id' => $data['contract_id'], 'invoice_code' => $code,
                'billing_month' => $data['billing_month'], 'billing_year' => $data['billing_year'],
                'due_date' => $data['due_date'] ?? null, 'created_by' => $invoice->created_by ?: auth()->id(),
            ]);
            if (! $invoice->exists) $invoice->status = 'unpaid';
            $invoice->save();
            $invoice->items()->delete();
            $total = 0;
            foreach ($data['items'] as $item) {
                $amount = round($item['quantity'] * $item['unit_price'], 2);
                $invoice->items()->create($item + ['amount' => $amount]);
                $total += $amount;
            }
            $invoice->update(['total_amount' => $total]);
            return $invoice;
        });
    }

    private function ensureUniquePeriod(array $data, ?Invoice $ignore = null): void
    {
        $exists = Invoice::where('contract_id', $data['contract_id'])->where('billing_month', $data['billing_month'])->where('billing_year', $data['billing_year'])
            ->when($ignore, fn ($q) => $q->whereKeyNot($ignore->id))->exists();
        if ($exists) throw ValidationException::withMessages(['billing_month' => 'Hợp đồng này đã có hóa đơn trong tháng/năm đã chọn.']);
    }

    private function ensureEditable(Invoice $invoice): void
    {
        if (in_array($invoice->status, ['paid', 'cancelled'], true)) abort(422, 'Chỉ được sửa hoặc xóa hóa đơn chưa thanh toán/quá hạn.');
    }

    private function markOverdueInvoices(): void
    {
        Invoice::where('status', 'unpaid')->whereNotNull('due_date')->whereDate('due_date', '<', today())->update(['status' => 'overdue']);
    }

    private function activeContracts()
    {
        return Contract::with('allocation.student.user', 'allocation.bed.room.building')->where('status', 'active')->get();
    }

    private function itemTypes(): array
    {
        return ['room_fee' => 'Tiền phòng', 'electricity' => 'Tiền điện', 'water' => 'Tiền nước', 'service' => 'Tiền dịch vụ', 'penalty' => 'Tiền phạt'];
    }
}
