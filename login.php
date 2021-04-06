<?php

	$message = '';
	if(isset($_COOKIE['error_message']))
	{
		$message = '<div id="message" class="alert alert-danger text-center">'
			. $_COOKIE['error_message'] .
		'</div>';

		setcookie('error_message', null, time() - 3600);
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
                <?php echo $message; ?>
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
						<small>Phone numbers should have no dashes (-).</small>
                    </div>
                    <div class="panel-body">
                        <form name="login" role="form" action="redirect.php?from=login" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control"
                                           value="admin"
                                           name="user"
                                           placeholder="Username or Phone Number)"
                                           type="text"
                                           autofocus
                                    />
                                </div>
                                <div class="form-group">
                                    <input class="form-control"
									       value="Er1C/er0"
                                           name="password"
                                           placeholder="Password"
                                           type="password"
                                    />
                                </div>
                                <input type="submit" class="btn btn-lg btn-success btn-block" value="Login" style="margin-top: 10px;" />
                            </fieldset>
                        </form>
                    </div>
                </div>
				<br>
				Go to Signup Page:<br>
                <a class="btn btn-sm btn-default" href="signup.php" style="margin-top: 10px;">Sign Up</a>
            </div>
        </div>

    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
