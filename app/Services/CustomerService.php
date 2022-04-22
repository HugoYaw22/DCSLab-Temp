<?php

namespace App\Services;

use App\Models\Customer;

interface CustomerService
{
    public function create(
        int $company_id,
        int $customer_group_id,
        string $code,
        int $is_member,
        string $name,
        ?string $zone = null,
        int $max_open_invoice,
        int $max_outstanding_invoice,
        int $max_invoice_age,
        int $payment_term,
        int $tax_id,
        ?string $remarks = null,
        int $status,
    ): ?Customer;

    public function read(
        int $companyId,
        string $search = '',
        bool $paginate = true,
        int $perPage = 10
    );

    public function update(
        int $id,
        int $company_id,
        int $customer_group_id,
        string $code,
        int $is_member,
        string $name,
        ?string $zone = null,
        int $max_open_invoice,
        int $max_outstanding_invoice,
        int $max_invoice_age,
        int $payment_term,
        int $tax_id,
        ?string $remarks = null,
        int $status,
    ): ?Customer;

    public function delete(int $id): bool;

    public function generateUniqueCode(int $companyId): string;

    public function isUniqueCode(string $code, int $companyId, ?int $exceptId = null): bool;
}