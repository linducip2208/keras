<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Aplikasi tanpa license pairing harus mengarahkan ke wizard pairing.
     * (RequirePair middleware — by design, bukan error.)
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/__pair');
    }
}
