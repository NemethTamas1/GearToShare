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

    protected function putAsFrontend(string $uri, array $data = [])
    {
        return $this->withHeaders([
            'Origin' => 'http://localhost',
            'Accept' => 'application/json',
        ])->put($uri, $data);
    }

    protected function deleteAsFrontend(string $uri)
    {
        return $this->withHeaders([
            'Origin' => 'http://localhost',
            'Accept' => 'application/json',
        ])->delete($uri);
    }
}
