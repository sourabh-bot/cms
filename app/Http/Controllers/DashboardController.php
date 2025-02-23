<?php

namespace App\Http\Controllers;

use App\Models\ContactAddtionInformaton;
use App\Models\ContactCustomFeildMaster;
use App\Models\ContactMaster;
use App\Models\GenderMaster;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    private $dirName = 'uploads';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        if (request()->ajax()) {
            $contacts = ContactMaster::all();
            return datatables($contacts)
                ->editColumn('gender_id', function($row){
                    return $row->gender->name;
                })
                ->editColumn('profile_image', function ($row) {
                    $url = Storage::url($row->profile_image);
                    return '<img src="' . $url . '" title="' . $row->name . '" height="50" width="50"/>';
                })
                ->addColumn('status', function($row){
                    return $row->is_merged ? 'Merged' : 'Active';
                })
                ->addColumn('action', function ($row) {
                    if(!$row->is_merged){
                        return '<button class="btn btn-success btn-sm edit" data-url="' . route('dashboard.show', $row->id) . '">Edit</button>
                        <button class="btn btn-danger btn-sm delete" data-url="' . route('dashboard.destroy', $row->id) . '">Delete</button>
                        <button class="btn btn-primary btn-sm merge_contact" data-url="' . route('getContact', $row->id) . '" data-contact_id="'.$row->id.'">Merge Contact</button>';
                    }
                    return '';
                })->rawColumns(['profile_image', 'action'])

                ->make(true);
        }
        $genders = GenderMaster::select('id', 'name', 'slug')->get();
        $custom_fields = ContactCustomFeildMaster::all();
        return view('dashboard.index', compact('genders', 'custom_fields'));
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
            $validationRules = [
                'name' => ['required', 'regex:/^[A-Za-z\s]+$/'],
                'email' => ['required', 'email'],
                'phone' => ['required', 'digits:10'],
                'gender_id' => ['required'],
                'profile_image' => ['required', 'mimes:jpg,png,jpeg', 'max:500'],
                'additional_document' => ['required', 'mimes:jpg,png,pdf', 'max:500']
            ];

            $errorMessages = [
                'name.required' => 'The name field is required.',
                'name.regex' => 'The name must only contain letters and spaces.',

                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',

                'phone.required' => 'The phone number field is required.',
                'phone.digits' => 'The phone number must be exactly 10 digits.',

                'gender_id.required' => 'Please select a gender.',

                'profile_image.required' => 'The profile image is required.',
                'profile_image.file' => 'The profile image must be a valid file.',
                'profile_image.mimes' => 'Only JPG, PNG, and JPEG files are allowed for the profile image.',

                'additional_document.required' => 'The additional document is required.',
                'additional_document.file' => 'The additional document must be a valid file.',
                'additional_document.mimes' => 'Only JPG, PNG, PDF, and DOCX files are allowed for the additional document.',
            ];

            $primaryFields = $request->only('name', 'email', 'phone', 'gender_id', 'profile_image', 'additional_document');

            $all = $request->all();

            $additonalFields = array_diff_key($all, $primaryFields);

            $customFields = ContactCustomFeildMaster::all();

            foreach ($customFields as $field) {
                if ($field->is_required) {
                    $validationRules['additional_information_'.$field->slug] = ['required'];
                    $errorMessages['additional_information_'.$field->slug . '.required'] = 'The ' . $field->label . ' is required.';
                }
            }

            $validated = Validator::make($request->all(), $validationRules, $errorMessages);

            if ($validated->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Valiadtion errors',
                    'errors' => $validated->errors()
                ], 422);
            }

            // Store primary data

            $contact_master = new ContactMaster;
            $contact_master->name = $primaryFields['name'];
            $contact_master->email = $primaryFields['email'];
            $contact_master->phone = $primaryFields['phone'];
            $contact_master->gender_id = $primaryFields['gender_id'];

            if ($request->has('profile_image')) {
                $profile_image = $request->file('profile_image');
                $contact_master->profile_image = $profile_image->store($this->dirName);
            }

            if ($request->has('additional_document')) {
                $additional_document = $request->file('additional_document');
                $contact_master->additional_document = $additional_document->store($this->dirName);
            }

            $contact_master->save();

            //Additional Data Store

            if(count($additonalFields) > 0){
                foreach($customFields as $field){
                    $additionalContactInformation = new ContactAddtionInformaton;
                    $additionalContactInformation->contact_id = $contact_master->id;
                    $additionalContactInformation->value = $all['additional_information_'.$field->slug] ?? null;
                    $additionalContactInformation->custom_field_id = $field->id;
                    $additionalContactInformation->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully.'
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
            $contact = ContactMaster::find($id)->toArray();
            if(!$contact){
                return response()->json([
                    'success' => false,
                    'message' => 'Contact is not found.'
                ], 422);
            }
            $additional_informations = ContactAddtionInformaton::where('contact_id', $contact['id'])->get();
            foreach($additional_informations as $information){
                $slug = $information->custom_field->slug;
                $contact['additional_information_'.$slug] = $information->value;
            }
            return response()->json([
                'success'=>true,
                'message'=>'Contact is found.',
                'data'=> $contact,
                'url' => route('dashboard.update', $id)
            ]);
        }catch(Exception $ex){
            Log::error($ex);
            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);
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
            $validationRules = [
                'name' => ['required', 'regex:/^[A-Za-z\s]+$/'],
                'email' => ['required', 'email'],
                'phone' => ['required', 'digits:10'],
                'gender_id' => ['required'],
                'profile_image' => ['required', 'mimes:jpg,png,jpeg', 'max:500'],
                'additional_document' => ['required', 'mimes:jpg,png,pdf', 'max:500']
            ];

            $errorMessages = [
                'name.required' => 'The name field is required.',
                'name.regex' => 'The name must only contain letters and spaces.',

                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',

                'phone.required' => 'The phone number field is required.',
                'phone.digits' => 'The phone number must be exactly 10 digits.',

                'gender_id.required' => 'Please select a gender.',

                'profile_image.required' => 'The profile image is required.',
                'profile_image.file' => 'The profile image must be a valid file.',
                'profile_image.mimes' => 'Only JPG, PNG, and JPEG files are allowed for the profile image.',

                'additional_document.required' => 'The additional document is required.',
                'additional_document.file' => 'The additional document must be a valid file.',
                'additional_document.mimes' => 'Only JPG, PNG, PDF, and DOCX files are allowed for the additional document.',
            ];

            $primaryFields = $request->only('name', 'email', 'phone', 'gender_id', 'profile_image', 'additional_document');

            $all = $request->all();

            $additonalFields = array_diff_key($all, $primaryFields);

            $customFields = ContactCustomFeildMaster::all();

            foreach ($customFields as $field) {
                if ($field->is_required) {
                    $validationRules['additional_information_'.$field->slug] = ['required'];
                    $errorMessages['additional_information_'.$field->slug . '.required'] = 'The ' . $field->label . ' is required.';
                }
            }

            $validated = Validator::make($request->all(), $validationRules, $errorMessages);

            if ($validated->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Valiadtion errors',
                    'errors' => $validated->errors()
                ], 422);
            }

            // Store primary data

            $contact_master = ContactMaster::find($id);
            $contact_master->name = $primaryFields['name'];
            $contact_master->email = $primaryFields['email'];
            $contact_master->phone = $primaryFields['phone'];
            $contact_master->gender_id = $primaryFields['gender_id'];

            if ($request->has('profile_image')) {
                $profile_image = $request->file('profile_image');
                $contact_master->profile_image = $profile_image->store($this->dirName);
            }

            if ($request->has('additional_document')) {
                $additional_document = $request->file('additional_document');
                $contact_master->additional_document = $additional_document->store($this->dirName);
            }

            $contact_master->save();

            //Additional Data Store

            if(count($additonalFields) > 0){
                foreach($customFields as $field){
                    $additionalContactInformation = ContactAddtionInformaton::find($field->id);
                    $additionalContactInformation->contact_id = $contact_master->id;
                    $additionalContactInformation->value = $all['additional_information_'.$field->slug] ?? null;
                    $additionalContactInformation->custom_field_id = $field->id;
                    $additionalContactInformation->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully.'
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
            $contact_field = ContactMaster::find($id);
            if(!$contact_field){
                return response()->json([
                    'success' => false,
                    'message' => 'Contact is not found.'
                ], 422);
            }
            $contact_field->delete();
            ContactAddtionInformaton::find($contact_field->id)->delete();
            return response()->json([
                'success'=>true,
                'message'=>'Contact deleted successfully.',
                'data'=> $contact_field
            ]);
        }catch(Exception $ex){
            Log::error($ex);
            return response()->json([
                'success'=> false,
                'message'=>'Server error'
            ]);
        }
    }

    public function getContact(Request $request, $id){
        try{
            $contacts = ContactMaster::whereNot('id', $id)->whereNot('is_merged', true)->get();
            $options = '<option value="">Select Contact</option>';
            foreach($contacts as $contact){
                $options.= '<option value="'.$contact->id.'">'.$contact->name.'-'.$contact->email.'</option>';
            }
            return response()->json([
                'success'=>true,
                'message'=>'Contact found successfully',
                'data'=>$options
            ]);
        }catch(Exception $ex){
            Log::error($ex);
            return response()->json([
                'success'=>false,
                'message'=>'Server error'
            ]);
        }
    }

    public function mergeContact(Request $request){
        try{
            $validated = Validator::make($request->only('parent_contact_id', 'contact_id'), [
                'parent_contact_id'=>'required',
                'contact_id'=>'required'
            ]);

            if($validated->fails()){
                return response()->json([
                    'success'=>false,
                    'message'=>'Validation Error',
                    'errors'=>$validated->errors()
                ], 422);
            }

            $contact = ContactMaster::find($request->contact_id);
            $contact->parent_contact_id = $request->parent_contact_id;
            $contact->is_merged = true;
            $contact->save();

            return response()->json([
                'success'=> true,
                'message'=>'Contact merged successfully'
            ]);

        }catch(Exception $ex){
            Log::error($ex);
            return response()->json([
                'success'=> false,
                'message'=> 'Server error'
            ], 500);
        }
    }
}
