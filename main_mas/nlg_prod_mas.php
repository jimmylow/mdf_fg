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
    
   
    if ($_POST['Submit'] == "Save") {
     $catcd     = mysql_real_escape_string($_POST['catcd']);
     $catdesc   = mysql_real_escape_string($_POST['catde']);
     $catitmgrp = mysql_real_escape_string($_POST['selitmgrp']);
     $catmark   = $_POST['catmark'];
     $catcut    = $_POST['catcut'];
     $catspread = $_POST['catspread'];
     $catbundle = $_POST['catbundle'];

     if ($catcd <> "") {

      $var_sql = " SELECT count(*) as cnt from cat_master ";
      $var_sql .= " WHERE cat_code = '$catcd'";

      $query_id = mysql_query($var_sql) or die ("Cant Check Category");
      $res_id = mysql_fetch_object($query_id);

      if ($res_id->cnt > 0 ) {
	     $backloc = "../stck_mas/catmas.php?stat=3&menucd=".$var_menucode;
         echo "<script>";
         echo 'location.replace("'.$backloc.'")';
         echo "</script>";   
      }else {
		
		 if ($catmark == ""){$catmark = 0;}	
		 if ($catcut == ""){$catcut = 0;}	
		 if ($catspread == ""){$catspread = 0;}	
		 if ($catbundle == ""){$catbundle = 0;}	
         $sql = "INSERT INTO cat_master values 
                ('$catcd', '$catdesc', '$catmark', '$catcut', '$catspread',  '$catbundle', '$var_loginid', CURDATE(), '$var_loginid', CURDATE(), '$catitmgrp')";
     	 mysql_query($sql) or mysql_error(); 
     	 $backloc = "../stck_mas/catmas.php?stat=1&menucd=".$var_menucode;
         echo "<script>";
         echo 'location.replace("'.$backloc.'")';
         echo "</script>";         
	  } 
     }else{
         $backloc = "../stck_mas/catmas.php?stat=4&menucd=".$var_menucode;
         echo "<script>";
         echo 'location.replace("'.$backloc.'")';
         echo "</script>";     
     }
    }
       
    if ($_POST['Submit'] == "Delete") {
     if(!empty($_POST['catcd']) && is_array($_POST['catcd'])) 
     {   
           foreach($_POST['catcd'] as $value ) {
		    	$sql = "DELETE FROM cat_master WHERE cat_code ='".$value."'"; 
		 		mysql_query($sql); 
		   }
		   $backloc = "../stck_mas/catmas.php?stat=1&menucd=".$var_menucode;
           echo "<script>";
           echo 'location.replace("'.$backloc.'")';
           echo "</script>";          
      }      
    }

    if (isset($_POST['Submit'])){ 
     if ($_POST['Submit'] == "Print") {
       	
       // Redirect browser 
        $fname = "cat_rpt.rptdesign&__title=myReport"; 
        $dest = "http://".$var_prtserver.":8080/birt-viewer/frameset?__report=".$fname."&dbsel=".$varrpturldb;
        $dest .= urlencode(realpath($fname));
        
        //header("Location: $dest" );
        echo "<script language=\"javascript\">window.open('".$dest."','name','height=1000,width=1000,left=200,top=200');</script>";
     }
    } 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">

<style media="all" type="text/css">
@import "../css/styles.css";
@import "../css/demo_table.css";
thead th input { width: 90% }


.style2 {
	margin-right: 0px;
}
</style>

<script type="text/javascript" language="javascript" src="../media/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.nightly.js"></script>
<script type="text/javascript" src="../media/js/jquery.dataTables.columnFilter.js"></script>

<script type="text/javascript" charset="utf-8"> 
$(document).ready(function() {
		$('#example').dataTable( {
			"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50,"All"]],
			"bStateSave": true,
			"bFilter": true,
			"sPaginationType": "full_numbers",
			"aoColumns": [
    					null,
    					null,
    					null,
    					null,
    					null,
    					null,
    					null
    				]
			
		} )
		.columnFilter({sPlaceHolder: "head:after",
		aoColumns: [ 
					 null,	
					 { type: "text" },
				     { type: "text" },
				     { type: "text" },
				     { type: "text" },
				     { type: "text" },
				     null
				   ]
		});

} );
			
function upperCase(x)
{
var y=document.getElementById(x).value;
document.getElementById(x).value=y.toUpperCase();
}

jQuery(function($) {
  
    $("tr :checkbox").live("click", function() {
        $(this).closest("tr").css("background-color", this.checked ? "#FFCC33" : "");
    });
  
});

   
function AjaxFunction(catcd)
{
      
		var httpxml;
		try	{
			// Firefox, Opera 8.0+, Safari
			httpxml=new XMLHttpRequest();
		}catch (e){
		  // Internet Explorer
		  try{
			  httpxml=new ActiveXObject("Msxml2.XMLHTTP");
		  }catch (e){
		    try{
			   httpxml=new ActiveXObject("Microsoft.XMLHTTP");
		    }catch (e){
			   alert("Your browser does not support AJAX!");
			   return false;
		    }
		}
		
}

function stateck()
{
		if(httpxml.readyState==4)
		{
			document.getElementById("msgcd").innerHTML=httpxml.responseText;
		}
}
	
	var url="aja_chk_cat.php";
	
	url=url+"?catcd="+catcd;
	url=url+"&sid="+Math.random();
	httpxml.onreadystatechange=stateck;
	
	httpxml.open("GET",encodeURI(url),true);
	httpxml.send(null);
}

function chkValue(vid)
{
    var col2 = document.getElementById(vid).value;
	
   	if (col2 !== ""){

		if(isNaN(col2)) {	
    	   alert('Please Enter a valid number:' + col2);
    	   document.getElementById(vid).focus();
    	   col2 = 0;
    	}
    	document.getElementById(vid).value = parseFloat(col2).toFixed(2);
    }
}

function validateForm()
{
    var x=document.forms["InpColMas"]["catcdid"].value;
	if (x==null || x=="")
	{
	alert("Category Code Cannot Be Blank");
	document.InpColMas.catcdid.focus();
	return false;
	}
}		
</script>
</head>
<body onload="document.InpColMas.catcdid.focus()">
  <?php include("../topbarm.php"); ?> 
  <!--<?php include("../sidebarm.php"); ?> -->

  <div class="contentc" style="width: 813px">
	<fieldset name="Group1" style="width: 750px;" class="style2">
	 <legend class="title">
	 <span style="color: rgb(51, 102, 204); font-family: 'Tw Cen MT'; font-size: 14px; font-style: normal; font-variant: normal; font-weight: bold; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255); display: inline !important; float: none;">
	 NLG PRODUCT CODE MASTER</span></legend>
	  <br>
	     
		<br/><br/>
        <form name="LstColMas" method="POST" action="<?php echo $_SERVER['PHP_SELF'].'?menucd='.$var_menucode; ?>">
		 <table cellpadding="0" cellspacing="0" id="example" class="display" width="100%">
         <thead>
       		<tr>
         	 <th></th>
         	 <th>Product Code</th>
         	 <th>Description</th>
         	 <th>Buyer</th>
         	 <th>Category</th>
         	 <th>Stat</th>
         	 <th></th>
         	</tr>

         	<tr>
         	 <th class="tabheader" style="width: 20px">#</th>
         	 <th class="tabheader" style="width: 100px">Product Code</th>
         	 <th class="tabheader" style="width: 250px">Description</th>
         	 <th class="tabheader" style="width: 100px">Buyer</th>
         	 <th class="tabheader" style="width: 100px">Category</th>
         	 <th class="tabheader" style="width: 100px">Stat</th>
         	 <th class="tabheader" style="width: 30px">View</th>
         	</tr>
         	</thead>
         <tbody>
		 <?php 
		 
	$var_server = '192.168.0.142:9909';
	$var_userid = 'root';
	$var_password = 'admin9002';
	$var_db_name='nl_db'; 
  
	$db_link2  = mysql_connect($var_server, $var_userid, $var_password)or die("cannot connect");
  mysql_select_db("$var_db_name")or die("cannot select DB ".$var_db_name.mysql_error());
	
	mysql_query("SET NAMES 'utf8'", $db_link2)or die(mysql_error()); 	//this will take effect both retrieve, update or insert data of utf-8 
  
		 
		    $sql = "SELECT * ";
		    $sql .= " FROM pro_cd_master";
    		$sql .= " ORDER BY modified_on";  
			$rs_result = mysql_query($sql, $db_link2) or die ("error : ".mysql_error()); 
		 
		    $numi = 1;
			while ($rowq = mysql_fetch_assoc($rs_result)) { 
					 
				//$showdte = date('d-m-Y', strtotime($rowq['modified_on']));
				$urlvie = 'vm_procd_mas.php';
				echo '<tr bgcolor='.$defaultcolor.'>';
            	echo '<td>'.$numi.'</td>';
            	echo '<td>'.htmlentities($rowq['prod_code']).'</td>';
              echo '<td align="center">'.$rowq['prod_desc'].'</td>';
            	echo '<td align="center">'.$rowq['prod_buyer'].'</td>';
            	//echo '<td align="center">'.$rowq['prod_type'].'</td>';
            	echo '<td align="center">'.$rowq['pro_cat'].'</td>';
            	echo '<td align="center">'.$rowq['actvty'].'</td>';

            
            $urlvie = 'vm_procd_mas.php';
            if ($var_accvie == 0){
            echo '<td align="center"><a href="#">[VIEW]</a>';'</td>';
            }else{
            echo '<td align="center"><a href="'.$urlvie.'?procd='.htmlentities($rowq['prod_code']).'&menucd='.$var_menucode.'">[VIEW]</a>';'</td>';
            }
            
            echo '</tr>';
            $numi = $numi + 1;
			}
		 ?></tbody>
		 </table>
         </form>
	 
	</fieldset>
</div>
</body>

</html>

