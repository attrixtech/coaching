@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    courseFee: {{ $enquiry && $enquiry->course ? $enquiry->course->total_fee : ($courses->first()->total_fee ?? 0) }},
    discount: 0,
    installments: 3,
    downPayment: 0,
    get netFee() { return Math.max(0, this.courseFee - this.discount); },
    get installmentVal() { return this.installments > 0 ? (this.netFee / this.installments).toFixed(2) : 0; },
    updateCourse(e) {
        const opt = e.target.selectedOptions[0];
        if (opt) {
            this.courseFee = parseFloat(opt.dataset.fee || 0);
        }
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Student Admission & Enrollment</h1>
            <p class="text-xs text-slate-500 mt-1">Issue official student registration, allocate batch and generate fee schedule.</p>
        </div>
        <a href="{{ route('admissions.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Admissions</span>
        </a>
    </div>

    @if($enquiry)
    <div class="bg-indigo-50 border border-indigo-200 text-indigo-900 p-4 rounded-2xl flex items-center justify-between text-xs">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-indigo-600 text-base"></i>
            <div>
                <span class="font-bold">Converting Lead:</span> {{ $enquiry->student_name }} ({{ $enquiry->enquiry_no }})
                &bull; Interested in: <strong class="underline">{{ $enquiry->course->name ?? 'Course' }}</strong>
            </div>
        </div>
        <span class="text-[10px] uppercase font-bold text-indigo-700 bg-white px-2 py-0.5 rounded shadow-xs">CRM Converted</span>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('admissions.store') }}" method="POST" class="space-y-6">
            @csrf
            @if($enquiry)
                <input type="hidden" name="enquiry_id" value="{{ $enquiry->id }}">
            @endif

            <!-- Organization & Academic Master Allocation -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-6 border-b border-slate-100">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Campus / Branch <span class="text-rose-500">*</span></label>
                    <select name="branch_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $enquiry && $enquiry->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Course Program <span class="text-rose-500">*</span></label>
                    <select name="course_id" required @change="updateCourse($event)" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" data-fee="{{ $c->total_fee }}" {{ $enquiry && $enquiry->interested_course_id == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} (₹{{ number_format($c->total_fee) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Allocated Class Batch <span class="text-rose-500">*</span></label>
                    <select name="batch_id" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">
                                {{ $batch->name }} ({{ $batch->delivery_mode }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Student Demographics -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Student Demographics</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">First Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="first_name" value="{{ $enquiry ? explode(' ', $enquiry->student_name)[0] : old('first_name') }}" required placeholder="First name" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Last Name</label>
                        <input type="text" name="last_name" value="{{ $enquiry && strpos($enquiry->student_name, ' ') !== false ? substr($enquiry->student_name, strpos($enquiry->student_name, ' ') + 1) : old('last_name') }}" placeholder="Last name" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Gender <span class="text-rose-500">*</span></label>
                        <select name="gender" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="2005-06-15" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Student Mobile <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" value="{{ $enquiry ? $enquiry->phone : old('phone') }}" required placeholder="+91 98765 43210" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Student Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ $enquiry ? $enquiry->email : old('email') }}" required placeholder="student@example.com" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Parent Details -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Guardian Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Father / Guardian Name</label>
                        <input type="text" name="father_name" value="{{ $enquiry ? $enquiry->father_or_guardian_name : old('father_name') }}" placeholder="Guardian name" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Father / Guardian Phone</label>
                        <input type="text" name="father_phone" placeholder="+91 98765 00000" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Residential City</label>
                        <input type="text" name="city" value="Bengaluru" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Financial Calculation & Fee Schedule -->
            <div class="pt-4 border-t border-slate-100 bg-slate-50/70 p-5 rounded-2xl border border-slate-200/60 space-y-4">
                <h3 class="text-xs font-bold text-brand-700 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-calculator"></i>
                    <span>Fee Structure & Payment Schedule Setup</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Base Course Fee (incl. GST)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400 font-bold text-xs">₹</span>
                            <input type="number" name="total_course_fee" x-model.number="courseFee" required class="w-full pl-7 pr-3 py-2 text-xs bg-white border border-slate-300 rounded-xl font-bold text-slate-800">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Scholarship / Fee Waiver</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-400 font-bold text-xs">₹</span>
                            <input type="number" name="discount_amount" x-model.number="discount" min="0" class="w-full pl-7 pr-3 py-2 text-xs bg-white border border-slate-300 rounded-xl font-semibold text-emerald-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Payment Plan</label>
                        <select name="fee_plan" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2">
                            <option value="installments">Flexible Installments</option>
                            <option value="full_upfront">100% Upfront Lump-Sum</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Number of Installments</label>
                        <select name="installments_count" x-model.number="installments" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2">
                            <option value="1">1 (Single Payment)</option>
                            <option value="2">2 Installments (50/50)</option>
                            <option value="3">3 Installments (Standard)</option>
                            <option value="4">4 Quarterly Installments</option>
                            <option value="6">6 Monthly Installments</option>
                        </select>
                    </div>
                </div>

                <!-- Live Summary Pill -->
                <div class="flex flex-wrap items-center justify-between p-3 bg-white rounded-xl border border-slate-200 text-xs">
                    <div>
                        <span class="text-slate-500">Agreed Net Course Fee:</span>
                        <strong class="text-base text-brand-700 ml-1">₹<span x-text="netFee.toLocaleString()"></span></strong>
                    </div>
                    <div>
                        <span class="text-slate-500">Per-Installment Dues:</span>
                        <strong class="text-sm text-slate-800 ml-1">₹<span x-text="parseFloat(installmentVal).toLocaleString()"></span></strong>
                        <span class="text-[10px] text-slate-400"> x <span x-text="installments"></span> periods</span>
                    </div>
                </div>

                <!-- Instant Downpayment Settlement at Desk -->
                <div class="pt-3 border-t border-slate-200/80">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Collect Down-Payment Now (Optional)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-slate-400 font-bold text-xs">₹</span>
                                <input type="number" name="collected_downpayment" placeholder="e.g. 15000" class="w-full pl-7 pr-3 py-2 text-xs bg-white border border-slate-300 rounded-xl font-bold text-emerald-600">
                            </div>
                            <span class="text-[10px] text-slate-400 mt-1 block">Issues official receipt and settles 1st installment immediately.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Payment Settlement Method</label>
                            <select name="payment_method" class="w-full text-xs bg-white border border-slate-300 rounded-xl px-3 py-2">
                                <option value="upi">UPI / QR Code (PhonePe, GPay, Paytm)</option>
                                <option value="cash">Cash Settlement</option>
                                <option value="card">Credit / Debit Card Terminal</option>
                                <option value="net_banking">Direct Bank IMPS / NEFT</option>
                                <option value="cheque">Bank Cheque / DD</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admissions.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
                    Confirm Admission & Issue Enrolment
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

