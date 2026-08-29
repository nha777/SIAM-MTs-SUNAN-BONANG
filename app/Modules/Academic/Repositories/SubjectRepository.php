<?php

namespace App\Modules\Academic\Repositories;

use App\Modules\Academic\Models\Subject;

class SubjectRepository implements SubjectRepositoryInterface
{
    public function all($search = null, $type = null, $paginate = 15)
    {
        $query = Subject::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        return $query->orderBy('name')->paginate($paginate);
    }

    public function find($id)
    {
        return Subject::findOrFail($id);
    }

    public function create(array $data)
    {
        return Subject::create($data);
    }

    public function update($id, array $data)
    {
        $subject = Subject::findOrFail($id);
        $subject->update($data);
        return $subject;
    }

    public function delete($id)
    {
        $subject = Subject::findOrFail($id);
        return $subject->delete();
    }
}
