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
   
 
	 if ($_POST['Submit'] == "Cancel") {
     	if(!empty($_POST['salorno']) && is_array($_POST['salorno'])) 
     	{
           foreach($_POST['salorno'] as $key) {
             $defarr = explode(",", $key);
             print_r($defarr);
             $var_sale = $defarr[0];
             $var_cust = $defarr[2];
                        
		         $vartoday = date("Y-m-d H:i:s");
			       $sql  = "Update csalesmas Set stat = 'C', modified_by = '$var_loginid', modified_on = '$vartoday' ";
             $sql .=	" Where sordno ='".$var_sale."' And scustcd='".$var_cust."'";
             echo $sql;
             mysql_query($sql) or die(mysql_error()." 1");		 	 
		   }
		   $backloc = "../cons/m_csales_mas.php?stat=1&menucd=".$var_menucode;
           echo "<script>";
           echo 'location.replace("'.$backloc.'")';
           echo "</script>";   
       }      
    }
        
   if ($_POST['Submit'] == "Active") {
     	if(!empty($_POST['salorno']) && is_array($_POST['salorno'])) 
     	{
           foreach($_POST['salorno'] as $key) {
             $defarr = explode(",", $key);
             $var_sale = $defarr[0];
             $var_cust = $defarr[2];
                        
		         $vartoday = date("Y-m-d H:i:s");
			       $sql  = "Update csalesmas Set stat = 'A', modified_by = '$var_loginid', modified_on = '$vartoday' ";
             $sql .=	" Where sordno ='".$var_sale."' And scustcd='".$var_cust."'";
             
             mysql_query($sql) or die(mysql_error()." 1");		 	 
		   }
		   $backloc = "../cons/m_csales_mas.php?stat=1&menucd=".$var_menucode;
           echo "<script>";
           echo 'location.replace("'.$backloc.'")';
           echo "</script>";   
       }      
    }
    
 //--- to add in year for them to select data faster - 02/05/2016   
   $var_fyear = "";   $var_tyear = "";
   if ($_POST['Submit'] == "GetData") {
       $var_fyear = $_POST['fyear'];
       $var_tyear = $_POST['tyear'];
   }
   
   if ($var_fyear == "") { $var_fyear = date(Y); }
   if ($var_tyear == "") { $var_tyear = date(Y); }    

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
		"bStateSave": false,
		"bFilter": true,
		"sPaginationType": "full_numbers",
		"bAutoWidth":false,
		"aoColumns": [
    					null,
    					null,
    					{ "sType": "uk_date" },
    					{ "sType": "uk_date" },
    					null,
    					null,
    					null,
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
				     { type: "text" },
				     { type: "text" },
				     { type: "text" },
				     { type: "text" },
				     null,
				     null,
				     null,
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
  <!--<?php include("../sidebarm.php"); ?>--> 

<body>
  <div class="contentc">


	<fieldset name="Group1" style=" width: 900px;" class="style2">
	 <legend class="title">COUNTER SALES LISTING</legend>
	  <br>
	 
        <form name="LstCatMas" method="POST" action="<?php echo $_SERVER['PHP_SELF'].'?menucd='.$var_menucode;; ?>">
		 <table>
		 <tr>
		  
           <td style="width: 1131px; height: 38px;" align="left">
           <?php
                $locatr = "csales_mas.php?menucd=".$var_menucode;
                if ($var_accadd == 0){
   					echo '<input disabled="disabled" type=button name = "Submit" value="Create" class="butsub" style="width: 60px; height: 32px">';
  				}else{
   					echo '<input type="button" value="Create" class="butsub" style="width: 60px; height: 32px" onclick="location.href=\''.$locatr.'\'" >';
  				}
  				
    	  	   $msgdel = "Are You Sure Delete Selected Sales Entry?";
    	  	   if ($var_accdel == 0){
   					echo '<input disabled="disabled" type=button name = "Submit" value="Cancel" class="butsub" style="width: 60px; height: 32px">';
  				}else{
   					echo '<input type=submit name = "Submit" value="Cancel" class="butsub" style="width: 60px; height: 32px" onclick="return confirm(\''.$msgdel.'\')">';
  				}
  				
			   $msgdel = "Are You Sure Active Selected Sales Entry?";
    	  	   if ($var_accdel == 0){
   					echo '<input disabled="disabled" type=button name = "Submit" value="Active" class="butsub" style="width: 60px; height: 32px">';
  				}else{
   					echo '<input type=submit name = "Submit" value="Active" class="butsub" style="width: 60px; height: 32px" onclick="return confirm(\''.$msgdel.'\')">';
  				}

    	      ?></td>
       <td>From Year : <input type=text name="fyear" value="<?php echo $var_fyear; ?>" size="8"></td>
       <td>To Year : <input type=text name="tyear" value="<?php echo $var_tyear; ?>" size="8"></td>     
       <td><input type=submit name = "Submit" value="GetData" class="butsub" style="width: 100px; height: 32px"></td>            
		 </tr>
		 </table>
		 <br>
		 <table cellpadding="0" cellspacing="0" id="example" class="display" width="100%">
         <thead>      
          <tr>
          <th></th>
          <th style="width: 234px">Sales No</th>
          <th style="width: 129px">Counter</th>
          <th style="width: 128px">MM/YY</th>
          <th style="width: 124px">Period</th>
          <th>Invoice</th>
          <th>GRN</th>
          <th>Status</th>
          <th></th>
          <th></th>
		  <th></th>
		  <th></th>
         </tr>       
         <tr>
          <th class="tabheader" style="width: 12px">#</th>
          <th class="tabheader" style="width: 129px">Sales No.</th>
          <th class="tabheader" style="width: 234px">Counter</th>
          <th class="tabheader" style="width: 128px">MM/YY</th>
          <th class="tabheader" style="width: 124px">Period</th>
          <th class="tabheader" style="width: 124px">Invoice</th>
          <th class="tabheader" style="width: 200px">GRN</th>
          <th class="tabheader" style="width: 50px">Status</th>
          <th class="tabheader" style="width: 12px">Detail</th>
          <th class="tabheader" style="width: 12px">Update</th>
		  <th class="tabheader" style="width: 12px">Cancel</th>
		  <th class="tabheader" style="width: 12px">Active</th>
         </tr>
         </thead>
		 <tbody>
		 <?php 
		    $sql = "SELECT sordno, sorddte, scustcd, smthyr, speriod, c.stat, grn_no, grn_date, cust.name, cinv.invno ";
		    $sql .= " FROM csalesmas c";
		    $sql .= " LEFT JOIN customer_master cust";
		    $sql .= " ON cust.custno = c.scustcd";
		    $sql .= " LEFT JOIN (SELECT mthyr, custcd, invno FROM cinvoicemas WHERE stat != 'C') as cinv";		    
		    $sql .= " ON c.smthyr = cinv.mthyr AND c.scustcd = cinv.custcd";
            $sql .= " where year(sorddte) between ".$var_fyear." and ".$var_tyear;   //--- additional add in for select data faster - 02/05/2016        
            $sql .= " ORDER BY sordno desc";  
			$rs_result = mysql_query($sql); 
	
		    $numi = 1;
			while ($rowq = mysql_fetch_assoc($rs_result)) { 
			
				$salorno = htmlentities($rowq['sordno']);
				//$orddte = date('d-m-Y', strtotime($rowq['sorddte']));
				
				$urlpopoldver = 'upd_csalesoldver.php';
				$urlpopnewver = 'upd_csalesnewver.php';
				$urlvie = 'vm_csales.php';
				$urlvieinv = 'vm_invoice.php';
				//$urlvie = 'ship_mas.php';
        
        /* $sqlcust = "select name from customer_master";
        $sqlcust .= " where custno = '".$rowq['scustcd']."'";   
        $custcd = $rowq['scustcd']; 

        
        $tmpcust = mysql_query($sqlcust) or die ("Cant get custname : ".mysql_error());
        
        if (mysql_numrows($tmpcust) >0) {
          $rstcust = mysql_fetch_object($tmpcust);
          $var_cname = $rstcust->name;
        } else { $var_cname = $rowq['scustcd']; } */
        
				$var_cname = $rowq['name'];
				if (is_null($var_cname)) {
				    $var_cname = $rowq['scustcd'];
				}
				
        /* $sqlinv = "select invno FROM csalesmas, cinvoicemas ";
        $sqlinv .= " WHERE smthyr = mthyr AND scustcd = custcd "; 
        $sqlinv .= " AND sordno = '".$salorno."'";
		$rs_result2 = mysql_query($sqlinv); 
			//echo $sql;
			while ($rowy = mysql_fetch_assoc($rs_result2)) { 			
				$invno = htmlentities($rowy['invno']);
			} */
                
				$invno = $rowq['invno'];
				if (is_null($invno)) {
				    $invno = "";
				}

        
				echo '<tr>';
            	echo '<td>'.$numi.'</td>';
           		echo '<td>'.$salorno.'</td>';
            	echo '<td>'.$var_cname.'</td>';
            	echo '<td>'.$rowq['smthyr'].'</td>';
            	echo '<td>'.$rowq['speriod'].'</td>';
            	//echo '<td>'.$invno.'</td>';
            	//-----------------------------------------------//
            	if ($var_accvie == 0){
            		echo '<td align="center"><a href="#">'.$invno.'</a>';'</td>';
            	}else{
            		echo '<td align="center"><a href="'.$urlvieinv.'?sorno='.$invno.'&custcd='.$custcd.'&menucd='.$var_menucode.'">'.$invno.'</a>';'</td>';
            	}

            	//-----------------------------------------------//
            	echo '<td>'.$rowq['grn_no'].'</td>';
            	echo '<td>'.$rowq['stat'].'</td>';
            
            	if ($var_accvie == 0){
            		echo '<td align="center"><a href="#">[VIEW]</a>';'</td>';
            	}else{
            		echo '<td align="center"><a href="'.$urlvie.'?sorno='.$salorno.'&custcd='.$rowq['scustcd'].'&menucd='.$var_menucode.'">[VIEW]</a>';'</td>';
            	}
	            if ($var_accupd == 0){
		            echo '<td align="center"><a href="#">[EDIT]</a>';'</td>';
	            }else{
	            	if ($rowq['stat'] == "C"){
	            		/* echo '<td align="center"><a href="#" title="This Counter Sales is Cancelled; Edit Is Not Allow">[EDIT]</a>';'</td>'; */
	            	    echo'<td></td>';
	            	}else{ 
		            	echo '<td align="center">';
						echo '<a href="'.$urlpopoldver.'?sorno='.$salorno.'&custcd='.$rowq['scustcd'].'&menucd='.$var_menucode.'">[EDIT_OLD]</a>';
						echo '&nbsp;<a href="'.$urlpopnewver.'?sorno='.$salorno.'&custcd='.$rowq['scustcd'].'&menucd='.$var_menucode.'">[EDIT_NEW]</a>';
						echo '</td>';
	            	}
	            }
	            if ($var_accdel == 0){
	              echo '<td align="center"><input type="checkbox" DISABLED  name="procd[]" value="'.$values.'" />'.'</td>';
	            }else{
	            //  if ($rowq['stat'] == "A"){
					//echo '<td align="center"><input type="checkbox" DISABLED  name="procd[]" value="'.$values.'" />'.'</td>';
				 // }else{	
	              	$values = implode(',', $rowq);	
	              	echo '<td align="center"><input type="checkbox" name="salorno[]" value="'.$values.'" />'.'</td>';
    	         // }	
    	        }
           		
           		 if ($var_accdel == 0){
	              echo '<td align="center"><input type="checkbox" DISABLED  name="procd[]" value="'.$values.'" />'.'</td>';
	            }else{
	              //if ($rowq['stat'] == "C"){
					//echo '<td align="center"><input type="checkbox" DISABLED  name="procd[]" value="'.$values.'" />'.'</td>';
				  //}else{	
	              	$values = implode(',', $rowq);	
	              	echo '<td align="center"><input type="checkbox" name="salorno[]" value="'.$values.'" />'.'</td>';
    	        //  }	
    	        }
           		
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
