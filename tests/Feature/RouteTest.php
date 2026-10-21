<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Share the errors variable to prevent ViewExceptions when testing views with withoutMiddleware()
        View::share('errors', new ViewErrorBag);

        // Seed some basic data so that route-model binding and pages have data to display
        $this->seedBasicData();
    }

    /**
     * Test all GET routes in the application to ensure no 500 Internal Server Errors occur.
     */
    public function test_all_get_routes_do_not_return_500()
    {
        $this->withoutMiddleware([
            \App\Http\Middleware\Authenticate::class,
            \App\Http\Middleware\EnsureRoutePermission::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);

        $routes = Route::getRoutes()->get('GET');
        $skippedPatterns = [
            'telescope',
            'sanctum',
            '_debugbar',
            'api/user', // sanctum default api route
        ];

        foreach ($routes as $route) {
            $uri = $route->uri();

            // Skip third-party/system routes
            foreach ($skippedPatterns as $pattern) {
                if (str_contains($uri, $pattern)) {
                    continue 2;
                }
            }

            // Replace route parameters with dummy/real IDs
            $testUrl = $this->resolveRouteParameters($uri);

            // Execute the GET request
            $response = $this->get($testUrl);

            // Assert that the status code is not 500 (we allow 200, 302, 303, 404, etc.)
            $status = $response->getStatusCode();
            $this->assertNotEquals(
                500,
                $status,
                "Route GET /{$uri} failed with a 500 error. Test URL was: /{$testUrl}"
            );
        }
    }

    /**
     * Seeds basic model instances to prevent 404s and database lookup errors.
     */
    private function seedBasicData()
    {
        // 1. Event Category
        $category = \App\Models\EventCategory::forceCreate([
            'name' => 'Seminar',
            'slug' => 'seminar',
            'is_active' => true,
        ]);

        // 2. Event
        $event = \App\Models\Event::forceCreate([
            'title' => 'Test Event',
            'slug' => 'test-event',
            'category_id' => $category->id,
            'visibility' => 'PUBLIC',
            'status' => 'DRAFT',
            'start_date' => now(),
            'end_date' => now()->addHours(2),
        ]);

        // 3. User
        $user = \App\Models\User::forceCreate([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        // 4. Invitation Template
        $template = \App\Models\InvitationTemplate::forceCreate([
            'name' => 'Default Template',
            'category' => 'seminar',
            'is_active' => true,
            'config' => \App\Models\InvitationTemplate::getDefaultConfig(),
        ]);

        // 5. Unit
        $unit = \App\Models\Unit::forceCreate([
            'name' => 'Teknik Informatika',
            'code' => 'IF',
            'type' => 'prodi',
        ]);
    }

    /**
     * Dynamically replaces placeholders like {event} or {id} with actual data.
     */
    private function resolveRouteParameters(string $uri): string
    {
        $event = \App\Models\Event::first();
        $category = \App\Models\EventCategory::first();
        $user = \App\Models\User::first();
        $template = \App\Models\InvitationTemplate::first();
        $unit = \App\Models\Unit::first();

        $replacements = [
            '{event}' => $event ? $event->id : '1',
            '{event_category}' => $category ? $category->id : '1',
            '{speaker}' => '1',
            '{ticket_type}' => '1',
            '{invitation_template}' => $template ? $template->id : '1',
            '{article}' => '1',
            '{gallery}' => '1',
            '{registration}' => '1',
            '{session}' => '1',
            '{event_session}' => '1',
            '{sponsor}' => '1',
            '{certificate_template}' => '1',
            '{certificate}' => '1',
            '{event_committee}' => '1',
            '{qr_code}' => '1',
            '{attendance_log}' => '1',
            '{slug}' => $event ? $event->slug : 'test-event',
            '{category}' => $category ? $category->slug : 'seminar',
            '{unit}' => $unit ? $unit->id : '1',
            '{user}' => $user ? $user->id : '1',
            '{template}' => $template ? $template->id : '1',
            '{id}' => '1',
            '{provider}' => 'google',
            '{hash}' => 'dummyhash',
            '{unique_code}' => 'dummycode',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $uri);
    }
}
