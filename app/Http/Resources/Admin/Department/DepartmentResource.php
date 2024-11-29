<?php

namespace App\Http\Resources\Admin\Department;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
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
    public function toArray(Request $request): array
    {
        return [
            'sn' => ++$request->start,
            'id' => $this->id,
            'name' => $this->name,
            'company_name' => $this->company->name,
            
          
            
        ];
    }
}
