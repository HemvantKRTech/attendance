<?php

namespace App\Http\Resources\Admin\Wages;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function status($status)
    {
        if ($status == 1) {
            $status = '<span class="">Active</span>';
        } elseif ($status == 2) {
            $status = '<span class="">Password Not Set</span>';
        } else{
            $status = '<span class="">Deactivated</span>';
        }
        return $status;
    }
    public function toArray($request)
    {
        return [
            'sn' => ++$request->start,
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'status' => $this->status($this->is_active),
        ];
    }
}
