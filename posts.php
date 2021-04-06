<?php

	require 'includes/functions.php';
	$message = '';
	session_start();

	if(!isset($_SESSION['loggedin']))
	{
		header('Location: index.php');
		exit();
	}

	if(count($_POST) > 0)
	{
		$check = checkPost($_POST, $_SESSION['username']);
		if($check !== true)
		{
			$message = '<div id="message" class="alert alert-danger text-center">'
				. $check .
			'</div>';
		}
		else
		{
			savePost($_POST);
		}
	}

	$posts = getAllPosts();
	
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
	// Read Comments From Database ==> Text File
	//-----------------------------------------------
	
	$result = $dbh->query("SELECT * FROM Comments");
	
	// w truncates the file
	$fp = fopen('comments.txt', 'w');
	
	while ($row = $result->fetch_assoc()) {
		// echo $row['Post_ID'] . "<br>";
		// echo $row['Username'] . "<br>";
	    // echo $row['Comment'] . "<br>";
		$a = $row['Post_ID'];
		$b = $row['Username'];
		$c = $row['Comment'];
		$string = $a . "|" . $b . "|" . $c . PHP_EOL;
		fwrite($fp,$string);
	}
	
	fclose($fp);
	
	$dbh->close();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Web Application</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet">
	<meta http-equiv='cache-control' content='no-cache'>
</head>
<body>

<div id="wrapper" style="margin-bottom: 85px;">

    <div class="container">

        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <h1 class="login-panel text-center text-muted" style="padding-bottom: 25px;">
                    Web Application
                </h1>
                <hr/>
                <?php echo $message; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <button class="btn btn-default" data-toggle="modal" data-target="#newPost"><i class="fa fa-rss"></i> New Tweet</button>
                <a href="logout.php" class="btn btn-default pull-right"><i class="fa fa-sign-out"> </i> Logout</a>
                <hr/>
            </div>
        </div>

        <?php
		
			//-----------------------------------------------
			// Connect to Database
			//-----------------------------------------------
			
			$dbh = mysqli_connect("localhost", "arcwebde_connect", "A$&slo#8143", "arcwebde_KPMG") or die ('Connection error: ' . mysqli_error($dbh));

			// Check connection
			if ($dbh -> connect_errno) {
			  echo "Failed to connect to MySQL: " . $dbh -> connect_error;
			  exit();
			}
			
            foreach($posts as $post)
            {
				
				echo '
				<div class="row">
					<div class="col-md-6 col-md-offset-3">
						<div class="panel '.getPriorityTag(trim($post[3])).'">
							<div class="panel-heading">
								<span>
									'.$post['2'].'
								</span>
								<span class="pull-right text-muted">';
									echo '
									<a class="" href="comment.php?cm='.$post['0'].'">
										<i class="fa fa-trash"></i> Comment
									</a>&nbsp;';		
									if($_SESSION['username'] == $post[1] || $_SESSION['adminuser'] == true)
									{
										echo '
										<a class="" href="delete.php?id='.$post['0'].'">
											<i class="fa fa-trash"></i> Delete
										</a>&nbsp;';
									}

									if($_SESSION['adminuser'] == true) 
									{	
		?>
										<a id="<?php echo $post['0']; ?>" class="edit_data" data-toggle="modal" data-target="#editPost">
											<i class="fa fa-edit"></i> Edit
										</a>
		<?php
									}
									echo '
									
								</span>
							</div>
							<div class="panel-body">
								<p class="text-muted">
								</p>
								<p>';
							
									$filename = "comments.txt";
									if (file_exists($filename)) {
										$lines = file($filename);
									} else {
										$lines = false;
									}
									
									$fp = fopen($filename, "r");
									if($lines != false)
									{
										$comments = 0;
										echo "COMMENTS:<br>";
										// comb through all existing lines
										foreach($lines as $line)
										{
											$pieces = preg_split("/\|/", $line);
											if($pieces[0] == $post['0'])
											{
												$comments = 1;
												echo $pieces[1] . " &#11166; " . $pieces[2]. "<br>";
												continue;
											}
										}
										if($comments != 1) {
											echo "No comments";
										}
									}
									
									fclose($fp);
									
								echo '</p>
							</div>
							<div class="panel-footer">
								<p>
									'.$post['1'].'
								</p>
							</div>
						</div>
					</div>
				</div>';
            }
			
			$file_pointer = "comments.txt"; 
			// Use unlink() function to delete posts.txt file 
			if (!unlink($file_pointer)) { 
				echo ("$file_pointer cannot be deleted due to an error"); 
			}
			
			$dbh->close();
        ?>


    </div>
</div>



<div id="newPost" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <form role="form" method="post" action="posts.php">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">New Post</h4>
        </div>
        <div class="modal-body">
                <div class="form-group">
                    <input class="form-control disabled" type="text" placeholder="Username" name="username" value="<?php echo $_SESSION['username']; ?>">
                </div>
                <div class="form-group">
                    <label>Tweet</label>
                    <input class="form-control" type="text" placeholder="" name="tweet">
                </div>
                <div class="form-group">
                    <label>POST Priority</label>
                    <select class="form-control" name="priority">
                        <option value="1">Important</option>
                        <option value="2">High</option>
                        <option value="3">Normal</option>
                    </select>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-primary" value="Post!"/>
        </div>
    </div><!-- /.modal-content -->
    </form>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="editPost" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <form id="editForm" role="form" method="post" action="edit.php">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Post</h4>
        </div>
        <div class="modal-body">
			<input id="postid" type="hidden" name="postid">
			<div class="form-group">
				<input id="username" class="form-control disabled" type="text" placeholder="Username" name="username">
			</div>
			<div class="form-group">
				<label>Tweet</label>
				<input id="tweet" class="form-control" type="text" placeholder="" name="tweet">
			</div>
			<div class="form-group">
				<label>Priority</label>
				<select id="priority" class="form-control" name="priority">
					<option value="1">Important</option>
					<option value="2">High</option>
					<option value="3">Normal</option>
				</select>
			</div>
        </div>
		<div id="response"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-primary" name="submit" onClick="empty()" value="Submit!"/>
        </div>
    </div><!-- /.modal-content -->
    </form>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>  
 $(document).ready(function(){ 
	
	$(document).on('click', '.edit_data', function(){
		
		var postid = $(this).attr("id");
		$.get('posts.txt', { "_": $.now() }, function(data) {
            var lines = data.split("\n");
			$.each(lines, function(n,data) {
				var lines = data.split("|");
				if(postid==lines[0]) {
					$('input[name="postid"]').val(lines[0]);
					$('input[name="username"]').val(lines[1]);
					$('input[name="tweet"]').val(lines[2]);
					var selct = parseInt(lines[4]);
					$('[name=priority]').val(selct);
					// alert(lines[1]+"@"+lines[2]+"@"+lines[3]+"@"+lines[4]);
				}	
            });
		});
	});
	
	// $('#form').submit(function() {
    // if ($.trim($("#email").val()) === "" || $.trim($("#user_name").val()) === "") {
    //    alert('you did not fill out one of the fields');
    //    return false;
    // }
	// });
	
	$('#editForm').on("submit", function(event){ 
		
		e.preventDefault();
		
		var username = $('#username').val();
		var tweet = $('#tweet').val();
		var priority = $('#priority').val();

		if($('#username').val() == '')  
		{  
			alert("Username is required"); 
			return false;
		}  
		else if($('#tweet').val() == '')  
		{  
			alert("Tweet is required"); 
            return false;			
		}   
		else
		{  

			var data = $("#editForm").serialize();
			
			$.ajax({
				type: "POST",
				url: "edit.php",
				data: data,
				// data: { username : username, tweet : tweet, comment : comment, priority : priority },  // passing the values
				success: function(response) {
					console.log(response);
					$("#editPost").modal('hide');
				},
				error: function(xhr, status, error){
					var errorMessage = xhr.status + ': ' + xhr.statusText
					alert('Error - ' + errorMessage);
				}	
			});
			
		}
		
	});  
});  
</script>
</html>
