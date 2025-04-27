<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    // عرض قائمة الفروع
    public function index()
    {
        $branches = Branch::all();
        return view('branches.index', compact('branches'));
    }

    // عرض نموذج إنشاء فرع جديد
    public function create()
    {
        return view('branches.create');
    }

    // حفظ الفرع الجديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'attendance_radius' => 'required|numeric|min:50', // نصف قطر لا يقل عن 50 متر
        ]);

        Branch::create($validated);

        return redirect()->route('branches.index')
            ->with('success', 'تم إنشاء الفرع بنجاح');
    }

    // عرض نموذج تعديل الفرع
    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    // تحديث بيانات الفرع
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'attendance_radius' => 'required|numeric|min:50',
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')
            ->with('success', 'تم تحديث بيانات الفرع بنجاح');
    }

    // حذف الفرع
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')
            ->with('success', 'تم حذف الفرع بنجاح');
    }

    // واجهة تحديد الموقع على الخريطة
    public function showMap()
    {
        $branches = Branch::all();
        return view('branches.map', compact('branches'));
    }
    
    public function saveLocation(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);
    
        $branch = Branch::find($request->branch_id);
        $branch->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);
    
        return redirect()->back()->with('success', ' data updated successfully');
    }
}