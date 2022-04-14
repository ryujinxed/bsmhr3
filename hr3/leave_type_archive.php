<?php
	$page_title = 'Leave Type Archive';
	require_once('includes/load.php');
	// Checkin What level user has permission to view this page
	page_require_level(1);
	
	$user = current_user();
	$user_level = $user['user_level'];
	if ($user_level <= 1){
		#	header("Location:claim_index_admin.php");
	}
	?><?php
	require_once("includes/load.php");
	if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
	
	$claim;$currency;$amount;
	$conn = new mysqli('localhost', 'root', '', 'bank') or die(mysqli_error());       
	$user = current_user();
	$username = $user['username'];
	$user_id = $user['id'];
	$user_level = $user['user_level'];
	$fullname = $user['name'];
	
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
					
					<li role="presentation"><a href="claim_index.php" class="btn" style="margin-bottom:10px">Request Claims</a></li>
					<li role="presentation"><a href="claim_type.php" class="btn" style="margin-bottom:10px">Types of Claims</a></li>
					<li role="presentation" class="active"><a href="claim_history.php" class="btn" style="margin-bottom:10px">Claims History</a></li>
				</ul>
				
				<?php else: ?>
				<ul class="nav nav-pills">
					
					<li role="presentation"><a href="claim_index.php" class="btn" style="margin-bottom:10px">Request Claims</a></li>
					<li role="presentation" class="active"><a href="claim_history.php" class="btn" style="margin-bottom:10px">Claims History</a></li>
				</ul>		
				<?php endif;?> -->
				
				<div class="col-md-12">
					<div class="panel">
						<div class="jumbotron text-center">
							<button name="cancel" class="btn btn-primary" onclick="location.href='leave_type.php'">Back</button>
							
							<h3>Deleted Leave Type Logs</h3>
							<div class="text-center">
								<table class="table" style="table-layout: fixed">
									<tr>
										<th>Leave Type</th>
										<th>Description</th>
										<th>Deletion Date</th>
										<th>Options</th>
									</tr>
								</table>
								<div style="max-height:300px; overflow:auto;">
									<table class="table" style="table-layout: fixed">
										<?php
											$query = $conn->query("SELECT * FROM tblleavetype_archive ORDER BY id DESC");
											
											while($user_data = mysqli_fetch_array($query)) {
												echo "<tr>";
												echo "<td>".$user_data['LeaveType']."</td>";
												echo "<td>".$user_data['Description']."</td>";
												echo "<td>".$user_data['DeletionDate']."</td>";
												
												echo "<td><a href='leave_type_retrieve_archive.php?leavetype_id=$user_data[id]'>Retrieve | </a><a href='leave_type_delete_archive.php?leavetype_id=$user_data[id]'>Delete</a></td>";
												echo "</tr>";
											}?>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div><?php include_once('layouts/footer.php'); ?>
		</body>
	</html>
