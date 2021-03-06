<?php
	$page_title = 'Timesheet Management';
	require_once('includes/load.php');
	// Checkin What level user has permission to view this page
	page_require_level(3);
?>

<?php
	require_once("includes/load.php");
	if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
	
	$user = current_user();
	$user_level = $user['user_level'];
	$user_id = $user['id'];
	
	$conn = new mysqli('localhost', 'root', '', 'bank') or die(mysqli_error());
	$query = $conn->query("SELECT complaint_notif FROM users WHERE id='$user_id'");
	while($user_data = mysqli_fetch_array($query)) {
		$complaint_notif = $user_data['complaint_notif'];
	}
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
	<div class="col-md-12">
		<?php echo display_msg($msg); ?>
		<nav class="breadcrumbs">
			<a href="time_index.php" class="breadcrumbs__item">Time and Attendance <?php if(!$complaint_notif==0){ ?><span class="badge" style="background-color: red;"><?php echo (int)$complaint_notif; ?></span><?php } ?></a>
			<a href="timesheet_index.php" class="breadcrumbs__item is-active">Timesheet Management</a>
		</nav>
	</div>
	<div class="col-md-12">
		<div class="card h-100">
			<div class="card-header">
				<div class="row">
					<div class="col-md-9">
						<h2>Timesheet Management</h2>
						<p>Browsing current clock in and clock out logs of users</p>
						<?php if ($user_level == 1): ?>
						<a href="time_archive.php" class="btn btn-danger">TRASH <span class="bi bi-trash3-fill"></span></a>
						<?php endif;?>
					</div>
					<div class="col-md-3" style="text-align:right">
						<div class="d-grid gap-2 d-md-flex justify-content-md-end">
							<a href="time_generate_index.php" class="btn btn-danger">PDF <span class="bi bi-filetype-pdf"></a>
								<a href="time_generate_index_excel.php" class="btn btn-success">EXCEL<span class="bi bi-filetype-xls"></a>
								</div></br>
							</div>
						</div>
					</div>
					
					<div class="card-body">
						<div style="max-height:600px">
							<table id="example" class="table table-bordered data-table" style="width:100%">
								<thead>
									<tr>
										<th>User</th> <th>Login Time</th> <th>Logout Time</th> <th>Hours Worked</th> <th>Options</th>
									</tr>
								</thead>
								<?php
									$user = current_user();
									$username = $user['username'];
									$name = $user['name'];
									$user_level = $user['user_level'];
									$conn = new mysqli('localhost', 'root', '', 'bank');
									
									if ($user_level <= 1){
										$query = $conn->query("SELECT * FROM time_attendance ORDER BY time_id DESC");
									}
									else if ($user_level <= 2){
										$query = $conn->query("SELECT * FROM time_attendance WHERE user_level >= 2 ORDER BY time_id DESC");
									}
									else {
										$query = $conn->query("SELECT * FROM time_attendance WHERE username = '$username' ORDER BY time_id DESC");
									}
									
									while($user_data = mysqli_fetch_array($query)) {
										echo "<tr>";
										echo "<td style='font-size: 14px'>".$user_data['name']."</td>";
										echo "<td style='font-size: 14px'>".$user_data['login_time']."</td>";
										
										$log = $user_data['working'];
										if ($log == 1){
											echo "<td style='font-size: 14px'>Currently Working</td>";
											echo "<td style='font-size: 14px'>Currently Working</td>";
											} else {
											echo "<td style='font-size: 14px'>".$user_data['logout_time']."</td>";
											echo "<td style='font-size: 14px'>".$user_data['calculated_work']."</td>";
										}
										
										if ($user_level <= 1){
											echo "<td style='font-size: 14px'><a href='time_edit.php?time_id=$user_data[time_id]' class='btn btn-xs btn-warning'data-toggle='tooltip' title='Edit'>
											<i class='bi bi-pencil-fill'></i></a>  <a href='time_delete.php?time_id=$user_data[time_id]' class='btn btn-xs btn-danger'data-toggle='tooltip' title='Remove'>
											<i class='bi bi-eraser-fill'></i></a></td>";
											} else {
											echo "<td style='font-size: 14px'><a href='time_delete.php?time_id=$user_data[time_id]' class='btn btn-xs btn-danger'data-toggle='tooltip' title='Remove'>
											<i class='bi bi-eraser-fill'></i></a>";
										}	
										echo "</tr>";
									}?>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php include_once('layouts/footer.php'); ?>																																