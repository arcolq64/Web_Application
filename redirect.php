<?php
	
	require 'includes/functions.php';

	// $pswd = "Er1C/er0";
	// $sltp = md5($pswd . SALT);
	// echo $sltp;
	// exit();
		
	if(count($_POST) > 0 && $_GET['from'] == 'login')
	{
		// assume not found for regular users and admin
		$found = false;  // regular users
		$admnf = false;	 // admin

		$user = trim($_POST['user']);
		$pass = trim($_POST['password']);
		$admnf = findAdmin($user, $pass);	// Either 0 or 1
		
		if($admnf == 1) {	// User is an administrator (true)
			
			session_start();
			$_SESSION['loggedin'] = true;
			$_SESSION['username'] = $user;
			$_SESSION['adminuser'] = true;
			header('Location: thankyou.php?type=login&username='.$user);
			
		} else {
			
			$found = 0;
			
			if(checkUsername($user))
			{
				$found = findUser($user, $pass, 'username');
				// echo "#1: " . $found . "<br>";
			}
			elseif(checkPhoneNumber($user))
			{
				$found = findUser($user, $pass, 'phone');
				// echo "#2: " . $found . "<br>";
			}
			
			if($found)
			{
				session_start();
				$_SESSION['loggedin'] = true;
				$_SESSION['username'] = $user;
				$_SESSION['adminuser'] = false;
				header('Location: thankyou.php?type=login&username='.$user);
			}
			else
			{
				setcookie('error_message', 'Login not found! Try again.');
				header('Location: login.php');
			}
		}
		
		exit();
		
	}
	elseif(count($_POST) > 0 && $_GET['from'] == 'signup')
	{	
		$check = checkSignUp($_POST);

		if($check !== true)
		{
			setcookie('error_message', $check);
			header('Location: signup.php');
		}
		else
		{
			$userfound = false; 
			$username = trim($_POST['username']);
			
			//-----------------------------------------------
			// Connect to Database
			//-----------------------------------------------
			
			$dbh = mysqli_connect("localhost", "arcwebde_connect", "A$&slo#8143", "arcwebde_KPMG") or die ('Connection error: ' . mysqli_error($dbh));

			// Check connection
			if ($dbh -> connect_errno) {
			  echo "Failed to connect to MySQL: " . $dbh -> connect_error;
			  exit();
			}
			
			//-----------------------------------------------
			// Determine if a Username entry exists.
			// The aim is to avoid duplicate entries.
			//-----------------------------------------------
			
			$result = $dbh->query("SELECT Username FROM Users WHERE Username = '$username'");
			if($result->num_rows == 0) {
				$userfound = false;
			} else {
				$userfound = true;
			}
			$dbh->close();

			/*
			$file_pointer = './users.txt';
			if(file_exists($file_pointer)) {
			
				$lines = file('users.txt');
				foreach ($lines as $line)
				{
					$pieces = preg_split('/\|/', $line);
					if($username == trim($pieces[0])) {	// Valid Username
					   $userfound = true;				// If true, record is found
					}
				}					
			}
			*/
			
			if($userfound == false) {
				if(saveUser($_POST))
				{
					session_start();
					$_SESSION['loggedin'] = true;
					$_SESSION['username'] = filterUserName(trim($_POST['username']));
					$_SESSION['adminuser'] = false;
					header('Location: thankyou.php?type=signup&username='.trim($_POST['username']));
				}
				else
				{
					setcookie('error_message', 'Unable to sign up at this time.');
					header('Location: signup.php');
				}
			} else {
				setcookie('error_message', 'Username not available.');
				header('Location: signup.php');
			}
		}

		exit();
	}

	// should never reach here but if we do, back to index they go
	header('Location: index.php');
	exit();

?>