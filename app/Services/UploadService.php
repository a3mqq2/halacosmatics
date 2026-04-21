<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class UploadService
{
    public function store(UploadedFile $file, string $folder, string $disk = 'public'): string
    {
        return $file->store($folder, $disk);
    }
}
