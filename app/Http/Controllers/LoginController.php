<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as BaseController;
use Auth;
use Illuminate\Support\Facades\Input;
use Validator;
use Cookie;

class LoginController extends BaseController
{
    public function __construct() {
		
    }
	
	/*
	User Login 
	*/	
    public function showLoginForm() {
		return view('login');		
    }	
	
	/*
	User Login Request
	*/	
    public function loginProcess(Request $request)   {	
	
		$validator = Validator::make(
			$request->all(),
			[
				'email'    => 'required|email',
				'password' => 'required',
			],
			[
				'email.required'    => 'Email is required.',
				'email.email'       => 'Please enter a valid email address.',
				'password.required' => 'Password is required.',
			]
		);

		if ($validator->fails()) {
			return redirect()->back()->withInput()->with('ERROR', $validator->errors()->first());
		}	
		
		if (Auth::guard('web')->attempt(['email' => $request->email,'password' => $request->password],$request->boolean('remember'))) {
			
			$request->session()->regenerate();

			if ($request->boolean('remember')) {
				Cookie::queue('site_admin_email', $request->email, 60 * 24 * 30); // 30 days
			} else {
				Cookie::queue(Cookie::forget('site_admin_email'));
			}

			return redirect()->route('dashboard');
		}

		return redirect()->route('login')->withInput()->with('ERROR', 'Invalid email or password.');	
	}	
	
	/*
	User Logout Request
	*/
	public function logout(Request $request)	{
		
		Auth::guard('web')->logout();

		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return redirect()->route('login');
	}	
}