<?php

namespace App\Modules\Academic\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Models\AcademicYear;
use App\Modules\Academic\Requests\StoreAcademicYearRequest;
use App\Modules\Academic\Requests\UpdateAcademicYearRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $years = AcademicYear::orderBy('start_year', 'desc')->get();

        if ($request->wantsJson()) {
            return response()->json($years);
        }

        return view('academic::years.index', ['years' => $years]);
    }

    public function create(): View
    {
        return view('academic::years.create');
    }

    public function store(StoreAcademicYearRequest $request)
    {
        $data = $request->validated();

        if (isset($data['name']) && preg_match('/^(\d{4})\/(\d{4})$/', $data['name'], $m)) {
            $data['start_year'] = (int)$m[1];
            $data['end_year'] = (int)$m[2];
        }

        $year = AcademicYear::create($data);

        if ($request->wantsJson()) {
            return response()->json($year, 201);
        }

        return redirect()->route('academic-years.index')->with('success', 'Academic year created.');
    }

    public function show(AcademicYear $academic_year, Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json($academic_year);
        }

        return view('academic::years.show', ['year' => $academic_year]);
    }

    public function edit(AcademicYear $academic_year): View
    {
        return view('academic::years.edit', ['year' => $academic_year]);
    }

    public function update(UpdateAcademicYearRequest $request, AcademicYear $academic_year)
    {
        $data = $request->validated();
        if (isset($data['name']) && preg_match('/^(\d{4})\/(\d{4})$/', $data['name'], $m)) {
            $data['start_year'] = (int)$m[1];
            $data['end_year'] = (int)$m[2];
        }

        $academic_year->update($data);

        if ($request->wantsJson()) {
            return response()->json($academic_year);
        }

        return redirect()->route('academic-years.index')->with('success', 'Academic year updated.');
    }

    public function destroy(AcademicYear $academic_year)
    {
        $academic_year->delete();

        return redirect()->route('academic-years.index')->with('success', 'Academic year deleted.');
    }

    public function restore($id)
    {
        $year = AcademicYear::withTrashed()->findOrFail($id);
        $year->restore();

        return redirect()->route('academic-years.index')->with('success', 'Academic year restored.');
    }

    public function activate($id)
    {
        AcademicYear::query()->update(['is_active' => false]);
        $year = AcademicYear::findOrFail($id);
        $year->is_active = true;
        $year->save();

        return redirect()->route('academic-years.index')->with('success', 'Academic year activated.');
    }
}
