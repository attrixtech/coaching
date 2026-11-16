<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{
    User, Student, Enquiry, Admission, Batch, Course, StudentFeeDue, Payment,
    Exam, Certificate, Expense, Classroom, Branch, AcademicSession
};

class CoachingErpWorkflowTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('email', 'admin@coaching.com')->first();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@coaching.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_dashboard_renders_for_authenticated_admin(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Webotapp Academy');
        $response->assertSee('Executive Operations Dashboard');
    }

    public function test_enquiries_index_and_creation(): void
    {
        $response = $this->actingAs($this->admin)->get('/enquiries');
        $response->assertStatus(200);
        $response->assertSee('Enquiries');

        $course = Course::first();
        $postResponse = $this->actingAs($this->admin)->post('/enquiries', [
            'branch_id' => 1,
            'student_name' => 'Kunal Kashyap',
            'phone' => '+91 99000 88000',
            'email' => 'kunal.test@example.com',
            'interested_course_id' => $course->id,
            'priority' => 'high',
            'enquiry_date' => date('Y-m-d'),
            'remarks' => 'Test Lead Inquirer',
        ]);

        $this->assertDatabaseHas('enquiries', [
            'student_name' => 'Kunal Kashyap',
            'phone' => '+91 99000 88000',
        ]);
    }

    public function test_admission_creation_with_installments_and_downpayment(): void
    {
        $course = Course::first();
        $batch = Batch::first();
        $branch = Branch::first();

        $postData = [
            'branch_id' => $branch->id,
            'course_id' => $course->id,
            'batch_id' => $batch->id,
            'first_name' => 'Pooja',
            'last_name' => 'Menon',
            'gender' => 'female',
            'date_of_birth' => '2004-08-20',
            'phone' => '+91 99112 33445',
            'email' => 'pooja.menon@example.com',
            'father_name' => 'Narayana Menon',
            'father_phone' => '+91 99112 33446',
            'current_address' => 'Indiranagar 2nd Stage',
            'city' => 'Bengaluru',
            'total_course_fee' => $course->total_fee,
            'discount_amount' => 5000,
            'discount_reason' => 'Academic Merit',
            'fee_plan' => 'installments',
            'installments_count' => 3,
            'collected_downpayment' => 15000,
            'payment_method' => 'upi',
        ];

        $response = $this->actingAs($this->admin)->post('/admissions', $postData);
        $response->assertRedirect();

        $this->assertDatabaseHas('students', [
            'first_name' => 'Pooja',
            'last_name' => 'Menon',
            'email' => 'pooja.menon@example.com',
        ]);

        $student = Student::where('email', 'pooja.menon@example.com')->first();
        $this->assertNotNull($student);

        // Assert admission created
        $this->assertDatabaseHas('admissions', [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'discount_amount' => 5000,
        ]);

        // Assert 3 installments generated
        $this->assertEquals(3, StudentFeeDue::where('student_id', $student->id)->count());

        // Assert payment recorded for downpayment
        $this->assertDatabaseHas('payments', [
            'student_id' => $student->id,
            'amount' => 15000,
        ]);
    }

    public function test_student_directory_and_profile(): void
    {
        $student = Student::first();
        $response = $this->actingAs($this->admin)->get('/students/' . $student->id);
        $response->assertStatus(200);
        $response->assertSee($student->full_name);
        $response->assertSee($student->admission_no);
    }

    public function test_attendance_marking(): void
    {
        $batch = Batch::first();
        $student = $batch->students->first();

        $response = $this->actingAs($this->admin)->post('/attendance', [
            'batch_id' => $batch->id,
            'attendance_date' => date('Y-m-d'),
            'attendance' => [
                $student->id => 'present',
            ],
            'remarks' => [
                $student->id => 'Punctual attendance',
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('student_attendances', [
            'batch_id' => $batch->id,
            'student_id' => $student->id,
            'attendance_date' => date('Y-m-d'),
            'status' => 'present',
        ]);
    }

    public function test_courses_and_batches(): void
    {
        $response = $this->actingAs($this->admin)->get('/courses');
        $response->assertStatus(200);
        $response->assertSee('Web Development');

        $batchResponse = $this->actingAs($this->admin)->get('/batches');
        $batchResponse->assertStatus(200);
    }

    public function test_fee_dues_and_payment_receipt(): void
    {
        $due = StudentFeeDue::where('status', '!=', 'paid')->first() ?? StudentFeeDue::first();
        $paymentResponse = $this->actingAs($this->admin)->post('/fees/pay', [
            'student_fee_due_id' => $due->id,
            'amount' => 5000,
            'payment_method' => 'upi',
            'payment_date' => date('Y-m-d'),
            'transaction_reference' => 'UPI99887766',
            'remarks' => 'Automated test fee settlement',
        ]);

        $this->assertDatabaseHas('payments', [
            'student_fee_due_id' => $due->id,
            'transaction_reference' => 'UPI99887766',
        ]);

        $payment = Payment::where('transaction_reference', 'UPI99887766')->first();
        $receiptResponse = $this->actingAs($this->admin)->get('/fees/receipt/' . $payment->id);
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee($payment->receipt_no);
    }

    public function test_exam_report_card_view(): void
    {
        $exam = Exam::first();
        $student = $exam->batch->students->first();

        $response = $this->actingAs($this->admin)->get("/exams/{$exam->id}/report-card/{$student->id}");
        $response->assertStatus(200);
        $response->assertSee('Official Student Scorecard');
    }

    public function test_public_certificate_verification(): void
    {
        $cert = Certificate::first();
        $response = $this->get('/verify-certificate/' . $cert->verification_code);
        $response->assertStatus(200);
        $response->assertSee('Valid & Authenticated Credential', false);
        $response->assertSee($cert->certificate_no);
    }

    public function test_expense_logging(): void
    {
        $branch = Branch::first();
        $response = $this->actingAs($this->admin)->post('/expenses', [
            'branch_id' => $branch->id,
            'category' => 'Utilities',
            'title' => 'Emergency Generator Fuel',
            'amount' => 4500,
            'expense_date' => date('Y-m-d'),
            'payment_method' => 'cash',
            'paid_to' => 'City Fuel Station',
            'remarks' => 'Power backup fuel voucher',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expenses', [
            'title' => 'Emergency Generator Fuel',
            'amount' => 4500,
        ]);
    }
}
