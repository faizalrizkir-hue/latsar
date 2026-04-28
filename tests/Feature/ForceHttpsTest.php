<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ForceHttpsTest extends TestCase
{
    public function test_http_request_redirects_to_https_when_enabled(): void
    {
        Config::set('app.force_https', true);

        $response = $this->get('http://localhost/login');

        $response->assertStatus(308);
        $response->assertRedirect('https://localhost/login');
    }

    public function test_secure_request_is_not_redirected_when_enabled(): void
    {
        Config::set('app.force_https', true);

        $response = $this->get('https://localhost/login');

        $response->assertOk();
    }

    public function test_http_request_is_not_redirected_when_disabled(): void
    {
        Config::set('app.force_https', false);

        $response = $this->get('/login');

        $response->assertOk();
    }
}
