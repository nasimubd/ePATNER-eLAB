<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportTemplate;
use App\Models\TemplateSection;
use App\Models\TemplateField;
use App\Models\LabTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = ReportTemplate::with(['labTest', 'creator'])
            ->forBusiness(Auth::user()->business_id);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('template_name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('labTest', function ($testQuery) use ($request) {
                        $testQuery->where('test_name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->filled('test_id')) {
            $query->where('lab_test_id', $request->test_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $templates = $query->latest()->paginate(10)->withQueryString();

        $labTests = LabTest::forBusiness(Auth::user()->business_id)
            ->active()
            ->orderBy('test_name')
            ->get();

        return view('admin.lab-reports.templates.index', compact('templates', 'labTests'));
    }

    public function create()
    {
        $labTests = LabTest::forBusiness(Auth::user()->business_id)
            ->active()
            ->orderBy('test_name')
            ->get();

        return view('admin.lab-reports.templates.create', compact('labTests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lab_test_id' => 'required|exists:lab_tests,id',
            'template_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'sections' => 'required|array|min:1',
            'sections.*.section_name' => 'required|string|max:255',
            'sections.*.fields' => 'required|array|min:1',
            'sections.*.fields.*.field_name' => 'required|string|max:255',
            'sections.*.fields.*.field_label' => 'required|string|max:255',
            'sections.*.fields.*.field_type' => 'required|in:text,number,select,textarea,date,time',
        ]);

        DB::transaction(function () use ($request) {
            // If this is set as default, unset other defaults for this test
            if ($request->is_default) {
                ReportTemplate::forBusiness(Auth::user()->business_id)
                    ->where('lab_test_id', $request->lab_test_id)
                    ->update(['is_default' => false]);
            }

            $template = ReportTemplate::create([
                'business_id' => Auth::user()->business_id,
                'lab_test_id' => $request->lab_test_id,
                'template_name' => $request->template_name,
                'description' => $request->description,
                'is_default' => $request->boolean('is_default'),
                'created_by' => Auth::id(),
            ]);

            foreach ($request->sections as $sectionIndex => $sectionData) {
                $section = TemplateSection::create([
                    'template_id' => $template->id,
                    'section_name' => $sectionData['section_name'],
                    'section_description' => $sectionData['section_description'] ?? null,
                    'section_order' => $sectionIndex + 1,
                    'is_required' => $sectionData['is_required'] ?? true,
                ]);

                foreach ($sectionData['fields'] as $fieldIndex => $fieldData) {
                    TemplateField::create([
                        'section_id' => $section->id,
                        'field_name' => $fieldData['field_name'],
                        'field_label' => $fieldData['field_label'],
                        'field_type' => $fieldData['field_type'],
                        'field_options' => $fieldData['field_options'] ?? null,
                        'default_value' => $fieldData['default_value'] ?? null,
                        'unit' => $fieldData['unit'] ?? null,
                        'normal_range' => $fieldData['normal_range'] ?? null,
                        'is_required' => $fieldData['is_required'] ?? false,
                        'field_order' => $fieldIndex + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.lab-reports.templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function show(ReportTemplate $template)
    {

        $template->load(['sections.fields', 'labTest']);

        return view('admin.lab-reports.templates.show', compact('template'));
    }

    public function edit(ReportTemplate $template)
    {

        $template->load(['sections.fields']);

        $labTests = LabTest::forBusiness(Auth::user()->business_id)
            ->active()
            ->orderBy('test_name')
            ->get();

        return view('admin.lab-reports.templates.edit', compact('template', 'labTests'));
    }

    public function update(Request $request, ReportTemplate $template)
    {

        $request->validate([
            'lab_test_id' => 'required|exists:lab_tests,id',
            'template_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'sections' => 'required|array|min:1',
            'sections.*.section_name' => 'required|string|max:255',
            'sections.*.fields' => 'required|array|min:1',
            'sections.*.fields.*.field_name' => 'required|string|max:255',
            'sections.*.fields.*.field_label' => 'required|string|max:255',
            'sections.*.fields.*.field_type' => 'required|in:text,number,select,textarea,date,time',
        ]);

        DB::transaction(function () use ($request, $template) {
            // If this is set as default, unset other defaults for this test
            if ($request->is_default) {
                ReportTemplate::forBusiness(Auth::user()->business_id)
                    ->where('lab_test_id', $request->lab_test_id)
                    ->where('id', '!=', $template->id)
                    ->update(['is_default' => false]);
            }

            $template->update([
                'lab_test_id' => $request->lab_test_id,
                'template_name' => $request->template_name,
                'description' => $request->description,
                'is_default' => $request->boolean('is_default'),
            ]);

            // Delete existing sections and fields
            $template->sections()->delete();

            // Recreate sections and fields
            foreach ($request->sections as $sectionIndex => $sectionData) {
                $section = TemplateSection::create([
                    'template_id' => $template->id,
                    'section_name' => $sectionData['section_name'],
                    'section_description' => $sectionData['section_description'] ?? null,
                    'section_order' => $sectionIndex + 1,
                    'is_required' => $sectionData['is_required'] ?? true,
                ]);

                foreach ($sectionData['fields'] as $fieldIndex => $fieldData) {
                    TemplateField::create([
                        'section_id' => $section->id,
                        'field_name' => $fieldData['field_name'],
                        'field_label' => $fieldData['field_label'],
                        'field_type' => $fieldData['field_type'],
                        'field_options' => $fieldData['field_options'] ?? null,
                        'default_value' => $fieldData['default_value'] ?? null,
                        'unit' => $fieldData['unit'] ?? null,
                        'normal_range' => $fieldData['normal_range'] ?? null,
                        'is_required' => $fieldData['is_required'] ?? false,
                        'field_order' => $fieldIndex + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.lab-reports.templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(ReportTemplate $template)
    {

        if ($template->reports()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete template that has associated reports.');
        }

        $template->delete();

        return redirect()->route('admin.lab-reports.templates.index')
            ->with('success', 'Template deleted successfully.');
    }

    public function toggleStatus(ReportTemplate $template)
    {
        $template->update(['is_active' => !$template->is_active]);

        $status = $template->is_active ? 'activated' : 'deactivated';

        // Return JSON response for AJAX requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Template {$status} successfully.",
                'is_active' => $template->is_active
            ]);
        }

        // Return redirect for regular requests
        return redirect()->back()->with('success', "Template {$status} successfully.");
    }


    public function getTemplatesForTest(Request $request)
    {
        $testId = $request->get('test_id');

        $templates = ReportTemplate::forBusiness(Auth::user()->business_id)
            ->where('lab_test_id', $testId)
            ->active()
            ->orderBy('is_default', 'desc')
            ->orderBy('template_name')
            ->get(['id', 'template_name', 'is_default']);

        return response()->json($templates);
    }

    public function getTemplateStructure(ReportTemplate $template)
    {

        $template->load(['sections.fields']);

        return response()->json($template);
    }

    public function duplicate(ReportTemplate $template)
    {
        // Load the template with its sections and fields
        $template->load(['sections.fields', 'labTest']);

        $labTests = LabTest::forBusiness(Auth::user()->business_id)
            ->active()
            ->orderBy('test_name')
            ->get();

        return view('admin.lab-reports.templates.duplicate', compact('template', 'labTests'));
    }

    public function storeDuplicate(Request $request, ReportTemplate $template)
    {
        $request->validate([
            'lab_test_id' => 'nullable|exists:lab_tests,id',
            'template_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'sections' => 'required|array|min:1',
            'sections.*.section_name' => 'required|string|max:255',
            'sections.*.section_description' => 'nullable|string',
            'sections.*.is_required' => 'boolean',
            'sections.*.fields' => 'required|array|min:1',
            'sections.*.fields.*.field_name' => 'required|string|max:255',
            'sections.*.fields.*.field_label' => 'required|string|max:255',
            'sections.*.fields.*.field_type' => 'required|in:text,number,select,textarea,date,time',
            'sections.*.fields.*.field_options' => 'nullable|string',
            'sections.*.fields.*.default_value' => 'nullable|string',
            'sections.*.fields.*.unit' => 'nullable|string',
            'sections.*.fields.*.normal_range' => 'nullable|string',
            'sections.*.fields.*.is_required' => 'boolean',
        ]);

        DB::transaction(function () use ($request) {
            // If this is set as default and lab_test_id is provided, unset other defaults for this test
            if ($request->is_default && $request->lab_test_id) {
                ReportTemplate::forBusiness(Auth::user()->business_id)
                    ->where('lab_test_id', $request->lab_test_id)
                    ->update(['is_default' => false]);
            }

            $newTemplate = ReportTemplate::create([
                'business_id' => Auth::user()->business_id,
                'lab_test_id' => $request->lab_test_id,
                'template_name' => $request->template_name,
                'description' => $request->description,
                'is_default' => $request->boolean('is_default'),
                'is_active' => $request->boolean('is_active', true),
                'created_by' => Auth::id(),
            ]);

            foreach ($request->sections as $sectionIndex => $sectionData) {
                $section = TemplateSection::create([
                    'template_id' => $newTemplate->id,
                    'section_name' => $sectionData['section_name'],
                    'section_description' => $sectionData['section_description'] ?? null,
                    'section_order' => $sectionIndex + 1,
                    'is_required' => $sectionData['is_required'] ?? true,
                ]);

                foreach ($sectionData['fields'] as $fieldIndex => $fieldData) {
                    // Process field_options for select fields
                    $fieldOptions = null;
                    if ($fieldData['field_type'] === 'select' && !empty($fieldData['field_options'])) {
                        $fieldOptions = array_map('trim', explode(',', $fieldData['field_options']));
                    }

                    TemplateField::create([
                        'section_id' => $section->id,
                        'field_name' => $fieldData['field_name'],
                        'field_label' => $fieldData['field_label'],
                        'field_type' => $fieldData['field_type'],
                        'field_options' => $fieldOptions,
                        'default_value' => $fieldData['default_value'] ?? null,
                        'unit' => $fieldData['unit'] ?? null,
                        'normal_range' => $fieldData['normal_range'] ?? null,
                        'is_required' => isset($fieldData['is_required']) ? true : false,
                        'field_order' => $fieldIndex + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.lab-reports.templates.index')
            ->with('success', 'Template duplicated successfully.');
    }
}
