<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'hId' => $this->hId,
            'company' => new CompanyResource($this->company),
            'branch' => new BranchResource($this->branch),
            'code' => $this->code,
            'date' => $this->date,
            'payment_term_type' => $this->payment_term_type,
            'expense_group_id' => $this->expense_group_id,
            'cash_id' => $this->cash_id,
            'amount' => $this->amount,
            'amount_owed' => $this->amount_owed,
            'remarks' => $this->remarks,
            'posted' => $this->posted,
        ];
    }
}
