<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

readonly class UpdateMarketerDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phone,
        public ?string $backupPhone,
        public ?string $email,
        public string $username,
        public ?string $password,
        public ?UploadedFile $passport,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            firstName:   $request->first_name,
            lastName:    $request->last_name,
            phone:       $request->phone,
            backupPhone: $request->backup_phone,
            email:       $request->email,
            username:    $request->username,
            password:    $request->filled('password') ? $request->password : null,
            passport:    $request->file('passport'),
        );
    }
}
