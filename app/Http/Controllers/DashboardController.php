<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Student, Enquiry, Admission, Batch, Course, Payment, Expense, StudentAttendance, Exam, Branch
};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedBranchId = $request->get('branch_id', session('active_branch_id'));
        if ($selectedBranchId) {
            session(['active_branch_id' => $selectedBranchId]);
        }

        $branchQuery = function($query) use ($selectedBranchId) {
            if ($selectedBranchId && $selectedBranchId !== 'all') {
                $query->where('branch_id', $selectedBranchId);
            }
        };

        // Key Metrics
        $totalStudents = Student::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)->count();
        $totalEnquiries = Enquiry::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)->count();
        $totalBatches = Batch::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)->where('status', 'active')->count();
        
        $totalFeeCollected = Payment::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)->sum('amount');
        $totalExpenses = Expense::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)->sum('amount');
        $netOperatingBalance = $totalFeeCollected - $totalExpenses;

        $newLeadsThisMonth = Enquiry::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $convertedLeads = Enquiry::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)
            ->where('status', 'converted')
            ->count();

        $conversionRate = $totalEnquiries > 0 ? round(($convertedLeads / $totalEnquiries) * 100, 1) : 0;

        // Recent Activity
        $recentEnquiries = Enquiry::with(['course', 'source', 'assignedTo'])
            ->when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['student', 'feeDue'])
            ->when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)
            ->latest()
            ->take(5)
            ->get();

        $activeBatches = Batch::with(['course', 'mentor', 'classroom'])
            ->when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)
            ->where('status', 'active')
            ->take(5)
            ->get();

        // Lead Status Breakdown
        $leadStats = Enquiry::when($selectedBranchId && $selectedBranchId !== 'all', $branchQuery)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Branches for switcher
        $branches = Branch::all();

        return view('dashboard.index', compact(
            'totalStudents', 'totalEnquiries', 'totalBatches', 'totalFeeCollected',
            'totalExpenses', 'netOperatingBalance', 'newLeadsThisMonth', 'conversionRate',
            'recentEnquiries', 'recentPayments', 'activeBatches', 'leadStats', 'branches',
            'selectedBranchId'
        ));
    }
}
