<?php

namespace App\Http\Resources\Admin\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Convert the status to a badge representation.
     *
     * @param int $status
     * @return string
     */
    private function status($status)
    {
        if ($status == 1) {
            return '<span class="badge badge-soft-success">Active</span>';
        } elseif ($status == 2) {
            return '<span class="badge badge-soft-warning">Password Not Set</span>';
        } else {
            return '<span class="badge badge-soft-danger">Deactivated</span>';
        }
    }

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'sn' => ++$request->start,
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile'=>$this->mobile,
            'status' => $this->status($this->status),
        ];
    }
}
