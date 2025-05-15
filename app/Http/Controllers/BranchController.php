<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount('users')->get();
        return view('branches.index', compact('branches'));
    }
    
    public function create()
    {
        return view('branches.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);
        
        Branch::create($request->all());
        
        return redirect()->route('branches.index')
            ->with('success', 'Branch created successfully');
    }
    
    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }
    
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);
        
        $branch->update($request->all());
        
        return redirect()->route('branches.index')
            ->with('success', 'Branch updated successfully');
    }
    
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')
            ->with('success', 'Branch deleted successfully');
    }
}