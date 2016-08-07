<?php

	include("../Setting/Configifx.php");
	include("../Setting/Connection.php");
	$var_loginid = $_SESSION['sid'];
    
    if($var_loginid == "") { 
      echo "<script>";   
      echo "alert('Not Log In to the system');"; 
      echo "</script>"; 

      echo "<script>";
      echo 'top.location.href = "../index.php"';
      echo "</script>";
    } else {
    
      $var_stat = $_GET['stat'];
      $var_menucode = $_GET['menucd'];
      include("../Setting/ChqAuth.php");
    }
   
 
	 if ($_POST['Submit'] == "Delete") {
     	if(!empty($_POST['procd']) && is_array($_POST['procd'])) 
     	{
           foreach($_POST['procd'] as $key) {
             $defarr = explode(",", $key);
             
             //print_r($defarr);
             $var_sale = $defarr[0];
             //$var_cust = $defarr[2];
                        
		         $vartoday = date("Y-m-d H:i:s");
			       $sql  = "Update invttrf Set stat = 'C', upd_by = '$var_loginid', upd_on = '$vartoday' ";
             $sql .=	" Where trf_id ='".$var_sale."'";
             //echo $sql;
             mysql_query($sql) or die(mysql_error()." 1");	
             
             $sql = " delete from invthist ";
             $sql .= " where refid = '".$var_sale."'";
             
             mysql_query($sql) or die ("Delete fail : ".mysql_error());       
             

		   }
		   $backloc = "../invt/m_unpost_do.php?stat=1&menucd=".$var_menucode;
           echo "<script>";
           echo 'location.replace("'.$backloc.'")';
           echo "</script>";   
       }      
    }
    
	if (isset($_POST['Submit'])){ 
     if ($_POST['Submit'] == "Print") {
     
     	 $backloc = "../invt/inq_raw_matall.php?menucd=".$var_menucode."&t=A";
         echo "<script>";
         echo 'location.replace("'.$backloc.'")';
         echo "</script>";   
     }
    }     
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">

<style media="all" type="text/css">
@import "../css/multitable/themes/smoothness/jquery-ui-1.8.4.custom.css";
@import "../css/styles.css";
@import "../css/demo_table.css";
thead th input { width: 90% }

.style2 {
	margin-right: 0px;
}
</style>

<script type="text/javascript" language="javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" language="javascript" src="../js/jquery-ui.min.js"></script>
<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.nightly.js"></script>
<script type="text/javascript" src="../media/js/jquery.dataTables.columnFilter.js"></script>

<script type="text/javascript"> 
$(document).ready(function() {
	$('#example').dataTable( {
		"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50,"All"]],
		"bStateSave": true,
		"bFilter": true,
		"sPaginationType": "full_numbers",
		"bAutoWidth":false,
		"aoColumns": [
    					null,
    					null,
    					null, 
    					null,
    				
    					null
    				]

	})
	
	.columnFilter({sPlaceHolder: "head:after",

		aoColumns: [ 
					 null,	
					 { type: "text" },
					 { type: "text" },
					 { type: "text" },
									     null
				   ]
		});	
} );
			
jQuery(function($) {
  
    $("tr :checkbox").live("click", function() {
        $(this).closest("tr").css("background-color", this.checked ? "#FFCC33" : "");
    });
  
});
			
</script>
</head>
  <?php include("../topbarm.php"); ?> 
  <!--<?php include("../sidebarm.php"); ?> -->
<body>
  
  <div class="contentc">


	<fieldset name="Group1" style=" width: 900px;" class="style2">
	 <legend class="title">UNPOST D/O LISTING</legend>
	  <br>
	 
        <form name="LstCatMas" method="POST" action="<?php echo $_SERVER['PHP_SELF'].'?menucd='.$var_menucode;; ?>">
		 <table>
		 <tr>
		  
           <td style="width: 1131px; height: 38px;" align="left">
           <?php
                $locatr = "unpost_do.php?menucd=".$var_menucode;
                if ($var_accadd == 0){
   					echo '<input disabled="disabled" type=button name = "Submit" value="Create" class="butsub" style="width: 60px; height: 32px">';
  				}else{
   					echo '<input type="button" value="Create" class="butsub" style="width: 60px; height: 32px" onclick="location.href=\''.$locatr.'\'" >';
  				}
  				//include("../Setting/btnprint.php");
          
    	  	   //$msgdel = "Are You Sure Delete Stock Transfer List?";
    	  	   //include("../Setting/btndelete.php");
    	      ?></td>
		 </tr>
		 </table>
		 <br>
		 <table cellpadding="0" cellspacing="0" id="example" class="display" width="100%">
         <thead>
           <tr>
          <th></th>
          <th>Document No.</th>
          <th>Delivery Number</th>
          <th>User</th>
		  <th>Transaction Date</th>
	

         </tr>

         <tr>
          <th class="tabheader" style="width: 12px">#</th>
          <th class="tabheader" style="width: 139px">Document No.</th>
          <th class="tabheader" style="width: 106px">Delivery Number</th>
          <th class="tabheader" style="width: 106px">User</th>
          <th class="tabheader" style="width: 106px">Transaction Date</th>

         </tr>
         </thead>
		 <tbody>
		 <?php 
		    $sql = "SELECT * ";
		    $sql .= " FROM unpos_do_det ";
    		$sql .= " ORDER BY doc_no";   
			$rs_result = mysql_query($sql); 

		    $numi = 1;
			while ($rowq = mysql_fetch_assoc($rs_result)) { 
			
				$doc_no = htmlentities($rowq['doc_no']);
				$var_login = htmlentities($rowq['var_login']);
				$rmk = htmlentities($rowq['rmk']);
				$donum = htmlentities($rowq['donum']);


				$trx_date = date('d-m-Y H:i:s', strtotime($rowq['trx_date']));
				$urlpop = 'upd_invttrf.php';
				$urlvie = 'vm_invttrf.php';
				echo '<tr bgcolor='.$defaultcolor.'>';
				
            	echo '<td>'.$numi.'</td>';
           		echo '<td>'.$doc_no.'</td>';
           		echo '<td>'.$donum.'</td>';
           		echo '<td>'.$var_login.'</td>';
            	echo '<td>'.$trx_date.'</td>';
           		 echo '</tr>';
            $numi = $numi + 1;
			}
		 ?>
		 </tbody>
		 </table>
		</form>
	   </fieldset>
	  </div>	
	  <div class="spacer"></div>
	
</body>

</html>
