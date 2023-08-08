<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Merchant;
use Auth;
use Validator;
use DB;
use Hash;


class AdminController extends Controller
{
    public function home()
    {
        // echo "Hello";
        // dd("Hello");
        // return view("storeDetails");
        return view('admin.auth.signin');
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
        
        $credentials = [
            'email' => $request['email'],
            'password' => $request['password'],
        ];
        // print_r($credentials);die();

        // if(Auth::attempt($credentials))
        if (Auth::guard('web')->attempt($credentials))
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

    /////////////////////////////////////////
    public function forgot_password()
    {
        return view('admin.forgot-password');
    }

    public function new_registration()
    {
        return view('admin.new-registration');
    }

    public function change_passwordform()
    {
        return view('admin.change-password');
    }
    public function change_password(Request $request)
    {
        // dd($request->all());
        // $id = Auth::guard('web')->user()->id;
        // dd("ID : ".$id);
        // dd("ID : ".Auth::guard('web')->user());

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

        if(Hash::check($request->currentpassword,Auth::guard('web')->user()->password))
        {
            $id = Auth::guard('web')->user()->id;
            $admin['password'] = bcrypt($request->newpassword);
            // $password = DB::table('users')->where('id',$id)->update($admin);
            $password = User::where('id',$id)->update($admin);
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

    public function merchants()
    {
        $myID=Auth::guard('web')->user()->id;
        // if (Auth::guard('admin')->user()->id)
        // {
            // $data = DB::table('users')->select('name', 'email', 'created_at', 'password', 'deleted_at')->get();
            $data = Merchant::select('id', 'name', 'email', 'telephone', 'password', 'created_at', 'status')->where('id','<>',$myID)->orderBy('id','Desc')->get();
            $result = json_decode(\json_encode($data, true));
            // dd($result);
            return view('admin.merchants', compact('result'));
        // }
    }
    public function updateStatus(Request $request, $userID, Merchant $merchant)
    {
        // dd('status : '.$status);
        // dd('status : '.$request->status);
        // Toggle the status between 0 and 1
        // dd('userID : '.$userID);
        // $status = $merchant->status === 1 ? 0 : 1;
        if(($request->status=='') || ($request->status=='0')){$newStatus='1';$statText="Activated Successfully";}
        elseif($request->status=='1'){$newStatus='0';$statText="Deactivated Successfully";}
        // dd('status : '.$newStatus);
        $data = Merchant::where('id',$userID)->update(['status'=>$newStatus]);
        
        return response()->json(['message' => $statText, 'status' => $newStatus]);
    }

    public function transactions()
    {
        return view('admin.transactions');
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function support()
    {
        return view('admin.support');
    }

    public function documentation()
    {
        return view('admin.documentation');
    }
}
