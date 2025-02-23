<?php

namespace App\Http\Controllers;

use App\Models\ContactCustomFeildMaster;
use App\Models\CustomFieldMaster;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        if (request()->ajax()) {
            $custom_fields = ContactCustomFeildMaster::orderBy('id', 'desc');
            return datatables($custom_fields)
                ->editColumn('field_id', function ($row) {
                    return optional($row->field)->type;
                })
                ->editColumn('is_required', function($row){
                    return $row->is_required == '1' ? 'Yes' : 'No';
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-success btn-sm edit" data-url="' . route('custom-field.show', $row->id) . '">Edit</button>
                <button class="btn btn-danger btn-sm delete" data-url="' . route('custom-field.destroy', $row->id) . '">Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $fields = CustomFieldMaster::select('id', 'type')->get();
        return view('custom_fields.index', compact('fields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $validated = Validator::make($request->only('label', 'field_id', 'is_required'), [
                'label' => ['required', 'unique:contact_custom_feild_masters,label'],
                'field_id' => ['required'],
                'is_required' => ['required']
            ], [
                'label.required'=>'Field name is required.',
                'label.unique'=>'This field name has already been taken.',
                'field_id.required'=>'Please select field type.'
            ]);
            if ($validated->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validated->errors()
                ], 422);
            }
            $custom_fields = new ContactCustomFeildMaster;
            $custom_fields->label = $request->label;
            $custom_fields->is_required = $request->is_required == '1' ? true : false;
            $custom_fields->field_id = $request->field_id;
            $custom_fields->slug = Str::slug($request->label);
            $custom_fields->save();
            return response()->json([
                'success' => true,
                'message' => 'Custom field added successfully.'
            ]);
        } catch (Exception $ex) {
            Log::error($ex);
            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try{
            $custom_field = ContactCustomFeildMaster::find($id);
            if(!$custom_field){
                return response()->json([
                    'success' => false,
                    'message' => 'Custom field is not found.'
                ], 422);
            }
            $custom_field->is_required = $custom_field->is_required ? '1' : '0';
            return response()->json([
                'success'=>true,
                'message'=>'Custom field is found.',
                'data'=> $custom_field,
                'url' => route('custom-field.update', $id)
            ]);
        }catch(Exception $ex){
            Log::error($ex);
            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {
            $validated = Validator::make($request->only('label', 'field_id', 'is_required'), [
                'label' => ['required', 'unique:contact_custom_feild_masters,id,' . $id],
                'field_id' => ['required',],
                'is_required' => ['required'],
            ], [
                'label.required' => 'Field name is required.',
                'label.unique' => 'This field name has already been taken.',
                'field_id.required' => 'Please select a field type.'
            ]);
            if ($validated->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validated->errors()
                ], 422);
            }
            $custom_fields = ContactCustomFeildMaster::find($id);
            if(!$custom_fields){
                return response()->json([
                    'success'=> false,
                    'message'=> 'Custom field is not found.'
                ]);
            }
            $custom_fields->label = $request->label;
            $custom_fields->is_required = $request->is_required == '1' ? true : false;
            $custom_fields->field_id = $request->field_id;
            $custom_fields->slug = Str::slug($request->label);
            $custom_fields->save();
            return response()->json([
                'success' => true,
                'message' => 'Custom field updated successfully.'
            ]);
        } catch (Exception $ex) {
            Log::error($ex);
            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try{
            $custom_field = ContactCustomFeildMaster::find($id);
            if(!$custom_field){
                return response()->json([
                    'success' => false,
                    'message' => 'Custom field is not found.'
                ], 422);
            }
            $custom_field->delete();
            return response()->json([
                'success'=>true,
                'message'=>'Custom field deleted successfully.',
                'data'=> $custom_field
            ]);
        }catch(Exception $ex){
            Log::error($ex);
            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
}
