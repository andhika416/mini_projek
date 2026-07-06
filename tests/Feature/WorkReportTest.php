<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_work_report(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('work-reports.store'), [
            'input_date' => now()->format('Y-m-d'),
            'latitude' => -6.2000000,
            'longitude' => 106.8166667,
            'start_time' => '08:00',
            'end_time' => '16:00',
            'work_plan' => 'Menyusun laporan bulanan.',
            'work_activity' => 'Mengumpulkan dan memvalidasi data.',
            'work_result' => 'Laporan selesai disusun.',
        ]);

        $response->assertRedirect(route('work-reports.index'));
        $this->assertDatabaseHas('work_reports', ['user_id' => $user->id]);
    }

    public function test_user_cannot_view_another_users_report(): void
    {
        $user = User::factory()->create();
        $report = WorkReport::factory()->create();

        $this->actingAs($user)->get(route('work-reports.show', $report))->assertForbidden();
    }

    public function test_admin_can_view_another_users_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $report = WorkReport::factory()->create();

        $this->actingAs($admin)->get(route('work-reports.show', $report))->assertOk();
    }

    public function test_regular_user_cannot_access_user_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_user_can_download_their_report_as_pdf(): void
    {
        $user = User::factory()->create();
        $report = WorkReport::factory()->for($user)->create();

        $this->actingAs($user)
            ->get(route('work-reports.pdf', $report))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
