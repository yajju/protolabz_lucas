<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class AdminController extends Controller
{
    public function home()
    {
        // echo "Hello";
        // dd("Hello");
        // return view("storeDetails");
        return view('admin.auth.sign_in');
        // $colorData="";
        // $optionData="";
        // return view("storeDetails",compact(['colorData','optionData']));
    }

    public function login_view()
    {
        if(Auth::user())
        {
            return redirect('admin/dashboard');
        }
        return view('admin.auth.signin');        
    }

    public function admin_login(Request $request)
    {
        // print_r($request->input());
        // // $input = $request->all();
        // // print_r($input);
        // die();
        $request->validate([
            'email'          => 'required',
            'password'       => 'required',
        ]);
        
        $credentials = [
            'email' => $request['email'],
            'password' => $request['password'],
        ];
        // print_r($credentials);die();

        if(Auth::attempt($credentials))
        {
            return redirect('admin/dashboard');
        }
        else
        {
            return back()->with('errormessage', 'Please Enter Valid Credentials !');
        }
      
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect('admin/login');
    }
}
