<?php

namespace App\Modules\Student\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Academic\Resources\AcademicClassResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->resource)) {
            return [];
        }

        return [
            'id' => $this->id,
            'nisn' => $this->nisn,
            'name' => $this->name,
            'gender' => $this->gender,
            'birth_place' => $this->birth_place,
            'birth_date' => $this->birth_date ? $this->birth_date->format('Y-m-d') : null,
            'status' => $this->status,
            'guardian_id' => $this->guardian_id,
            'guardian' => new GuardianResource($this->whenLoaded('guardian')),
            'class_id' => $this->class_id,
            'class' => new AcademicClassResource($this->whenLoaded('class')),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
