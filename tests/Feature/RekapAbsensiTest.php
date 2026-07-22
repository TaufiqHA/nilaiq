<?php

namespace Tests\Feature;

use App\Models\Classes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapAbsensiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_rekap_absensi(): void
    {
        $response = $this->get(route('rekap-absensi.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access rekap absensi index page.
     */
    public function test_authenticated_user_can_access_rekap_absensi_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);

        $response = $this->actingAs($user)->get(route('rekap-absensi.index'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.rekapAbsensi');
        $response->assertSee('Silakan Pilih Kelas');
    }

    /**
     * Test authenticated user can access rekap absensi with a class.
     */
    public function test_authenticated_user_can_view_rekap_absensi_for_class(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $class = Classes::factory()->create(['name' => 'VII A']);

        $response = $this->actingAs($user)->get(route('rekap-absensi.index', ['class_id' => $class->id]));

        $response->assertStatus(200);
        $response->assertViewIs('auth.rekapAbsensi');
        $response->assertSee('Kelas');
        $response->assertSee('VII A');
    }
}
