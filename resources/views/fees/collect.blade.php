@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Cashier Payment Collection Desk</h1>
            <p class="text-xs text-slate-500 mt-1">Receive installment payment, record transaction mode, and issue official GST receipt.</p>
        </div>
        <a href="{{ route('fees.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Fee Dues</span>
        </a>
    </div>

    @if($due)
    <div class="bg-indigo-50 border border-indigo-200 text-indigo-900 p-4 rounded-2xl flex items-center justify-between text-xs">
        <div>
            <span class="font-bold">Collecting For:</span> {{ $due->student->full_name }} ({{ $due->student->admission_no }})
            <div class="text-[11px] text-indigo-700 mt-0.5">
                {{ $due->title }} &bull; Outstanding Balance: <strong class="text-rose-600 font-bold">₹{{ number_format($due->balance_amount, 2) }}</strong>
            </div>
        </div>
        <span class="text-[10px] uppercase font-bold text-indigo-700 bg-white px-2 py-0.5 rounded shadow-xs">Due #{{ $due->installment_no }}</span>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('fees.pay') }}" method="POST" class="space-y-5 text-xs">
            @csrf

            @if($due)
                <input type="hidden" name="student_fee_due_id" value="{{ $due->id }}">
            @else
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Select Pending Due Record <span class="text-rose-500">*</span></label>
                    <select name="student_fee_due_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                        @foreach(\App\Models\StudentFeeDue::where('status', '!=', 'paid')->with('student')->get() as $d)
                            <option value="{{ $d->id }}">
                                {{ $d->student->full_name ?? 'Candidate' }} — {{ $d->title }} (Due: ₹{{ number_format($d->balance_amount) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Collection Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Amount Being Settled (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount" value="{{ $due ? $due->balance_amount : 10000 }}" required min="1" step="0.5" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-900 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Payment Method <span class="text-rose-500">*</span></label>
                    <select name="payment_method" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                        <option value="upi">UPI / QR (PhonePe / GPay / Paytm)</option>
                        <option value="cash">Cash Collection</option>
                        <option value="card">Card POS Terminal</option>
                        <option value="net_banking">Net Banking / IMPS / NEFT</option>
                        <option value="cheque">Bank Cheque / DD</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Transaction ID / Bank Reference</label>
                    <input type="text" name="transaction_reference" placeholder="e.g. UPI Ref / Cheque No / Card Auth" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Cashier Notes / Receipt Remarks</label>
                <textarea name="remarks" rows="2" placeholder="Fee received against installment schedule. Settled by student/parent..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('fees.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-semibold shadow-md shadow-brand-600/30 transition">
                    Confirm Receipt & Settle Dues
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
