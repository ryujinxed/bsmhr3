<?php
	require_once('includes/load.php');
	// // Functions for Leaves JOIN Table
	// $leaves = join_leaves_table();
		$count_id = count_id();
	//include connection file
	Class dbObj{
		/* Database connection start */
		var $dbhost = "localhost";
		var $username = "root";
		var $password = "";
		var $dbname = "bank";
		var $conn;
		function getConnstring() {
			$con = mysqli_connect($this->dbhost, $this->username, $this->password, $this->dbname) or die("Connection failed: " . mysqli_connect_error());
			
			/* check connection */
			if (mysqli_connect_errno()) {
				printf("Connect failed: %s\n", mysqli_connect_error());
				exit();
				} else {
				$this->conn = $con;
			}
			return $this->conn;
		}
	}
	include_once('libs/fpdf/fpdf.php');
	class PDF extends FPDF
	{
		// Page header
		function Header()
		{
			// Logo
			#$this->Image('logo.png',10,-1,70);
			$this->SetFont('Arial','B',13);
			// Move to the right
			$this->Cell(50);
			// Title
			$this->Cell(80,10,'Accepted Leaves',1,0,'C');
			// Line break
			$this->Ln(20);
		}
		
		// Page footer
		function Footer()
		{
			$user = current_user();
			$username = $user['username'];
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			// Arial italic 8
			$this->SetFont('Arial','I',8);
			// Page number
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb} Generated by '.$username,0,0,'C');
		}
	}
	$user = current_user();
	$username = $user['username'];
	$name = $user['name'];
	$user_level = $user['user_level']; 
	
	$db = new dbObj();
	$connString =  $db->getConnstring();
	
	$header = mysqli_query($connString, "SHOW columns FROM tblleaves");
	
	if(isset($_POST['generate'])) {
		$fromdate = $_POST['fromdate'];
		$todate = $_POST['todate'];
		

		if ($user_level == 1){
			$result = mysqli_query($connString, "SELECT emp_name, LeaveType, amount_of_days, AdminRemarkDate FROM tblleaves WHERE PostingDate >= '$fromdate' 
		AND PostingDate <= '$todate' AND status = 1 ORDER BY PostingDate ASC");
				} else {
					$result = mysqli_query($connString, "SELECT emp_name, LeaveType, amount_of_days, AdminRemarkDate FROM tblleaves WHERE PostingDate >= '$fromdate' 
		AND PostingDate <= '$todate' AND username = '$username' AND status = 1 ORDER BY PostingDate ASC");		
				}
// if ($user_level == 1){

// 	$sql  =" SELECT l.id,l.LeaveType,l.FromDate,l.ToDate,l.Description,l.PostingDate,l.AdminRemarkDate,l.AdminRemark,l.Status,l.empid,l.amount_of_days,l.remaining_days,u.name,u.username,u.status";
// 	$sql .=" FROM tblleaves l";
// 	$sql .=" LEFT JOIN users u ON l.empid = u.id";
// 	$sql .=" WHERE l.PostingDate >= '$fromdate' AND l.PostingDate <= '$todate' AND u.status = 1 ";
// 	$sql .=" ORDER BY l.id DESC";
// 	}else{
// 	  $sql  =" SELECT l.id,l.LeaveType,l.FromDate,l.ToDate,l.Description,l.PostingDate,l.AdminRemarkDate,l.AdminRemark,l.Status,l.empid,l.amount_of_days,l.remaining_days,u.name";
// 	  $sql .=" FROM tblleaves l";
// 	  $sql .=" LEFT JOIN users u ON l.empid = u.id";
// 	  $sql .=" WHERE l.PostingDate >= '$fromdate' AND l.PostingDate <= '$todate' AND u.username = '$username'  AND u.status = 1 ";
// 	  $sql .=" ORDER BY l.id DESC";
// 	}
// 	if($result = mysqli_query($connString,$sql)){
	// while ($row = $result -> fetch_row()) {
	
		$pdf = new PDF();
		$pdf->SetLeftMargin(14.5);
		//header
		$pdf->AddPage();
		//foter page
		$pdf->AliasNbPages();
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(40,10,'Employee Name',1,);
		$pdf->Cell(40,10,'Types of Leave',1,);
		$pdf->Cell(40,10,'No. of Days',1,);
		$pdf->Cell(40,10,'Remarks Date',1,);

		$bruh = array(35, 35, 35, 20, 50);
		
		foreach($header as $heading) {
		}
		foreach($result as $rows) {
			$pdf->Ln();
					$plus = "0";
			foreach($rows as $row) {
				// $pdf->Cell($bruh[$plus],10,$row,1,);
				// $plus+=1;
				$pdf->Cell(40,8,$row,1,);

				 // $pdf->Cell(10,8,$count_id,1,);
				 // $pdf->Cell(40,8,$name,1,);
				 // $pdf->Cell(40,8,$row[0],1,);
				 // $fdate = date("m-j-Y", strtotime($row[1]));
				 // $tdate = date("m-j-Y", strtotime($row[2]));
				 // $pdf->Cell(43,8,$fdate." - ".$tdate,1,);
				 // if($row[3]<="1"){
				 // $pdf->Cell(20,8,"   ".$row[3]." Day",1,);	 
				 // }else{
				 // $pdf->Cell(20,8,"   ".$row[3]." Days",1,);
				 // }
				 // $AdminRemarkDate = date("F j, Y-g:i a", strtotime($row[4]));
				 // $pdf->Cell(40,8,$AdminRemarkDate,1,);
			}
		}
		$pdf->Output();
}
?>
