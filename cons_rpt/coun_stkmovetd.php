<?php
	
    include("../Setting/Configifx.php");
	include("../Setting/Connection.php");
	
	$var_loginid = $_SESSION['sid'];
	ini_set('max_execution_time', 0);
    
    if($var_loginid == "") { 
      echo "<script>";   
      echo "alert('Not Log In to the system');"; 
      echo "</script>"; 

      echo "<script>";
      echo 'top.location.href = "../index.php"';
      echo "</script>";
    } else {
      $var_menucode = $_GET['menucd'];
      include("../Setting/ChqAuth.php");
    }

    if (isset($_POST['Submit'])){ 
     if ($_POST['Submit'] == "Print") {
     
     	$fpr = $_POST['selfpr'];
     	$tpr = $_POST['seltpr'];
     	$asd = date("Y-m-d", strtotime($_POST['inqasat']));

		#----------------Prepare Temp Table For Printing -----------------------------------
     	$sql  = " Delete From tmpctstmv where usernm = '$var_loginid'";
        mysql_query($sql) or die("Unable To Prepare Temp Table For Printing".mysql_error());
		#-----------------------------------------------------------------------------------
		
		#----------------------------------------------------
		$sql  = "CREATE TEMPORARY TABLE tmp2salesmas(";
	    $sql .= "sordno varchar(30), sorddte datetime, scustcd varchar(10), smthyr varchar(7), speriod varchar(1), ";
		$sql .= "less_type varchar(1), less_amt decimal(10,2), stat varchar(1), salesdte date)";
		mysql_query($sql) or die("Unable To Prepare Temp Table For Printing".mysql_error());
		$sql  = " Delete From tmp2salesmas";
        mysql_query($sql) or die("Unable To Prepare Temp Table For Printing".mysql_error);
		
		$shardSize = 7000;
	 	$sqliq = "";		   			
	 	$k = 0;
		$sqld  = "select *";
        $sqld .= " from csalesmas where stat != 'C'";
		$rs_n1 = mysql_query($sqld);
		while ($rn1 = mysql_fetch_assoc($rs_n1)) {
			$sordno    = $rn1['sordno'];
		    $sorddte   = $rn1['sorddte'];
		    $scustcd   = $rn1['scustcd'];
			$smthyr    = $rn1['smthyr'];
		    $speriod   = $rn1['speriod'];
		    $less_type = $rn1['less_type'];
			$less_amt  = $rn1['less_amt'];
		    $stat      = $rn1['stat'];
			if (empty($less_amt)){$less_amt = 0;}
			
			$mthsdte   = substr($smthyr, 0, 2);
			$yrsdte    = substr($smthyr, 3, 6);
			$bsdte     = $yrsdte.'-'.$mthsdte.'-01';
			$salesdte  = date("Y-m-t", strtotime($bsdte));
						//echo $sordno.' '.$smthyr.' '.$mthsdte.' '.$yrsdte.' '.$bsdte.' '.$salesdte."<br>";

			if ($k % $shardSize == 0) {
       			if ($k != 0) {	  
        	   		mysql_query($sqliq) or die("Unable Save In Temp Table ".mysql_error());
       			}
       			$sqliq = 'Insert Into tmp2salesmas values ';
    		}
   			$sqliq .= (($k % $shardSize == 0) ? '' : ', ') . "('$sordno', '$sorddte', '$scustcd', '$smthyr', '$speriod','$less_type', '$less_amt', 
															   '$stat', '$salesdte')";
			$k = $k + 1;
		}
		if (!empty($sqliq)){
			mysql_query($sqliq) or die ("Cant insert 2 : ".mysql_error());
		}
		#----------------------------------------------------
		
		$shardSize = 6000;
	 	$sqliq = "";		   			
	 	$k = 0;
		$sqld  = "select distinct min(x.salesdte), x.scustcd, y.sprocd from tmp2salesmas x, csalesdet y";
		$sqld .= " where x.salesdte <= '$asd' and x.sordno = y.sordno";
		$sqld .= " and y.sprocd between  '$fpr' and '$tpr'";
		$sqld .= " group by 2, 3";
		$rs_n1 = mysql_query($sqld);
		while ($rn1 = mysql_fetch_assoc($rs_n1)) {
			$msdte  = $rn1['min(x.salesdte)'];
		    $custcd = $rn1['scustcd'];
		    $prcd   = $rn1['sprocd'];
		    
		    $sqlf = "select sordno from tmp2salesmas";
			$sqlf .= " where scustcd = '$custcd' and salesdte = '$msdte'";
			$s_rf = mysql_query($sqlf);
			$rf = mysql_fetch_array($s_rf);
			$refno  = $rf['sordno'];
		    
			//echo $msdte.' '.$custcd.' '.$prcd.' '.$refno.'<br>';
			$sqlf = "select openingqty from csalesdet";
			$sqlf .= " where sprocd = '$prcd' and sordno = '$refno'";
			$s_rf = mysql_query($sqlf);
			$rf = mysql_fetch_array($s_rf);
			$openqty  = $rf['openingqty'];
		    if (empty($openqty)){$openqty = 0;}
		    
       		if ($k % $shardSize == 0) {
       			if ($k != 0) {	  
           			mysql_query($sqliq) or die("Unable Save In Temp Table ".mysql_error());
       			}
       			$sqliq = 'Insert Into tmpctstmv (countercd, counterde, procode, prodesc, openqty, usernm) values ';
    		}
   			$sqliq .= (($k % $shardSize == 0) ? '' : ', ') . "('$custcd', '$custde', '$prcd', '$prde', '$openqty','$var_loginid')";
		 	$k = $k + 1;
		}	
		if (!empty($sqliq)){
			mysql_query($sqliq) or die ("Cant insert 2 : ".mysql_error());
		}
		#-----------------------------------------------------------------------------------

		#-----------------------------------------------------------------------------------
		$shardSize = 6000;
	 	$sqliq = "";		   			
	 	$k = 0;    
		$sql2  = "select y.scustcd, x.sprocd, sum(x.soldqty), sum(x.doqty), sum(x.rtnqty), sum(x.shortqty), sum(x.overqty), sum(x.adjqty)";
		$sql2 .= " from csalesdet x, tmp2salesmas y";
		$sql2 .= " where y.salesdte <= '$asd'  and x.sordno = y.sordno";
		$sql2 .= " and   x.sprocd between '$fpr' and '$tpr'";
		$sql2 .= " group by 1, 2";
		$sql2 .= " order by 1, 2";
		$rs_n = mysql_query($sql2);
		while ($rn = mysql_fetch_assoc($rs_n)) {
		    $custcd   = $rn['scustcd'];
			$prcd     = $rn['sprocd'];
			$soldqty  = $rn['sum(x.soldqty)'];
			$doqty    = $rn['sum(x.doqty)'];
			$rtnqty   = $rn['sum(x.rtnqty)'];
			$shortqty = $rn['sum(x.shortqty)'];
			$overqty  = $rn['sum(x.overqty)'];
			$adjqty   = $rn['sum(x.adjqty)'];

       		if ($k % $shardSize == 0) {
       			if ($k != 0) {	  
           			mysql_query($sqliq) or die("Unable Save In Temp Table ".mysql_error());
       			}
       			$sqliq = 'Insert Into tmpctstmv (countercd, counterde, procode, prodesc, doqty, soldqty, rtnqty, shortqty, overqty, adjqty, usernm) values ';
    		}
   			$sqliq .= (($k % $shardSize == 0) ? '' : ', ') . "('$custcd', '$custde', '$prcd', '$prde', '$doqty', '$soldqty', '$rtnqty', '$shortqty' , '$overqty', 
   															  '$adjqty', '$var_loginid')";
		 	$k = $k + 1;
		}	
		if (!empty($sqliq)){
			mysql_query($sqliq) or die ("Cant insert 2 : ".mysql_error());
		}
		#-----------------------------------------------------------------------------------

		$sqlm  = "select count(*)";
		$sqlm .= " from tmpctstmv ";
        $sqlm .= " where usernm = '$var_loginid'";
     	$sql_resultm = mysql_query($sqlm);
        $rowm = mysql_fetch_array($sql_resultm);
        if ($rowv1[0] == "" or $rowv1[0] == null){ 
					$rowv1[0]  = 0.00;
		}
        $cnt  = $rowm[0];

		if($cnt == 0){
			echo "<script>";   
      		echo "alert('No Data Found On Selected Query');"; 
      		echo "</script>";
		}else{
			// Redirect browser
        	$fname = "ctstk_move.rptdesign&__title=myReport"; 
        	$dest = "http://".$var_prtserver.":8080/birtfg/frameset?__report=".$fname."&fp=".$fpr."&tp=".$tpr."&usernm=".$var_loginid."&s=".$asd."&dbsel=".$varrpturldb;
        	$dest .= urlencode(realpath($fname));

        	//header("Location: $dest" );
        	echo "<script language=\"javascript\">window.open('".$dest."','name','height=1000,width=1000,left=200,top=200');</script>";
       }
        
        $backloc = "../cons_rpt/coun_stkmovetd.php?stat=4&menucd=".$var_menucode;
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
@import "../css/styles.css";
@import "../css/demo_table.css";

.style2 {
	margin-right: 8px;
}
</style>
<script type="text/javascript" src="../js/datetimepicker_css.js"></script>
<script type="text/javascript" language="javascript" src="../media/js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.nightly.js"></script>
<script type="text/javascript" src="../media/js/jquery.dataTables.columnFilter.js"></script>
<script type="text/javascript" src="../js/JavaScriptUtil.js"></script>
<script type="text/javascript" src="../js/Parsers.js"></script>
<script type="text/javascript" src="../js/InputMask.js"></script>

<script type="text/javascript" charset="utf-8"> 

function setup() {

	document.InpPurBal.selfpr.focus();
						
 	//Set up the date parsers
    var dateParser = new DateParser("dd-MM-yyyy");
      
	//Set up the DateMasks
	var errorMessage = "Invalid date: ${value}. Expected format: ${mask}";
	var dateMask1 = new DateMask("dd-MM-yyyy", "inqasat");
	dateMask1.validationMessage = errorMessage;		
}

function getXMLHTTP() { //fuction to return the xml http object
		var xmlhttp=false;	
		try{
			xmlhttp=new XMLHttpRequest();
		}
		catch(e)	{		
			try{			
				xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e){
				try{
				xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
				}
				catch(e1){
					xmlhttp=false;
				}
			}
		}		 	
		return xmlhttp;
}

function chkSubmit()
{
	var fs = document.forms["InpPurBal"]["selfpr"].value;
	if (fs==null || fs=="")
	{
		alert("From Product Code Cannot Be Blank");
		document.InpPurBal.selfpr.focus();
		return false;
	}
		
	var ts = document.forms["InpPurBal"]["seltpr"].value;
	if (ts==null || ts=="")
	{
		alert("To Product Code Cannot Be Blank");
		document.InpPurBal.seltpr.focus();
		return false;
	}

	var x=document.forms["InpPurBal"]["inqasat"].value;
	if (x==null || x=="")
	{
		alert("At As Date Cannot Be Blank");
		document.InpPurBal.inqasat.focus();
		return false;
	}
}
</script>
</head>

 <!--<?php include("../sidebarm.php"); ?>--> 
<body onload="setup()">
   <?php include("../topbarm.php"); ?> 
	<div class="contentc">
	<fieldset name="Group1" style=" width: 598px; height:200px;">
	 <legend class="title">COUNTER STOCK MOVEMENT REPORT (UP TO DATE)</legend>
	  <br>
	  <form name="InpPurBal" method="POST" onSubmit= "return chkSubmit()" action="<?php echo $_SERVER['PHP_SELF'].'?menucd='.$var_menucode; ?>" style="height: 134px; width: 583px;">
		<table style="width: 573px">
		  <tr>
		  	<td style="width: 15px"></td>
		  	<td style="width: 128px">From Group Code</td>
		  	<td style="width: 2px">:</td>
		  	<td style="width: 134px">
		  		<select name="selfpr" id="selfpr" style="width: 278px">
			    <?php
                   $sql = "select distinct GroupCode from product where (Status <> 'D' or Status is null) ORDER BY GroupCode";
                   $sql_result = mysql_query($sql) or die("Not Enable To Query Product Code".mysql_error());
                       
				   if(mysql_num_rows($sql_result)) 
				   {
				   	 while($row = mysql_fetch_assoc($sql_result)) 
				     { 
					  echo '<option value="'.$row['GroupCode'].'">'.$row['GroupCode'].'</option>';
				 	 } 
				   }
	            ?>				   
			  </select>
		  	</td>
		  </tr>
		  <tr><td style="width: 15px"></td></tr>
		  <tr>
		  	<td></td>
		  	<td>To Group Code</td>
		  	<td>:</td>
		  	<td>
		  		<select name="seltpr" id="seltpr" style="width: 278px">
			    <?php
                   $sql = "select distinct GroupCode from product where (Status <> 'D' or Status is null) ORDER BY GroupCode";
                   $sql_result = mysql_query($sql) or die("Enable To Query Supplier".mysql_error());
                      
				   if(mysql_num_rows($sql_result)) 
				   {
				   	 while($row = mysql_fetch_assoc($sql_result)) 
				     { 
					  echo '<option value="'.$row['GroupCode'].'">'.$row['GroupCode'].'</option>';
				 	 } 
				   }
	            ?>				   
			  </select>

		  	</td>
		  </tr>
		  <tr><td style="width: 15px"></td></tr>
	  	  <tr>
	  	    <td style="width: 15px"></td>
	  	    <td style="width: 128px" class="tdlabel">As At</td>
	  	    <td style="width: 2px">:</td> 
	  	    <td style="width: 134px">
				<input class="inputtxt" name="inqasat" id ="inqasat" type="text" value="<?php  echo date("d-m-Y"); ?>" style="width: 100px">
				<img alt="Date Selection" src="../images/cal.gif" onclick="javascript:NewCssCal('inqasat','ddMMyyyy')" style="cursor:pointer">
			</td>	
	  	  </tr>
	   	  <tr>
	   	  	<td>&nbsp;</td>
	   	  </tr>
	  	  <tr>
	  	   <td colspan="8" align="center">
	  	   <?php
	  	   		include("../Setting/btnprint.php");
	  	   ?>
	  	   </td>
	  	  </tr>
	  	  <tr><td>&nbsp;</td></tr>	
	  	</table>
	   </form>	
	</fieldset>
	 </div>

</body>

</html>
