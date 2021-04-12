<?php

    namespace App\Controllers;
	use App\Models\users;


    class Forgot extends Home
    {
        function __construct()
        {
            parent::__construct();
        }
		
		public function index()
		{
			//IF LOGGED IN, REDIRECT
			if(session()->get('loggedin'))
				return redirect('/');
			//INITIATE DATABASE CONNECTION
			$db = db_connect();
			$request = service('request');
			$creds = $request->getPost();
			$ip = $request->getIPAddress();
			$ip = inet_pton($ip);

			if(!isset($creds['submit']) || $creds['submit'] !== 'Submit')
			{
				echo view('templates/header');
				echo view('forgot');
				return view('templates/footer');
			}

			$email = $creds['email'];
			//CHECK IF INPUT IS VALID
			$valid = $this->validate([
				'email' => ['label' => 'Email', 'rules' => "required|valid_email|is_not_unique[auth.email]"]
			]);

			//IF NOT
			if(!$valid)
			{
				echo view('templates/header');
				echo view('forgot',[
					'validation' => $this->validator
				]);
				return view('templates/footer');
			}

			//CREATE USERS MODEL OBJECT
			$model = new users($db);
			$model->requestReset($email, $ip);
		}
    }