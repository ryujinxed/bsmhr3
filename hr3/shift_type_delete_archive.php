<?php
	$page_title = 'Shift Type Delete Prompt';
	require_once('includes/load.php');
	// Checkin What level user has permission to view this page
	page_require_level(1);
	
	require_once("includes/load.php");
	if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
	
	$user = current_user();
	$user_level = $user['user_level'];
	$conn = new mysqli('localhost', 'root', '', 'bank') or die(mysqli_error());       
	$shift_type_id = $_GET['shifttype_id'];
	
?><?php include_once('layouts/header.php'); ?>
<html>
	<head>
		<meta name="generator"
		content="HTML Tidy for HTML5 (experimental) for Windows https://github.com/w3c/tidy-html5/tree/c63cc39" />
		<title></title>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12"><?php echo display_msg($msg); ?>
				<!-- <?php if ($user_level <= '1'): ?>
				<ul class="nav nav-pills">
					
					<li role="presentation"><a href="claim_index.php" class="btn" style="margin-bottom:10px">Appoint Claims</a></li>
					<li role="presentation"><a href="claim_type.php" class="btn" style="margin-bottom:10px">Types of Claims</a></li>
					<li role="presentation" class="active"><a href="claim_history.php" class="btn" style="margin-bottom:10px">Claims History</a></li>
				</ul>
				
				<?php else: ?>
				<ul class="nav nav-pills">
					
					<li role="presentation"><a href="claim_index.php" class="btn" style="margin-bottom:10px">Appoint Claims</a></li>
					<li role="presentation" class="active"><a href="claim_history.php" class="btn" style="margin-bottom:10px">Claims History</a></li>
				</ul>		
				<?php endif;?> -->
				
				<div class="col-md-12">
					<div class="panel">
						<div class="jumbotron text-center">
							<?php
								if(isset($_POST['yes'])) {
									$query = $conn->query("DELETE FROM tblshifttype_archive WHERE id=$shift_type_id");
									$session->msg('s',"Successfully Deleted");
									
									#header("Location:timesheet_index.php");
									echo "<script>window.location.href='shift_type_archive.php';</script>";
								}
							?>
							<h2>Are you sure you want to delete?</h2>	
							<form method="post" action="">
							</br>
								<button type="submit" name="yes" class="btn btn-primary" value="yes">Yes</button>
							</form></br>
							<button name="cancel" class="btn" onclick="location.href='shift_type_archive.php'">Cancel</button>
						</div>
					</div>
				</div>
			</div><?php include_once('layouts/footer.php'); ?>
		</body>
	</html>
