<?php

namespace App\Http\Controllers;

use App\Models\AcademicDeliveryStructure;
use App\Models\Course;
use Illuminate\Http\Request;

class AcademicDeliveryStructureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $courses = Course::active()->orderBy('title')->get();
        $selectedCourseId = $request->filled('course_id') ? (int) $request->course_id : null;

        $query = AcademicDeliveryStructure::with('course')->orderBy('title');

        if ($selectedCourseId) {
            $query->where('course_id', $selectedCourseId);
        }

        $academicDeliveryStructures = $query->get();

        return view('admin.master-data.academic-delivery-structures.index', compact(
            'academicDeliveryStructures',
            'courses',
            'selectedCourseId'
        ));
    }

    public function ajax_add()
    {
        $courses = Course::where('is_active', 1)->get();
        return view('admin.master-data.academic-delivery-structures.add', compact('courses'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'course_id' => 'required',
        ]);

        $descriptions = array_values(array_filter((array) $request->input('descriptions', [])));

        AcademicDeliveryStructure::create([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'descriptions' => $descriptions,
            'status' => 1,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.academic-delivery-structures.index')->with('success', 'Academic Delivery Structure saved successfully.');
    }

    public function ajax_edit($id)
    {
        $academicDeliveryStructure = AcademicDeliveryStructure::find($id);
        $courses = Course::where('is_active', 1)->get();
        return view('admin.master-data.academic-delivery-structures.edit', compact('academicDeliveryStructure', 'courses'));
    }

    public function ajax_view($id)
    {
        $academicDeliveryStructure = AcademicDeliveryStructure::with('course')->find($id);
        return view('admin.master-data.academic-delivery-structures.view', compact('academicDeliveryStructure'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'course_id' => 'required',
        ]);

        $descriptions = array_values(array_filter((array) $request->input('descriptions', [])));

        $academicDeliveryStructure = AcademicDeliveryStructure::find($id);
        $academicDeliveryStructure->update([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'descriptions' => $descriptions,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.academic-delivery-structures.index')->with('success', 'Academic Delivery Structure updated successfully.');
    }

    public function delete($id)
    {
        AcademicDeliveryStructure::find($id)->delete();
        return redirect()->route('admin.academic-delivery-structures.index')->with('success', 'Academic Delivery Structure deleted successfully.');
    }
}
