<?php

define('SALT', 'a_very_random_salt_for_this_app');

/**
 * Look up the user & password pair from the admin file.
 *
 * @param $user string The username or phone number to look up
 * @param $password string The password to look up
 * @return bool true if found, false if not
 */
function findAdmin($user, $password)
{
    $admnf = 0;

    $lines = file('admin.ini');

    foreach($lines as $line)
    {
        $pieces = preg_split("/\|/", $line); // | is a special character, so escape it
		
        if( trim($pieces[1]) == $user && trim($pieces[2]) == md5($password . SALT))
        {
            $admnf = true;
        }
    }

	return $admnf;
}

/**
 * Look up the user & password pair from the text file.
 *
 * User can be the username or the phone number.
 * Passwords are simple md5 hashed.
 *
 * @param $user string The username or phone number to look up
 * @param $password string The password to look up
 * @param $field string user|phone
 * @return bool true if found, false if not
 */
function findUser($user, $password, $field)		// Only used for regular users
{
	$found = false;
	$passwordHash = md5($password . SALT);
	
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
	// Determine if Login is OK
	//-----------------------------------------------
	
	if($field == 'username') {
		$check = $dbh->query("SELECT * FROM Users WHERE Username = '$user' && Password = '$passwordHash'");
	}
	if($field == 'phone') {
		$check = $dbh->query("SELECT * FROM Users WHERE PhoneNumber = '$user' && Password = '$passwordHash'");
	}
	
	if($check->num_rows == 0) {
		return 0;
	} else {
		return 1;
	}
	$dbh->close();
	
	/*
    $lines = file('users.txt');
    foreach($lines as $line)
    {
        $pieces = preg_split("/\|/", $line); // | is a special character, so escape it
        if($field == 'username' && $pieces[0] == $user && trim($pieces[2]) == md5($password . SALT))
        {
            $found = true;
        }
        elseif($field == 'phone' && $pieces[1] == $user && trim($pieces[2]) == md5($password . SALT))
        {
            $found = true;
        }
    }
	*/
	
}

/**
 * md5() should not be used for production purposes
 *
 * @param $data
 * @return bool returns false if fopen() or fwrite() fails
 */
function saveUser($data)
{
    $success = false;

	//-----------------------------------------------
	// Connect to Database
	//-----------------------------------------------
	
	$dbh = mysqli_connect("localhost", "arcwebde_connect", "A$&slo#8143", "arcwebde_KPMG") or die ('Connection error: ' . mysqli_error($dbh));

	// Check connection
	if ($dbh -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $dbh -> connect_error;
	  exit();
	}
	
	$username       = trim($data['username']);
	$phoneNumber    = trim(preg_replace("/[^0-9]/", '', $data['phoneNumber']));
	$password       = trim($data['password']);
	$passwordHash   = md5($password . SALT);
	
	if (!$result = $dbh->query("INSERT INTO Users (Username, PhoneNumber, Password) VALUES ('".$username."', '".$phoneNumber."', '".$passwordHash."')"))
	{
		echo "Database Insert Error";
		exit();
	}
	
	$dbh->close();	
	
	/*
    $fp = fopen('users.txt', 'a+');
    if($fp != false)
    {
        $username       = trim($data['username']);
        $phoneNumber    = trim(preg_replace("/[^0-9]/", '', $data['phoneNumber']));
        $password       = trim($data['password']);
        $passwordHash   = md5($password . SALT);
        $results = fwrite($fp, $username.'|'.$phoneNumber.'|'.$passwordHash. PHP_EOL);
        fclose($fp);
        if($results)
        {
            $success = true;
        }
    }
	*/

}

function checkUsername($username)
{
    return preg_match('/^[a-z]([a-z]|[0-9]){6}([a-z]|[0-9])*[0-9]+$/i', $username);
}

function checkPhoneNumber($phoneNumber)
{
    // assuming phone numbers can start with a 0
    return preg_match("/^[0-9]{7}$|^[0-9]{10}$/", $phoneNumber);
}

/**
 * @param $data
 * @return bool|string
 */
function checkSignUp($data)
{
    $valid = true;

    // if any of the fields are missing, return an error
    if( trim($data['username'])        == '' ||
        trim($data['phoneNumber'])     == '' ||
        trim($data['password'])        == '' ||
        trim($data['verify_password']) == '')
    {
        $valid = "All inputs are required.";
    }
    elseif(!preg_match('/^[a-z]([a-z]|[0-9]){6}([a-z]|[0-9])*[0-9]+$/i', trim($data['username'])))
    {
        $valid = "Invalid username!";
    }
    elseif(!preg_match("/^((\([0-9]{3}\))|([0-9]{3}))?( |-)?[0-9]{3}( |-)?[0-9]{4}$/", trim($data['phoneNumber'])))
    {
        $valid = "Invalid phone number!";
    }
    else if(!preg_match('/((?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[,.\/\?\*!])){8}/', trim($data['password'])))
    {
        $valid = "Invalid password!";
    }
    elseif($data['password'] != $data['verify_password'])
    {
        $valid = 'Passwords do not match!';
    }

    return $valid;
}

/**
 * @param $data
 * @return bool
 */
function checkPost($data, $username)
{
    $valid = true;

    // if any of the fields are missing, return an error
    if( trim($data['username']) == '' ||
        trim($data['tweet'])    == '' ||
        trim($data['priority']) == '')
    {
        $valid = "All inputs are required.";
    }
    elseif($username != trim($data['username']))
    {
        $valid = "Invalid username!";
    }
    elseif(!preg_match('/^admin||[a-z]([a-z]|[0-9]){6}([a-z]|[0-9])*[0-9]+$/i', trim($data['username'])))
    {
        $valid = "Invalid username!";
    }
    elseif(!preg_match('/^[a-z0-9 ,\.\'?!]+$/i', trim($data['tweet'])))
    {
        $valid = "Invalid tweet!";
    }
    elseif(!preg_match('/^[1-3]$/i', trim($data['priority'])))
    {
        $valid = "Invalid priority!";
    }

    return $valid;
}

/**
 * @param $data
 * @return bool true on successful write
 */
function savePost($data)
{
    $success = false;

	//-----------------------------------------------
	// Connect to Database
	//-----------------------------------------------
	
	$dbh = mysqli_connect("localhost", "arcwebde_connect", "A$&slo#8143", "arcwebde_KPMG") or die ('Connection error: ' . mysqli_error($dbh));

	// Check connection
	if ($dbh -> connect_errno) {
	  echo "Failed to connect to MySQL: " . $dbh -> connect_error;
	  exit();
	}
	
	$postid   = uniqid();
	$username = trim($data['username']);
	$tweet    = trim($data['tweet']);
	$priority = trim($data['priority']);
	
	if (!$result = $dbh->query("INSERT INTO Posts (Post_ID, Username, Tweet, Priority) VALUES ('".$postid."', '".$username."', '".$tweet."', '".$priority."')"))
	{
		echo "Database Insert Error";
		exit();
	} else {
		$success = true;
	}
	
	$dbh->close();
	
	/*
    $fp = fopen('posts.txt', 'a+');
    if($fp != false)
    {
        $id         = uniqid();
        $username   = trim($data['username']);
        $tweet      = trim($data['tweet']);
        $priority   = trim($data['priority']);
        $results = fwrite(
            $fp,
            $id.'|'.$username.'|'.$tweet.'|'.$priority.PHP_EOL
        );
        fclose($fp);
        if($results)
        {
            $success = true;
        }
    }
	*/

    return $success;
}

function getAllPosts()
{
	
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
	// Fetch Data From Database
	//-----------------------------------------------
	
	$result = $dbh->query("SELECT * FROM Posts");
	
	if($result->num_rows == 0) {
		echo "Error: Found No Posts";
		exit();
	} else {
		
		
		
		// w truncates the file
		$fp = fopen('posts.txt', 'w');
		
		while ($row = $result->fetch_assoc()) {
			$a = $row['Post_ID'];
			$b = $row['Username'];
			$c = $row['Tweet'];
			$d = $row['Priority'];
			$string = $a . "|" . $b . "|" . $c . "|" .$d . PHP_EOL;
			fwrite($fp,$string);
		}
		
		fclose($fp);
		
		//-----------------------------------------------
		// Process Intermediate Text File
		//-----------------------------------------------
		
		$posts = [];
		$lines = file('posts.txt');
		
		if($lines != false)
		{
			$importantPosts = [];
			$highPosts      = [];
			$normalPosts    = [];

			foreach($lines as $line)
			{
				$pieces = preg_split("/\|/", $line);
				if(trim($pieces[3]) == 1)
				{
					$importantPosts[] = $pieces;
				}
				elseif(trim($pieces[3]) == 2)
				{
					$highPosts[] = $pieces;
				}
				elseif(trim($pieces[3]) == 3)
				{
					$normalPosts[] = $pieces;
				}
			}
			
			$posts = array_merge($importantPosts, $highPosts, $normalPosts);
		}
		
		$file_pointer = "posts.txt"; 
		// Use unlink() function to delete posts.txt file 
		if (!unlink($file_pointer)) { 
			echo ("$file_pointer cannot be deleted due to an error"); 
		}
		
		return $posts;
	
	}
}

function getPriorityTag($id)
{
    $tags = [
        1 => 'panel-danger',
        2 => 'panel-warning',
        3 => 'panel-info'
    ];

    return $tags[$id];
}

function deletePost($id, $username)
{
	
	$lines = file('posts.txt');

    if($lines != false)
    {
        // w truncates the file
        $fp = fopen('posts.txt', 'w');

        // comb through all existing lines
        foreach($lines as $line)
        {
            $pieces = preg_split("/\|/", $line);

            if($pieces[0] == $id && $pieces[1] == $username || 
			   $pieces[0] == $id && $_SESSION['adminuser'] == true && $_SESSION['username'] == "admin")
            {
                continue;       // skip this line if this is the post to delete
            }

            fwrite($fp, $line); // include this line
        }

        fclose($fp);
    }
}

function filterUserName($name)
{
    // if it's not alphanumeric, replace it with an empty string
    return preg_replace("/[^a-z0-9]/i", '', $name);
}
