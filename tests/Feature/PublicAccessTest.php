<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The landing page is the product's front door. A guest must land on it
     * and stay there — never be bounced to /login for simply visiting.
     */
    public function test_the_root_url_renders_for_a_guest_without_redirecting(): void
    {
        $response = $this->get('/');

        // assertOk is the real assertion here: a redirect to /login would be
        // a 302. An earlier version compared against a hardcoded login URL,
        // which passed vacuously as soon as APP_URL changed.
        $response->assertOk();
        $response->assertSee('Turn unstructured data into', false);
    }

    public function test_the_landing_page_lists_open_tasks_to_a_guest(): void
    {
        Task::factory()->create(['title_en' => 'Invoice batch 999 — 12 rows']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Invoice batch 999 — 12 rows', false)
            // Guests are asked to sign in only at the point of claiming.
            ->assertSee('Sign in to claim');
    }

    public function test_the_workspace_is_closed_to_guests(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/wallet')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');
    }

    public function test_switching_language_flips_direction_and_persists(): void
    {
        $this->get('/language/ar')->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee('lang="ar" dir="rtl"', false);

        $this->get('/language/en');

        $this->get('/')
            ->assertOk()
            ->assertSee('lang="en" dir="ltr"', false);
    }

    public function test_an_unsupported_locale_is_rejected(): void
    {
        $this->get('/language/de')->assertNotFound();
    }
}
