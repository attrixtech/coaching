@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- STUDENT WELCOME BANNER -->
    <div class="bg-gradient-to-r from-brand-700 via-indigo-700 to-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md text-white font-extrabold text-2xl flex items-center justify-center border border-white/20 shadow-inner">
                    {{ substr($student->first_name, 0, 1) }}
                </div>
                <div>
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-400 text-slate-950 mb-1">
                        Student Academic Portal
                    </span>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Welcome, {{ $student->full_name }}!</h1>
                    <p class="text-xs text-indigo-200 mt-0.5">
                        Admission No: <strong class="text-white font-mono">{{ $student->admission_no }}</strong> &bull;
                        Roll No: <strong class="text-white font-mono">{{ $student->roll_no }}</strong> &bull;
                        Campus: <span class="text-white">{{ $student->branch->name ?? 'Indiranagar Central' }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('students.show', $student) }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-semibold backdrop-blur-sm border border-white/20 transition">
                    View Complete 360° Profile
                </a>
            </div>
        </div>
    </div>

    <!-- 4 STUDENT METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Attendance Metric -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Attendance Rate</span>
                <div class="text-2xl font-bold mt-1 {{ $attendancePct >= 75 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $attendancePct }}%
                </div>
                <div class="text-[11px] text-slate-400 mt-1">
                    {{ $attendancePct >= 75 ? 'Eligible for Examination' : 'Attendance Warning (<75%)' }}
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-clipboard-user"></i>
            </div>
        </div>

        <!-- Pending Fee Balance -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Fee Dues</span>
                <div class="text-2xl font-bold mt-1 {{ $pendingFeeBalance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    ₹{{ number_format($pendingFeeBalance, 2) }}
                </div>
                <div class="text-[11px] text-slate-400 mt-1">
                    Paid to Date: ₹{{ number_format($totalFeePaid, 0) }}
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <!-- Active Batches -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Enrolled Batches</span>
                <div class="text-2xl font-bold text-brand-700 mt-1">{{ $student->batches->count() }} Cohorts</div>
                <div class="text-[11px] text-slate-400 mt-1">Active class enrollment</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-users-rectangle"></i>
            </div>
        </div>

        <!-- Exam Assessments -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Completed Assessments</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $student->examAttempts->count() }} Exams</div>
                <div class="text-[11px] text-emerald-600 font-semibold mt-1">
                    Certificates: {{ $student->certificates->count() }}
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-award"></i>
            </div>
        </div>
    </div>

    <!-- STUDENT ACTIVE BATCHES & TIMINGS -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">My Class Batches & Daily Timetable</h2>
                <p class="text-xs text-slate-400">Classroom rooms, timings and assigned faculty mentors</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Cohort Batch</th>
                        <th class="px-4 py-3 font-semibold">Program</th>
                        <th class="px-4 py-3 font-semibold">Mentor</th>
                        <th class="px-4 py-3 font-semibold">Classroom</th>
                        <th class="px-4 py-3 font-semibold">Class Timing</th>
                        <th class="px-4 py-3 font-semibold">Delivery Mode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($student->batches as $b)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3 font-bold text-brand-700">{{ $b->name }}</td>
                        <td class="px-4 py-3 font-medium">{{ $b->course->name ?? 'Course' }}</td>
                        <td class="px-4 py-3">{{ $b->mentor->name ?? 'Lead Faculty' }}</td>
                        <td class="px-4 py-3 text-slate-800">{{ $b->classroom->room_name ?? 'Virtual Studio' }}</td>
                        <td class="px-4 py-3 font-mono text-slate-600 font-medium">
                            {{ date('h:i A', strtotime($b->start_time)) }} - {{ date('h:i A', strtotime($b->end_time)) }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-indigo-50 text-indigo-700">
                                {{ $b->delivery_mode }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No active batches enrolled.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2-COLUMNS: ASSIGNMENTS & EXAM SCORECARDS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Upcoming Homework & Assignments -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Upcoming Homework & Project Tasks</h2>
                    <p class="text-xs text-slate-400">Deadlines assigned by faculty mentors</p>
                </div>
                <a href="{{ route('lms.assignments') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800">View all</a>
            </div>
            <div class="divide-y divide-slate-100 text-xs">
                @forelse($upcomingAssignments as $asg)
                <div class="p-4 hover:bg-slate-50/70 transition flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-900">{{ $asg->title }}</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">
                            {{ $asg->batch->name ?? 'Batch' }} &bull; Max Marks: <strong>{{ $asg->max_marks }}</strong>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[11px] font-mono font-bold text-amber-600 block">
                            Due: {{ date('d M, h:i A', strtotime($asg->due_datetime)) }}
                        </span>
                        <span class="text-[10px] text-slate-400">Pending Submission</span>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-slate-400 italic">No pending assignments! You are all caught up.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Exam Scorecards -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Recent Exam Results & Scorecards</h2>
                    <p class="text-xs text-slate-400">Official academic evaluations</p>
                </div>
            </div>
            <div class="divide-y divide-slate-100 text-xs">
                @forelse($recentExamAttempts as $att)
                <div class="p-4 hover:bg-slate-50/70 transition flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-900">{{ $att->exam->title ?? 'Assessment' }}</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">
                            Subject: {{ $att->exam->subject->name ?? 'General' }}
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <span class="font-bold text-brand-700 block">{{ $att->marks_obtained }} / {{ $att->exam->total_marks ?? 100 }}</span>
                            <span class="text-[10px] font-extrabold text-indigo-700">Grade: {{ $att->grade }} ({{ $att->percentage }}%)</span>
                        </div>
                        <a href="{{ route('exams.report-card', [$att->exam, $student]) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-brand-50 text-brand-700 rounded font-semibold text-[11px]">
                            Scorecard
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-slate-400 italic">No completed examinations yet.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
