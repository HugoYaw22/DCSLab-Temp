<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvestorResource extends JsonResource
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
            'contact' => $this->contact,
            'address' => $this->address,
            'city' => $this->city,
            'tax_number' => $this->tax_number,
            'remarks' => $this->remarks,
            'status' => $this->status
        ];
    }
}
