<?php

namespace App\Http\Resources\Admin\AdvancePayment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdvancePaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($request);
        return [
            'sn' => ++$request->start,
            'id' => $this->advance_payment_id,
            'name' => $this->employee_name,
            'amount' => $this->total_amount,
            'interest' => $this->total_interest,
            'total_payable_amount' => $this->total_payable_amount,
            'emi_amount'=>$this->total_emi_amount,
            'total_emi_count'=>$this->total_emi_count,
            'pending_emi_count' => $this->pending_emi_count,
            'payment_type'=>$this->payment_type,
            'date_taken'=>$this->latest_date_taken,
            'code'=>$this->employee_code,
        ];
    }
}
