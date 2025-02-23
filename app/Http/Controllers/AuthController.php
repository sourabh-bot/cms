<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //

    public function index(Request $request){
       return view('auth.index');
    }

    public function userLogin(Request $request){
        try{
            $validated = Validator::make($request->only(['email', 'password']), [
                'email'=>'required|email',
                'password'=>'required'
            ]);
            if($validated->fails()){
                return redirect()->back()
                    ->withErrors($validated->errors())
                    ->withInput($request->all());
            }
            if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password])){
                return redirect()->route('dashboard.index')->with('success', 'Login successful!');
            }
            return redirect()->back()->with('error', 'Invalid credentials.');
        }catch(Exception $ex){
            Log::error($ex);
            return redirect()->back()->with('error', 'Server errors');
        }
    }

    public function userLogout(){
        Auth::logout();
        return redirect()->route('login');
    }
}
