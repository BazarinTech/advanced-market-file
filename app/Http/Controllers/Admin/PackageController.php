<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Support\Media;
use Illuminate\Http\Request;
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
            'amount'        => 'required|numeric|min:0',
            'daily'         => 'required|numeric|min:0',
            'days'          => 'required|integer|min:1',
            'image'         => 'nullable|image|max:2048',
            'active'        => 'nullable|boolean',
            'one_time_only' => 'nullable|boolean',
            'tasks_per_day' => 'required|integer|min:1|max:20',
            'task_category' => 'nullable|string|max:100',
        ]);

        $data['active']        = $request->has('active') ? 1 : 0;
        $data['one_time_only'] = $request->has('one_time_only') ? 1 : 0;

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image'] = Media::put($file, 'packages', $filename);
        }

        Package::create($data);

        return back()->with('success', 'Package created successfully.');
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'amount'        => 'required|numeric|min:0',
            'daily'         => 'required|numeric|min:0',
            'days'          => 'required|integer|min:1',
            'image'         => 'nullable|image|max:2048',
            'active'        => 'nullable|boolean',
            'one_time_only' => 'nullable|boolean',
            'tasks_per_day' => 'required|integer|min:1|max:20',
            'task_category' => 'nullable|string|max:100',
        ]);

        $data['active']        = $request->has('active') ? 1 : 0;
        $data['one_time_only'] = $request->has('one_time_only') ? 1 : 0;

        if ($request->hasFile('image')) {
            Media::delete('packages', $package->image);
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['image'] = Media::put($file, 'packages', $filename);
        } else {
            unset($data['image']);
        }

        $package->update($data);

        return back()->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        Media::delete('packages', $package->image);
        $package->delete();

        return back()->with('success', 'Package deleted.');
    }
}
