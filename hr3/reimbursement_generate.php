<?php
	require_once('includes/load.php');
	//include connection file
	Class dbObj{
		/* Database connection start */
		var $dbhost = "localhost";
		var $username = "root";
		var $password = "";
		var $dbname = "db";
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
			$this->Cell(80,10,'Accepted Claims List',1,0,'C');
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
	
	$header = mysqli_query($connString, "SHOW columns FROM time_attendance");
	
	if(isset($_POST['generate'])) {
		$fromdate = $_POST['fromdate'];
		$todate = $_POST['todate'];
		$user_selected = $_POST['user_selected'];		
		
		if ($user_level <= 2){
			if ($user_selected == 'All users'){
				$result = mysqli_query($connString, "SELECT name, reimbursement, reimbursement_date, amount, status FROM reimbursements WHERE reimbursement_date >= '$fromdate' 
				AND reimbursement_date <= '$todate' AND accepted != 0 ORDER BY reimbursement_id DESC");	
				
				} else {
				$result = mysqli_query($connString, "SELECT name, reimbursement, reimbursement_date, amount, status FROM reimbursements WHERE reimbursement_date >= '$fromdate' 
				AND reimbursement_date <= '$todate' AND name = '$user_selected' AND accepted != 0 ORDER BY reimbursement_id DESC");	
			}
		} 
		elseif ($user_level > 2) {
			$result = mysqli_query($connString, "SELECT name, reimbursement, reimbursement_date, amount, status FROM reimbursements WHERE reimbursement_date >= '$fromdate' 
			AND reimbursement_date <= '$todate' AND username = '$username' AND accepted != 0 ORDER BY reimbursement_id DESC");	
		}
		
		$pdf = new PDF();
		$pdf->SetLeftMargin(14.5);
		//header
		$pdf->AddPage();
		//foter page
		$pdf->AliasNbPages();
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(35,10,'Name',1,);
		$pdf->Cell(35,10,'Reimbursement',1,);
		$pdf->Cell(35,10,'Reimbursement Date',1,);
		$pdf->Cell(35,10,'Amount',1,);
		$pdf->Cell(35,10,'Status',1,);
		
		$bruh = array(35, 35, 35, 20, 50);
		
		foreach($header as $heading) {
		}
		foreach($result as $row) {
			$pdf->Ln();
			$plus = "0";
			foreach($row as $column) {
				$pdf->Cell($bruh[$plus],10,$column,1,);
				$plus+=1;
			}
		}
		$pdf->Output();
	}
?>
