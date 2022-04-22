<?php

namespace App\Services;

use App\Models\Capital;

interface CapitalService
{
    public function create(
        int $company_id,
        int $investor_id,
        int $group_id,
        int $cash_id,
        ?int $ref_number = null,
        ?string $date = null,
        int $capial_status,
        int $amount,
        ?string $remarks = null,
    ): ?Capital;

    public function read(
        int $companyId,
        string $search = '',
        bool $paginate = true,
        int $perPage = 10
    );

    public function update(
        int $id,
        int $company_id,
        int $investor_id,
        int $group_id,
        int $cash_id,
        ?int $ref_number = null,
        ?string $date = null,
        int $capial_status,
        int $amount,
        ?string $remarks = null,
    ): ?Capital;

    public function delete(int $id): bool;

    public function generateUniqueCode(int $companyId): string;

    public function isUniqueCode(string $code, int $companyId, ?int $exceptId = null): bool;
}