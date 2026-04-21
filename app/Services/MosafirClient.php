<?php

namespace App\Services;

use App\Models\Setting;

class MosafirClient
{
    public function __construct(private MosafirService $mosafir) {}

    public function login(string $phone, string $password): ?array
    {
        if (! $phone || ! $password) {
            return null;
        }

        $response = $this->mosafir->post('login', [
            'email'    => $phone,
            'password' => $password,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json('data');

        if (empty($data['token'])) {
            return null;
        }

        return [
            'token'      => $data['token'],
            'owner_name' => $data['user']['owner_name'] ?? null,
            'name'       => $data['user']['name'] ?? null,
        ];
    }

    public function getPrices(): ?array
    {
        $token = Setting::get('musafir_token');

        if (! $token) {
            return null;
        }

        $response = $this->mosafir->get('prices', token: $token);

        if (! $response->successful()) {
            return null;
        }

        return $response->json('data');
    }

    public function showParcel(string|int $parcelId): ?array
    {
        $token = Setting::get('musafir_token');

        if (! $token) {
            return null;
        }

        $response = $this->mosafir->get("parcels/{$parcelId}", token: $token);

        if (! $response->successful()) {
            return null;
        }

        return $response->json('data');
    }

    public function createParcel(array $data): ?array
    {
        $token = Setting::get('musafir_token');

        if (! $token) {
            return null;
        }

        $response = $this->mosafir->post('parcels/store', [
            'desc'             => $data['desc'],
            'customer_name'    => $data['customer_name'],
            'qty'              => $data['qty'] ?? 1,
            'recipient_number' => $data['recipient_number'],
            'product_price'    => $data['product_price'],
            'address'          => $data['address'],
            'delivery_on'      => $data['delivery_on'] ?? 'customer',
            'to_city_id'       => $data['to_city_id'],
            'is_payment_down'  => $data['is_payment_down'] ?? true,
        ], token: $token);

        if (! $response->successful()) {
            return null;
        }

        return $response->json('data');
    }
}
