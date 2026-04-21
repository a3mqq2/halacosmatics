<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function getAll(): array
    {
        return [
            'about'           => Setting::get('about'),
            'policy'          => Setting::get('policy'),
            'musafir_token'      => Setting::get('musafir_token'),
            'musafir_owner_name' => Setting::get('musafir_owner_name'),
        ];
    }

    public function save(array $data): void
    {
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
