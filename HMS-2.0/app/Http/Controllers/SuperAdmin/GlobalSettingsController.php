<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GlobalSettingsController extends Controller
{
    /**
     * Display a listing of global settings.
     */
    public function index()
    {
        $settings = Setting::all();
        return view('super-admin.settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new setting.
     */
    public function create()
    {
        return view('super-admin.settings.create');
    }

    /**
     * Store a newly created setting in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|unique:settings,key',
            'value' => 'nullable|string',
            'type' => 'required|in:string,integer,decimal,boolean,json',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Setting::create($request->all());

        return redirect()->route('super-admin.settings.index')
            ->with('success', 'Setting created successfully.');
    }

    /**
     * Show the form for editing the specified setting.
     */
    public function edit(Setting $setting)
    {
        return view('super-admin.settings.edit', compact('setting'));
    }

    /**
     * Update the specified setting in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|unique:settings,key,' . $setting->id,
            'value' => 'nullable|string',
            'type' => 'required|in:string,integer,decimal,boolean,json',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $setting->update($request->all());

        return redirect()->route('super-admin.settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    /**
     * Remove the specified setting from storage.
     */
    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()->route('super-admin.settings.index')
            ->with('success', 'Setting deleted successfully.');
    }

    /**
     * Toggle setting status (enable/disable).
     */
    public function toggle(Setting $setting)
    {
        // Assuming we add an 'enabled' field, but for now, maybe just update value for boolean types
        if ($setting->type === 'boolean') {
            $newValue = $setting->value == '1' ? '0' : '1';
            $setting->update(['value' => $newValue]);
        }

        return redirect()->back()
            ->with('success', 'Setting updated successfully.');
    }
}
