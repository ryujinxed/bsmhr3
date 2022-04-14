<?php
  $page_title = 'Admin | Leave Details ';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(2);
   
  if (!$session->isUserLoggedIn(true)) { redirect('index.php', false);}
?>

<?php include_once('layouts/header.php'); ?>
<!-- This will be the body -->
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
    <?php if($user['user_level'] === '1'): ?>
        <!-- admin menu -->
        <nav class="breadcrumbs">
        <a href="apply_leave.php" class="breadcrumbs__item">Apply Leave</a>
        <a href="leave_history.php" class="breadcrumbs__item">Leave History</a>
        <a href="leave_type.php" class="breadcrumbs__item">Leave Types</a>
        <a href="leave_report.php" class="breadcrumbs__item">Leave Report</a>
        <a href="leave_management.php" class="breadcrumbs__item is-active">Leave Management</a>
        </nav>
      
    <?php elseif($user['user_level'] === '2'): ?>
        <!-- Special menu -->
        <nav class="breadcrumbs">
        <a href="apply_leave.php" class="breadcrumbs__item">Apply Leave</a>
        <a href="leave_history.php" class="breadcrumbs__item">Leave History</a>
        <a href="leave_type.php" class="breadcrumbs__item">Leave Types</a>
        <a href="leave_report.php" class="breadcrumbs__item">Leave Report</a>
        <a href="leave_management.php" class="breadcrumbs__item is-active">Leave Management</a>
        </nav>


      <?php elseif($user['user_level'] === '3'): ?>
        <!-- User menu -->
        <nav class="breadcrumbs">
        <a href="apply_leave.php" class="breadcrumbs__item">Apply Leave</a>
        <a href="leave_history.php" class="breadcrumbs__item">Leave History</a>
        </nav>
    <?php endif;?>
  </div>
  
  <div class="col-md-12">
    <div class="card h-100">
      <div class="card-header">
         <h2>Leave Details:</h2>
              <table class="table table-bordered table-hover" style="width:100%">
            <tbody>
              <?php 

              //Query Statement for leave history===
              $conn = new mysqli('localhost', 'root', '', 'bank') or die(mysqli_error());
              $user = current_user();
              $userid = (int)$user['id'];
              //Getting an ID located in the URL...
              $lid=intval($_GET['id']);
              //===================================
              $sql  =" SELECT l.id,l.LeaveType,l.FromDate,l.ToDate,l.Description,l.PostingDate,l.AdminRemarkDate,l.AdminRemark,l.Status,l.empid,u.name,u.username";
              $sql .=" FROM tblleaves l";
              $sql .=" LEFT JOIN users u ON l.empid = u.id";
              $sql .=" WHERE l.id = '{$lid}'";
              $sql .=" ORDER BY id DESC";
              if($result = $conn->query($sql)){
              while ($row = $result -> fetch_row()) {

              ?>
              <tr>
              <td style="font-size:16px;"> <b>Name:</b></td>
                <td><?php echo remove_junk($row[10]); ?></td>
                <td style="font-size:16px;"><b>Id:</b></td>
                <td colspan="3"><?php echo remove_junk($row[9]); ?></td>
                <!-- <td style="font-size:16px;"><b>Gender :</b></td>
                <td><?php echo htmlentities($result->Gender);?></td> -->
                </tr>

                <tr>
                <td style="font-size:16px;"><b>Username:</b></td>
                <td colspan="5"><?php echo remove_junk($row[11]); ?></td>
                <!-- <td style="font-size:16px;"><b>Emp Contact No. :</b></td>
                <td><?php echo htmlentities($result->Phonenumber);?></td>-->
                </tr>

                <tr>
                <td style="font-size:16px;"><b>Leave Type :</b></td>
                <td><?php echo remove_junk($row[1]) ?></td>
                <td style="font-size:16px;"><b>Leave Date:</b></td>
                <td><b>From</b> <?php echo remove_junk($row[2]);?> <b>To</b> <?php echo remove_junk($row[3]);?></td>
                <td style="font-size:16px;"><b>Posting Date:</b></td>
                <td><?php echo remove_junk($row[5]);?></td>
                </tr>

                <tr>
                <td style="font-size:16px;"><b>Leave Description: </b></td>
                <td colspan="5"><?php echo remove_junk($row[4]);?></td>                          
                </tr>

                <tr>
                <td style="font-size:16px;"><b>Leave Status:</b></td>
                <td colspan="5"><?php $stats=$row[8];
                if($stats==1){
                ?>
                <span style="color: green">Approved</span>
                 <?php } if($stats==2)  { ?>
                <span style="color: red">Not Approved</span>
                <?php } if($stats==0)  { ?>
                 <span style="color: blue">waiting for approval</span>
                 <?php } ?>
                </td>
                </tr>

                <tr>
                <td style="font-size:16px;"><b>Admin Remark: </b></td>
                <td colspan="5"><?php
                if($row[7]==""){
                  echo "waiting for Approval";  
                }
                else{
                echo remove_junk($row[7]);
                }
                ?></td>
                 </tr>

                 <tr>
                  <td style="font-size:16px;"><b>Admin Action taken date : </b></td>
                  <td colspan="5"><?php
                  if($row[6]==""){
                    echo "NA";  
                  }
                  else{
                  echo remove_junk($row[6]);
                  }
                  ?></td>
                   </tr>
                   <!-- //Condition: If Admin taken action or not... -->
                   <?php 
                    if($stats==0)
                    {

                    ?>
                    <tr>
                     <td colspan="5">
                      <a href="leave_approval_form.php?id=<?php echo remove_junk($row[0]);?>" class="btn" style="margin-bottom:10px; background-color:steelblue; color: whitesmoke;"> Take Action</a>

                      </td>
                    </tr>
                    <?php } ?>
                    <!-- // -->
              <?php } }?>
          </tbody>
        </table>
      </div>
      </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>