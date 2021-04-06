<?php

	require 'includes/functions.php';

	session_start();
	
	if(!isset($_SESSION['loggedin']))
	{
		header('Location: index.php');
		exit();
	}
	
	$postid = $_SESSION['cmnt_id'];
	$username = $_SESSION['username'];
	$comment = $_POST['comment2'];
	// echo $post_ID . "<br>";
	// echo $username . "<br>";
	// echo $comment . "<br>";

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
	
	//-----------------------------------------------
	// Write to Database
	//-----------------------------------------------
	
	if (!$result = $dbh->query("INSERT INTO Comments (Post_ID, Username, Comment) VALUES ('".$postid."', '".$username."', '".$comment."')"))
	{
		echo "Database Insert Error";
		exit();
	} else {
		echo "Success";
		$success = true;
	}
	
	$dbh->close();
			
?>