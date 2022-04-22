<?php

namespace App\Services;

use App\Models\Income;

interface IncomeService
{
    public function create(
        int $company_id,
        int $branch_id,
        int $income_group_id,
        ?int $cash_id = null,
        string $code,
        ?string $date = null,
        string $payment_term_type,
        string $amount,
        string $amount_owed,
        ?string $remarks = null,
        int $posted,
    ): ?Income;

    public function read(
        int $companyId,
        string $search = '',
        bool $paginate = true,
        int $perPage = 10
    );

    public function update(
        int $id,
        int $company_id,
        int $branch_id,
        int $income_group_id,
        ?int $cash_id = null,
        string $code,
        ?string $date = null,
        string $payment_term_type,
        string $amount,
        string $amount_owed,
        ?string $remarks = null,
        int $posted,
    ): ?Income;

    public function delete(int $id): bool;

    public function generateUniqueCode(int $companyId): string;

    public function isUniqueCode(string $code, int $companyId, ?int $exceptId = null): bool;
}