<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CapitalResource extends JsonResource
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
            'ref_number' => $this->ref_number,
            'investor' => new InvestorResource($this->investor),
            'group' => new CustomerGroupResource($this->group),
            'cash' => new CashResource($this->cash),
            'date' => $this->date,
            'capital_status' => $this->capital_status,
            'amount' => $this->amount,
            'remarks' => $this->remarks,
        ];
    }
}
