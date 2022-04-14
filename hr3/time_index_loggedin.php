<?php
	$page_title = 'Time and Attendance';
	require_once('includes/load.php');
	// Checkin What level user has permission to view this page
	page_require_level(3);
	
	$conn = new mysqli('localhost', 'root', '', 'bank') or die(mysqli_error());	
	$user = current_user();
	$username = $user['username'];
	$name = $user['name'];
	$user_id = $user['id'];
	$user_level = $user['user_level'];
?>

<?php
	require_once("includes/load.php");
	if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
	
	$query1 = $conn->query("SELECT working FROM `time_attendance` WHERE `username` = '$username'") or die(mysqli_error());
	while($user_data = mysqli_fetch_array($query1)){
		$working = $user_data['working'];
	}
	if ($working == 0){
		header("Location:time_index.php");  
	} 
?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
	<div class="col-md-12">
		<?php echo display_msg($msg); ?>
		<nav class="breadcrumbs">
			<a href="timesheet_index.php" class="breadcrumbs__item">Timesheet Management</a>
			<a href="time_index.php" class="breadcrumbs__item is-active">Time and Attendance</a>
		</nav>
	</div>
	<div class="col-md-12">
		<div class="panel">
			<div class="jumbotron text-center">
				<?php
					$query = $conn->query("SELECT complaint_notif FROM users WHERE id='$user_id'");
					while($user_data = mysqli_fetch_array($query)) {
						$complaint_notif = $user_data['complaint_notif'];
					}
				?>
				<h2>Time and Attendance</h2>
				<p>Current Time <?php echo date("F j, Y, g:i a");?></p></br>
				<form action="time_index_loggedin.php" method="POST">
					<input type="submit" name="logout" value="End your current shift" class="btn" style="color: red; font-size:20px">
				</form>
				<?php if ($user_level >= 1): ?>
				<form class="form-inline" method="post" action="complaint_index.php">
					<button type="submit" id="pdf" name="generate_pdf" class="btn" style="font-size:20px">Support <?php if(!$complaint_notif==0){ ?><span class="badge" style="background-color: red;"><?php echo (int)$complaint_notif; ?></span><?php } ?></button>
				</form>
				<?php endif;?>
			</div>
		<?php			
			if(isset($_POST['login'])) {
				$session->msg('s',"Shift has started");
				$login_time = date("Y-m-d H:i:s", strtotime("+0 HOURS"));
				
				$q_student = $conn->query("SELECT * FROM `users` WHERE `username` = '$username'") or die(mysqli_error());
				$f_student = $q_student->fetch_array();
				$conn->query("INSERT INTO `time_attendance` VALUES('', '$user_id', '$login_time', '', '$username', '$name', '$user_level', '', '1')") or die(mysqli_error());
			}
			
			if(isset($_POST['logout'])) {
				$session->msg('d',"Shift has ended");
				$logout_time = date("Y-m-d H:i:s", strtotime("+0 HOURS"));
				
				$query = $conn->query("SELECT MAX(time_id) FROM time_attendance");
				$row = $query->fetch_array();
				$last = $row[0];
				
				$q_student = $conn->query("SELECT * FROM `users` WHERE `username` = '$username'") or die(mysqli_error());
				$f_student = $q_student->fetch_array();
				$conn->query("UPDATE time_attendance SET logout_time='$logout_time' WHERE time_id='$last'") or die(mysqli_error());
				
				$get_login_time = $conn->query("SELECT login_time FROM time_attendance WHERE time_id='$last'");
				$fetch = mysqli_fetch_array($get_login_time);
				$login_time = $fetch['login_time'];		
				
				$time1 = strtotime($login_time);
				$time2 = strtotime($logout_time);
				$result1 = round(abs($time1 - $time2) / 3600,2);
				$result2 = round(abs($time1 - $time2) / 60,2);
				
				$result = "$result1 Hours ($result2 Minutes)";
				$conn->query("UPDATE time_attendance SET calculated_work='$result' WHERE time_id='$last'");
				$conn->query("UPDATE time_attendance SET working='0' WHERE time_id='$last'");  
				echo "<script>window.location.href='time_index.php';</script>";
			}
		?>
		<div class="row" style="margin:50px">
				<div class="col-md-7" style="max-height:500px; overflow:auto;">
					<h2 style="text-align:left">Ongoing Events</h2>
					<?php
						$today = date("Y-m-d", strtotime("+0 HOURS"));
						$query1 = $conn->query("SELECT * FROM events WHERE fromdate <= '$today' AND todate >= '$today' AND min_user_level >= '$user_level'");
						
						while($user_data = mysqli_fetch_array($query1)) {
							$getfromdate = $user_data['fromdate'];
							$gettodate = $user_data['todate'];
							$getauthor = $user_data['author'];
							$getevent = $user_data['event'];
							
							echo "<p>".$getevent." from ".$getfromdate." to ".$gettodate." noted by ".$getauthor."</p>";
							} if (mysqli_num_rows($query1)==0) { 
							echo "No events for today";
						}
						
						echo "</br><h2 style='text-align:left'>Upcoming Events</h2>";
						$query1 = $conn->query("SELECT * FROM events WHERE fromdate > '$today' AND min_user_level >= '$user_level'");
						
						while($user_data = mysqli_fetch_array($query1)) {
							$getfromdate = $user_data['fromdate'];
							$gettodate = $user_data['todate'];
							$getauthor = $user_data['author'];
							$getevent = $user_data['event'];
							
							echo "<p>".$getevent." from ".$getfromdate." to ".$gettodate." noted by ".$getauthor."</p>";
							} if (mysqli_num_rows($query1)==0) { 
							echo "No upcoming events";
						}
					?> 
				</div>
				
				<?php if($user['user_level'] <= '2'): ?>
				<div class="col-md-5" style="margin-bottom: 50px">
				<div class="card h-100">
					<?php
						if(isset($_POST['add_event'])){
							
							$event = $_POST['event'];
							$fromdate = $_POST['fromdate'];
							$todate = $_POST['todate'];
							$min_user_level = $_POST['min_user_level'];
							$author = $name;
							
							$query = $conn->query("SELECT * FROM user_groups WHERE group_name = '$min_user_level'");
							while($user_data = mysqli_fetch_array($query)) {
								$group_level = $user_data['group_level']; 
							}
							
							$conn->query("INSERT INTO `events` VALUES('', '$event', '$fromdate', '$todate', '$group_level', '$author')") or die(mysqli_error());
							
							$session->msg('s',"Event Successfully Created");
							echo "<script>window.location.href='time_index.php';</script>";
						}
					?>
					<form method="post" action="time_index.php" enctype="multipart/form-data">
						<div class="card-header">
						<h6>Create a New Event</h6>
						</div>
						<div class="card-body">
						<div class="form-group">
							<label>Description</label>
							<input required type="text" class="form-control" name="event" placeholder="" />
						</div></br>
						<div class="form-group">
							<label>Beginning Date</label>
							<input type="date" class="form-control" name="fromdate" value="<?php echo date('Y-m-d'); ?>" />
						</div></br>
						<div class="form-group">
							<label>Ending Date</label>
							<input type="date" class="form-control" name="todate" value="<?php echo date('Y-m-d'); ?>" />
						</div></br>
						<div class="form-group">
							<label>Minimum User Level</label>
							<select class="form-control" name="min_user_level" placeholder="Show events for: "><?php
								$query = $conn->query("SELECT group_name FROM user_groups");
								while($row = mysqli_fetch_array($query)){
									echo '<option>'.$row['group_name'].'</option>';
								}
							?>
							</select>
						</div></br>
						<button type="submit" name="add_event" class="btn btn-primary" value="add_event">Create Event</button>
					</form>
					</div>
					</div>
				</div>
				<table id="datatablesSimple" class="table table-striped data-table" style="width:100%">
					<thead>
						<tr>
							<th>Description</th> <th>From</th> <th>To</th> <th>Created by</th> <th>Options</th>
						</tr>
					</thead>
					<?php
						if ($user_level == 1){
							$query = $conn->query("SELECT * FROM events ORDER BY event_id DESC");
							} else {
							$query = $conn->query("SELECT * FROM events WHERE min_user_level >= 2 ORDER BY event_id DESC");
						}
						
						while($user_data = mysqli_fetch_array($query)) {
							echo "<tr>";
							echo "<td>".$user_data['event']."</td>";
							echo "<td>".$user_data['fromdate']."</td>";
							echo "<td>".$user_data['todate']."</td>";
							echo "<td>".$user_data['author']."</td>";
							echo "<td><a href='event_edit.php?event_id=$user_data[event_id]'>Edit</a> | <a href='event_delete.php?event_id=$user_data[event_id]'>Delete</a></td>";
							echo "</tr>";
						}	
					?>
				</table>
				<?php endif;?>
			</div>
	</div>
</div>
</div>

<?php include('layouts/table/tablefooter.php');?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="dist/js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
<script src="dist/js/datatables-simple-demo.js"></script>

<?php include_once('layouts/footer.php'); ?>				