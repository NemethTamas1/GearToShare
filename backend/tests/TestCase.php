<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private function frontendHeaders(): array
    {
        return [
            'Origin' => 'http://localhost',
            'Accept' => 'application/json'
        ];
    }

    protected function getAsFrontend(string $uri)
    {
        return $this->withHeaders($this->frontendHeaders())->get($uri);
    }

    protected function postAsFrontend(string $uri, array $data = [])
    {
        return $this->withHeaders($this->frontendHeaders())->post($uri, $data);
    }

    protected function putAsFrontend(string $uri, array $data = [])
    {
        return $this->withHeaders($this->frontendHeaders())->put($uri, $data);
    }

    protected function patchAsFrontend(string $uri, array $data = [])
    {
        return $this->withHeaders($this->frontendHeaders())->patch($uri, $data);
    }

    protected function deleteAsFrontend(string $uri)
    {
        return $this->withHeaders($this->frontendHeaders())->delete($uri);
    }
}
