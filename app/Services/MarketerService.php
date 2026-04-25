<?php

namespace App\Services;

use App\DTOs\CreateMarketerDTO;
use App\DTOs\UpdateMarketerDTO;
use App\Models\Marketer;
use App\Models\MarketerTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MarketerService
{
    public function __construct(private UploadService $uploadService) {}

    public function list(): LengthAwarePaginator
    {
        return QueryBuilder::for(Marketer::class)
            ->allowedFilters(
                AllowedFilter::partial('first_name'),
                AllowedFilter::partial('last_name'),
                AllowedFilter::partial('phone'),
                AllowedFilter::partial('email'),
                AllowedFilter::exact('is_active'),
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
            )
            ->allowedSorts('first_name', 'last_name', 'created_at', 'is_active')
            ->defaultSort('-created_at')
            ->paginate(20)
            ->withQueryString();
    }

    public function create(CreateMarketerDTO $dto): Marketer
    {
        $passportPath = $dto->passport
            ? $this->uploadService->store($dto->passport, 'passports')
            : null;

        return Marketer::create([
            'first_name'   => $dto->firstName,
            'last_name'    => $dto->lastName,
            'phone'        => $dto->phone,
            'backup_phone' => $dto->backupPhone,
            'email'        => $dto->email,
            'password'     => $dto->password,
            'passport'     => $passportPath,
            'is_active'    => true,
            'status'       => $dto->status,
        ]);
    }

    public function update(Marketer $marketer, UpdateMarketerDTO $dto): void
    {
        $data = [
            'first_name'   => $dto->firstName,
            'last_name'    => $dto->lastName,
            'phone'        => $dto->phone,
            'backup_phone' => $dto->backupPhone,
            'email'        => $dto->email,
        ];

        if ($dto->passport) {
            $data['passport'] = $this->uploadService->store($dto->passport, 'passports');
        }

        if ($dto->password != null) {
            $data['password'] = $dto->password;
        }

        $marketer->update($data);
    }

    public function approve(Marketer $marketer): void
    {
        $marketer->update(['status' => 'approved']);
    }

    public function reject(Marketer $marketer): void
    {
        $marketer->update(['status' => 'rejected']);
    }

    public function toggle(Marketer $marketer): void
    {
        $marketer->update(['is_active' => !$marketer->is_active]);
    }

    public function delete(Marketer $marketer): void
    {
        $marketer->delete();
    }

    public function addTransaction(Marketer $marketer, array $data): MarketerTransaction
    {
        return DB::transaction(function () use ($marketer, $data) {
            $amount  = (float) $data['amount'];
            $balance = (float) $marketer->balance;

            $balanceAfter = $data['type'] === 'deposit'
                ? $balance + $amount
                : $balance - $amount;

            $marketer->update(['balance' => $balanceAfter]);

            return $marketer->transactions()->create([
                'user_id'        => Auth::guard('web')->id(),
                'type'           => $data['type'],
                'recipient_name' => $data['recipient_name'],
                'description'    => $data['description'],
                'amount'         => $amount,
                'date'           => $data['date'],
                'balance_after'  => $balanceAfter,
            ]);
        });
    }

    public function statement(Marketer $marketer): LengthAwarePaginator
    {
        return $marketer->transactions()->latest('date')->latest('id')->paginate(30);
    }
}
