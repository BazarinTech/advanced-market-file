<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::orderBy('amount')->get();
        return Inertia::render('Admin/Packages/Index', compact('packages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'amount'        => 'required|numeric|min:1',
            'daily'         => 'required|numeric|min:1',
            'days'          => 'required|integer|min:1',
            'image'         => 'nullable|image|max:2048',
            'active'        => 'nullable|boolean',
            'tasks_per_day' => 'required|integer|min:1|max:20',
            'task_category' => 'nullable|string|max:100',
        ]);

        $data['active'] = $request->has('active') ? 1 : 0;

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(base_path('images/packages'), $filename);
            $data['image'] = $filename;
        }

        Package::create($data);

        return back()->with('success', 'Package created successfully.');
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'amount'        => 'required|numeric|min:1',
            'daily'         => 'required|numeric|min:1',
            'days'          => 'required|integer|min:1',
            'image'         => 'nullable|image|max:2048',
            'active'        => 'nullable|boolean',
            'tasks_per_day' => 'required|integer|min:1|max:20',
            'task_category' => 'nullable|string|max:100',
        ]);

        $data['active'] = $request->has('active') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($package->image) {
                @unlink(base_path('images/packages/' . $package->image));
            }
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(base_path('images/packages'), $filename);
            $data['image'] = $filename;
        } else {
            unset($data['image']);
        }

        $package->update($data);

        return back()->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        if ($package->image) {
            @unlink(base_path('images/packages/' . $package->image));
        }
        $package->delete();

        return back()->with('success', 'Package deleted.');
    }
}
