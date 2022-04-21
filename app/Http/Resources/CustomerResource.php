<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'is_member' => $this->is_member,
            'customer_group' => new CustomerGroupResource($this->customer_group),
            'zone' => $this->zone,
            'max_open_invoice' => $this->max_open_invoice,
            'max_outstanding_invoice' => $this->max_outstanding_invoice,
            'max_invoice_age' => $this->max_invoice_age,
            'payment_term' => $this->payment_term,
            'tax_id' => $this->tax_id,
            'remarks' => $this->remarks,
            'status' => $this->status
        ];
    }
}
