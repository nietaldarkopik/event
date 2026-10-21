<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackendFlowsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag);
    }

    public function test_invitation_template_crud_flow_uses_event_categories()
    {
        $this->withoutMiddleware();

        \App\Models\EventCategory::create([
            'name' => 'Seminar',
            'slug' => 'seminar',
            'is_active' => true,
        ]);

        $response = $this->post('/admin/invitation-templates', [
            'name' => 'Template Seminar',
            'category' => 'seminar',
            'description' => 'Deskripsi',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('invitation-templates.index'));

        $this->assertDatabaseHas('invitation_templates', [
            'name' => 'Template Seminar',
            'category' => 'seminar',
            'is_active' => true,
        ]);

        $template = \App\Models\InvitationTemplate::first();

        $response = $this->get(route('invitation-templates.edit', $template->id));
        $response->assertStatus(200);
    }

    public function test_api_invitation_templates_by_category_returns_active_templates()
    {
        $categorySlug = 'seminar_api';

        \App\Models\InvitationTemplate::create([
            'name' => 'API Template',
            'category' => $categorySlug,
            'is_active' => true,
            'config' => \App\Models\InvitationTemplate::getDefaultConfig(),
        ]);

        \App\Models\InvitationTemplate::create([
            'name' => 'Inactive Template',
            'category' => $categorySlug,
            'is_active' => false,
            'config' => \App\Models\InvitationTemplate::getDefaultConfig(),
        ]);

        $response = $this->get('/api/invitation-templates/by-category/' . $categorySlug);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'API Template'])
            ->assertJsonMissing(['name' => 'Inactive Template']);
    }

    public function test_event_create_uses_event_category_and_slug_consistency()
    {
        $this->withoutMiddleware();

        $category = \App\Models\EventCategory::create([
            'name' => 'Wisuda',
            'slug' => 'wisuda',
            'is_active' => true,
        ]);

        $response = $this->post('/admin/events', [
            'title' => 'Event Wisuda',
            'category_id' => $category->id,
            'visibility' => 'PUBLIC',
            'status' => 'DRAFT',
        ]);

        $response->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseHas('events', [
            'title' => 'Event Wisuda',
            'category_id' => $category->id,
        ]);
    }
}
