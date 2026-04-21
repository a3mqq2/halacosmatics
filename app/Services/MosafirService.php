<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class MosafirService
{
    private string $baseUrl = 'https://alms.ly/api/clientarea';

    public function get(string $endpoint, array $query = [], ?string $token = null): Response
    {
        $request = Http::timeout(15)->acceptJson();

        if ($token) {
            $request = $request->withToken($token);
        }

        return $request->get("{$this->baseUrl}/{$endpoint}", $query);
    }

    public function post(string $endpoint, array $data = [], ?string $token = null): Response
    {
        $request = Http::timeout(15)->acceptJson();

        if ($token) {
            $request = $request->withToken($token);
        }

        return $request->post("{$this->baseUrl}/{$endpoint}", $data);
    }
}
