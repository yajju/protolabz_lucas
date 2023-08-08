<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;
use Session;
use Auth;
use Validator;
use DB;
use Hash;

class MerchantController extends Controller
{
    public function home()
    {
        // echo "Hello";
        // dd("Hello");
        // return view("storeDetails");
        return view('merchants.auth.signin');
        // $colorData="";
        // $optionData="";
        // return view("storeDetails",compact(['colorData','optionData']));
    }

    public function login_view()
    {
        // if(Auth::user())
        if(Auth::guard('merchant')->user())
        {
            return redirect('merchant/dashboard');
        }
        // dd("as : ".Auth::guard('merchant')->user());
        return view('merchants.auth.signin');        
    }

    public function logged(Request $request)
    {
        // print_r($request->input());
        // // $input = $request->all();
        // // print_r($input);
        // die();
        $request->validate([
            'email'          => 'required',
            'password'       => 'required',
        ]);        
        
        // $credentials = [
        //     'email' => $request['email'],
        //     'password' => $request['password'],
        // ];
        // // print_r($credentials);die();

        // if(Auth::attempt($credentials))
        // {
        //     return redirect('admin/dashboard');
        // }
        // else
        // {
        //     return back()->with('errormessage', 'Please Enter Valid Credentials !');
        // }

        $credentials = $request->only('email', 'password');

        if (Auth::guard('merchant')->attempt($credentials))
        {
            return redirect()->intended('merchant/dashboard')
                        ->withSuccess('Signed in');
            // return redirect()->back();
        }
        else
        {
            return back()->with('errormessage', 'Please Enter Valid Credentials !');
        }
      
    }

    public function dashboard()
    {
        // $profile_image=Auth::guard('merchant')->user()->profile_image;
        // return view('merchants.dashboard',compact('profile_image'));
        return view('merchants.dashboard');
    }

    public function logout()
    {
        Auth::guard('merchant')->logout();
        return redirect('merchant/login');
    }

    /////////////////////////////////////////
    public function forgot_password()
    {
        return view('merchants.forgot-password');
    }

    public function registration_form()
    {
        return view('merchants.auth.registration');
    }
    public function register(Request $request)
    {
        // dd($request);
        $rol = [
			'uname' => 'required|string|max:25',
            'email' => 'required|email|unique:merchants,email',
            'mobile' => 'required|digits:10',
            'password' => 'required|min:6',
		];
		$message = [
			'uname.required' => 'Name is Required',
			'uname.string' => 'Name field must be a String',
			'uname.max' => 'Name cant be more than :max characters',

			'email.required' => 'Email is Required',
			'email.email' => 'Email must be a valid email address',
			'email.unique' => 'This Email has already been taken',

			'mobile.required' => 'Mobile is Required',
			'mobile.digits' => 'Mobile must be exactly digits',

			'password.required' => 'Password is Required',
			'password.min' => 'Password must be at least :min characters',
		];
		$validator = Validator::make($request->all(), $rol, $message);
        // dd($validator);
		if ($validator->fails())
        {
			// return response()->json(['status' => '0','message' => $validator->errors()]);
            return redirect()->back()->with('errmsg',$validator->errors()->first());
		}

        // dd("done");
        $user['name']  = $request->uname;
        $user['email']  = $request->email;
        $user['telephone']  = $request->mobile; 
        $user['password']  = bcrypt($request->password); 
        $user['status']  = "0";
        // $user_id = DB::table('merchants')->insertGetId($user);
        $user_id = Merchant::insertGetId($user);
        if($user_id)
        {
            return redirect()->back()->with('succmsg','Merchant Registered Successfully<br>Please wait for the approval <meta http-equiv="Refresh" content="5;/merchant/login">');
        }
        else
        {
            return redirect()->back()->with('errmsg','Some Error found');
        }
    }

    public function change_passwordform()
    {
        return view('merchants.change-password');
    }
    public function change_password(Request $request)
    {
        // dd($request->all());
        // $id = Auth::guard('merchant')->user()->id;
        // dd("ID : ".$id);
        // dd("ID : ".Auth::guard('merchant')->user());

        // $request->validate([
        //     'currentpassword' => 'required|min:8',
        //     'newpassword' => 'required|min:8',
        //     'confirmpassword'  => 'required|same:newpassword|',          
        //   ]); 


                // $validator = Validator::make($request->all(), [            
                //     'currentpassword' => 'required|min:8',
                //     'newpassword' => 'required|min:8',
                //     'confirmpassword'  => 'required|same:newpassword|',
                // ]);
                // if ( $validator->fails())
                // {
                //     // return response()->json([
                //     //     'status'=>'0',
                //     //     'message'=>$validator->errors()->first(),
                //     // ]);
                //     return redirect()->back()->with('errmsg',$validator->errors()->first() );
                // }

        $rol = [
			'currentpassword' => 'required|min:2',
            'newpassword' => 'required|min:2',
            'confirmpassword'  => 'required|same:newpassword|',
		];
		$message = [
			'currentpassword.required' => 'Current Password is Required',
			'currentpassword.min' => 'Current Password - Minimum Two (2) Character\'s Required',
			'newpassword.required' => 'Current Password is Required',
			'newpassword.min' => 'New Password - Minimum Two (2) Character\'s Required',
			'confirmpassword.same' => 'New Password is Mismatched',
		];
		$validator = Validator::make($request->all(), $rol, $message);
		if ($validator->fails())
        {
			// return response()->json(['status' => '0','message' => $validator->errors()]);
            return redirect()->back()->with('errmsg',$validator->errors()->first());
		}

        if(Hash::check($request->currentpassword,Auth::guard('merchant')->user()->password))
        {
            $id = Auth::guard('merchant')->user()->id;
            $admin['password'] = bcrypt($request->newpassword);
            // $password = DB::table('merchants')->where('id',$id)->update($admin);
            $password = Merchant::where('id',$id)->update($admin);
            if($password)
            {
                return redirect()->back()->with('succmsg','Password Updated');
            }
            else
            {
                return redirect()->back()->with('errmsg','Password Not Updated');
            }
        }
        else
        {
            return redirect()->back()->with('errmsg','Current Password Not Matched');
        }   
    }
    ////////////////////////////////////////

    public function profile()
    {
        // if (Auth::guard('admin')->user()->id)
        // {
            $id = Auth::guard('merchant')->user()->id;
            // $result = DB::table('merchants')->select('name', 'email', 'created_at', 'password', 'profile_image')->where('id',$id)->get();
            $result = Merchant::select('name', 'email', 'created_at', 'password', 'profile_image')->where('id',$id)->get();
            // $result = json_decode(json_encode($result, true));
            // $result = json_encode($result);
            $result = json_decode($result,true);
            $profile_image=$result[0]['profile_image'];
            return view('merchants.profile', compact('result','id','profile_image'));
        // }
    }
    public function profileupdate(Request $request,$id)
    {
        $rol = [
			'cname' => 'required|min:2',
            'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
		];
		$message = [
			'cname.required' => 'Name is Required',
			'cname.min' => 'Name must be of minimum Two (2) Character\'s',
			// 'profile_image.image' => 'Image is Required',
			'profile_image.mimes' => 'Image can be of jpeg png jpg gif',
			'profile_image.max' => 'Image max allowed size is 2 MB',
		];
		$validator = Validator::make($request->all(), $rol, $message);
		if ($validator->fails())
        {
			// return response()->json(['status' => '0','message' => $validator->errors()]);
            return redirect()->back()->with('errmsg',$validator->errors()->first());
		}

        // ///////////////////////////////////////////////
        // {
        //     $request->validate([
        //         'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the validation rules as needed
        //     ]);
    
        //     if ($request->hasFile('profile_image')) {
        //         $image = $request->file('profile_image');
        //         $imageName = time() . '.' . $image->getClientOriginalExtension();
        //         $image->move(public_path('images/profile'), $imageName);
    
        //         // Save the image path to the user's profile_image column in the database
        //         auth()->user()->update(['profile_image' => 'images/profile/' . $imageName]);
        //     }
    
        //     return redirect()->back()->with('success', 'Profile image uploaded successfully!');
        // }
        // ///////////////////////////////////////////////
        
        if($request->cname)
        {
            $user['name'] = $request->cname;
        }
        if ($request->hasFile('profile_image'))
        {
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/profile'), $imageName);

            // Save the image path to the user's profile_image column in the database
            // auth()->user()->update(['profile_image' => 'images/profile/' . $imageName]);
            $user['profile_image'] = 'images/profile/' . $imageName;
        }

        $id = Auth::guard('merchant')->user()->id;
        // $userupdated = DB::table('merchants')->where('id',$id)->update($user);
        $userupdated = Merchant::where('id',$id)->update($user);
        if($userupdated)
        {
            return redirect()->back()->with('succmsg','User Profile Updated');
        }
        else
        {
            return redirect()->back()->with('errmsg','User Profile not Updated');
        }
    }

    public function transactions()
    {
        return view('merchants.transactions');
    }

    public function reports()
    {
        return view('merchants.reports');
    }

    public function support()
    {
        return view('merchants.support');
    }

    public function documentation()
    {
        return view('merchants.documentation');
    }

}
