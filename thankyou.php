<?php

require 'includes/functions.php';

$username = filterUserName($_GET['username']);

if($_GET['type'] == 'login')
{
    $message = 'Thank you '.$username.' for logging in!';
}
else
{
    $message = 'Thank you '.$username.' for signing up!';
}

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
                <div class="alert alert-success text-center">
                    <?php echo $message; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center" style="margin-top: 10px;">
				Access User Web Posts:<br>
                <a href="posts.php" class="btn btn-default btn-lg" style="margin-top: 10px;">
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
