<?php
	
	require 'includes/functions.php';
	session_start();
	
	$message = '<div class="alert alert-danger text-center">';
	$valid = 0;
	
	$_SESSION['cmnt_id'] = $_GET['cm'];
	
?>
<!DOCTYPE html>
<html>
<head>
    <title>Web Application</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div id="wrapper" style="margin-bottom: 85px;">

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <h1 class="login-panel text-center text-muted">
                    Web Application
                </h1>
                <hr/>
            </div>
        </div>
		<form role="form" method="post" action="process.php"
		style = "border: 3px solid black; font-size: 24; margin: 50px 50px; padding: 35px 35px;">
			<div>
				<div>
					<h2 style="text-align: center">Submit Comment</h2>
				</div><br>
				<div>
					<div>
						<input id="username2" class="form-control" disabled="disabled" type="text" placeholder="Username" name="username2" value="<?php echo $_SESSION['username']; ?>">
					</div><br>
					<div>
						<label>Comment</label><br>
						<textarea id="comment2" class="form-control" rows="3" value="" name="comment2"></textarea>
					</div><br>
				</div>
				<div id="response"></div><br>
				<div>
					<input type="submit" class="btn btn-primary" name="submit" onClick="empty()" value="Submit!"/>
				</div>
			</div>
		</form><br>
        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center">
                <a href="posts.php" class="btn btn-default btn-lg">
                    <i class="fa fa-list"></i> Posts
                </a>
            </div>
        </div>

    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
