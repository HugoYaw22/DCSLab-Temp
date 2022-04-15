<?php

namespace App\Services;

use App\Models\Cash;

interface CashService
{
    public function create(
        int $company_id,
        string $code,
        string $name,
        ?int $is_bank = null,
        int $status,
    ): ?Cash;

    public function read(
        int $companyId,
        string $search = '',
        bool $paginate = true,
        int $perPage = 10
    );

    public function update(
        int $id,
        int $company_id,
        string $code,
        string $name,
        ?int $is_bank = null,
        int $status,
    ): ?Cash;

    public function delete(int $id): bool;

    public function generateUniqueCode(int $companyId): string;

    public function isUniqueCode(string $code, int $companyId, ?int $exceptId = null): bool;
}