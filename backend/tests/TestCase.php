<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function postAsFrontend(string $uri, array $data = [])
    {
        return $this->withHeaders([
            'Origin' => 'http://localhost',
            'Accept' => 'application/json'
        ])->post($uri, $data);
    }
}
