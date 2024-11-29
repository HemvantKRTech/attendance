<?php

namespace App\Http\Resources\Admin\Attendance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray($request)
    {
        // dd($request);
        return [
            'sn' => ++$request->start,
            'id' => $this->id,
            'name' => $this->employee_name,
            'employeId' => $this->employee_id,
            'hours'=>$this->hours,
            'overtime'=>$this->overtime,
            'status' => $this->status,
            'remarks'=>$this->remarks,
            'attendance_date'=>$this->attendance_date,
            'code'=>$this->code,
        ];
    }
}
