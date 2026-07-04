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
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController {

    public function __construct() {
		
    }
	
	public function index()   {
	
		$params = array();
		
		return view('dashboard',$params);
	}
	

}