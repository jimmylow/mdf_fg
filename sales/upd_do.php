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
    
      $var_dono = $_GET['dono'];
      $var_menucode = $_GET['menucd'];
      include("../Setting/ChqAuth.php");

    }
    
    if ($_POST['btnGet'] == "Get" && !empty($_POST['dono'])) {
    	$var_dono = $_POST['dono'];
    }
    
    if ($_POST['Submit'] == "Update") {
      
    //phpinfo();
    $vmdelordno = $_POST['dono'];
    $vmmthyr  = $_POST['samthyr'];
		$vmdodte = date('Y-m-d', strtotime($_POST['dodte']));

		if ($vmdelordno <> "") {
    			
        $vartoday = date("Y-m-d H:i:s"); 

				$sql = "Update salesdo Set delorddte = '$vmdodte', ";
				$sql .= "  mthyr = '$vmmthyr', modified_by = '$var_loginid', modified_on='$vartoday ' ";
				$sql .= "  Where delordno ='$vmdelordno'";
        
				mysql_query($sql) or die ("Cant update : ".mysql_error());       
				
				$backloc = "../sales/m_do_mas.php?menucd=".$var_menucode;
           		echo "<script>";
           		echo 'location.replace("'.$backloc.'")';
            	echo "</script>"; 	
					
		}else{
			$backloc = "../sales/upd_do.php?stat=4&menucd=".$var_menucode;
           	echo "<script>";
           	echo 'location.replace("'.$backloc.'")';
            echo "</script>"; 
		} 
   }
       
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">


<style media="all" type="text/css">
@import "../css/multitable/themes/smoothness/jquery-ui-1.8.4.custom.css";
@import "../css/styles.css";

.general-table #prococode                        { border: 1px solid #ccc; font-size: 12px; font-weight: bold;}
.general-table #procoucost                      { border: 1px solid #ccc; font-size: 12px; font-weight: bold;}
.general-table #prococompt                      { border: 1px solid #ccc; font-size: 12px; font-weight: bold;}

.style2 {
	margin-right: 0px;
}
</style>

<!-- jQuery libs -->
<script type="text/javascript" src="../js/datetimepicker_css.js"></script>
<script type="text/javascript" src="../js/multitable/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="../js/multitable/jquery-ui-1.8.14.custom.min.js"></script>
<script type="text/javascript" src="../js/JavaScriptUtil.js"></script>
<script type="text/javascript" src="../js/Parsers.js"></script>
<script type="text/javascript" src="../js/InputMask.js"></script>


<script type="text/javascript"> 

function upperCase(x)
{
var y=document.getElementById(x).value;
document.getElementById(x).value=y.toUpperCase();
}

function setup() {

		document.InpPO.dodte.focus();
				
 		//Set up the date parsers
        var dateParser = new DateParser("dd-MM-yyyy");
        
		//Set up the DateMasks
		var errorMessage = "Invalid date: ${value}. Expected format: ${mask}";
		var dateMask1 = new DateMask("dd-MM-yyyy", "shipdte");
		dateMask1.validationMessage = errorMessage; 
    
		var dateMask2 = new DateMask("dd-MM-yyyy", "dodte");
		dateMask1.validationMessage = errorMessage;      
}

function setup2() {

 		//Set up the date parsers
        var dateParser = new DateParser("MM/yyyy");
      
		//Set up the DateMasks
		var errorMessage = "Invalid date: ${value}. Expected format: ${mask}";
		var dateMask1 = new DateMask("MM/yyyy", "samthyr");
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

function validateForm()
{

	var btnGet=document.forms["InpPO"]["btnGet"].value;
	if (btnGet!=null || btnGet=="Get") {
		return true;
	}
	  
   var x=document.forms["InpPO"]["dodte"].value;
	if (x==null || x=="")
	{
	alert("DO Date Must Not Be Blank");
	document.InpPO.dodte.focus;
	return false;
	}
  
   var x=document.forms["InpPO"]["samthyr"].value;
	if (x==null || x=="")
	{
	alert("MM/YYYY Must Not Be Blank");
	document.InpPO.samthyr.focus;
	return false;
	}  
  
}


</script>
</head>

<body onload="setup()">
	  <?php include("../topbarm.php"); ?> 
<!--  <?php include("../sidebarm.php"); ?> -->

  <?php
  if (!empty($var_dono)) {
  	 $sql = "select delorddte, sordno, mthyr, stat from salesdo";
     $sql .= " where delordno ='".$var_dono."'";
     $sql_result = mysql_query($sql);
     $row = mysql_fetch_array($sql_result);
     $num=mysql_numrows($sql_result);
     if ($num==0) {
     	echo "<script>";
     	echo "alert('Delivery Order No ".$var_dono. " does not exist!')";
     	echo "</script>";
     }
     
     if ($row['stat'] == "C"){
     	echo "<script>";
     	echo "alert('This DO is Cancelled; Edit Is Not Allow')";
     	echo "</script>";
     	$var_dono = "";
     }
     else {
     $dodte = date('d-m-Y', strtotime($row['delorddte']));
     $var_ordno = $row['sordno'];
     $mthyr = $row['mthyr'];
       
  	 $sql = "select shipdte from salesshipmas";
     $sql .= " where shipno ='".$var_ordno."'";
     $sql_result = mysql_query($sql);
     $row = mysql_fetch_array($sql_result);

     $shipdte = date('d-m-Y', strtotime($row['shipdte']));
     }
     
  }  
     
  ?> 
  
  <div class="contentc">

	<fieldset name="Group1" style=" width: 973px;" class="style2">
	 <legend class="title">UPDATE DELIVERY ENTRY</legend>
	  <br>	 
	  
	  <form name="InpPO" method="POST" action="<?php echo $_SERVER['PHP_SELF'].'?menucd='.$var_menucode; ?>" onsubmit="return validateForm()">
	   
		<table style="width: 993px; ">
		  <tr>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 122px">Order No</td>
	  	   <td style="width: 13px">:</td>
	  	   <td style="width: 201px">
			<input class="inputtxt" name="sordno" id="sordnoid" type="text" readonly style="width: 204px;" value = "<?php echo $var_ordno; ?>">         
		   </td>
		   <td style="width: 10px"></td>
		   <td style="width: 204px">Shipping Date</td>
		   <td>:</td>
		   <td style="width: 284px">
		   <input class="inputtxt" name="shipdte" id ="shipdte" type="text" style="width: 128px;" value="<?php  echo $shipdte; ?>" readonly></td>       
	  	  </tr>

	   	   <tr>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 122px">DO No</td>
	  	   <td style="width: 13px">:</td>
	  	   <td style="width: 201px">
			<input class="inputtxt" name="dono" id="donoid" type="text" style="width: 204px;" value = "<?php echo $var_dono; ?>">
			<input type="submit" name="btnGet" value="Get" class="butsub" style="width: 60px; height: 32px" >                  
		   </td>
			<td style="width: 10px"></td>
			<td style="width: 204px">DO Date</td>
			<td style="width: 16px">:</td>
			<td style="width: 284px">
		   <input class="inputtxt" name="dodte" id ="dodte" type="text" style="width: 128px;" value="<?php echo $dodte; ?>">
		   <img alt="Date Selection" src="../images/cal.gif" onclick="javascript:NewCssCal('dodte','ddMMyyyy')" style="cursor:pointer"></td>
	  	  </tr> 
	   	   <tr>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 122px"></td>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 201px">
		   </td>
			<td style="width: 10px"></td>
			<td style="width: 204px">MM/YYYY</td>
			<td style="width: 16px">:</td>
			<td style="width: 284px">
<input class="inputtxt" name="samthyr" id="samthyrid" type="text" maxlength="45" style="width: 100px;" value="<?php echo $mthyr; ?>"></td>
	  	  </tr>          
		  <tr>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 122px"></td>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 201px">
		   <td style="width: 10px"></td>
		   <td style="width: 204px"></td>
		   <td></td>
		   <td style="width: 284px">
	  	  </tr>	  	  
	  	  </table>
		 
     <br /><br />
		 <table>
		  	<tr>
				<td style="width: 1150px; height: 22px;" align="center">
				<?php
				 $locatr = "m_do_mas.php?menucd=".$var_menucode;
			
				 echo '<input type="button" value="Back" class="butsub" style="width: 60px; height: 32px" onclick="location.href=\''.$locatr.'\'" >';
					include("../Setting/btnupdate.php");
				?>
				</td>
			</tr>
			<tr>
				<td style="width: 1150px" colspan="5">
				<span style="color:#FF0000">Message :</span>
				<?php
					if (isset($var_stat)){
						switch ($var_stat)
						{
						case 1:
							echo("<span>Success Process</span>");
							break;
						case 0:
							echo("<span>Process Fail</span>");
							break;
						case 3:
							echo("<span>Duplicated Found Or Code Number Fall In Same Range</span>");
							break;
						case 4:
							echo("<span>Please Fill In The Data To Save</span>");
							break;
						case 5:
							echo("<span>This Product Code And Rev No Has A Record</span>");
							break;
						case 6:
							echo("<span>Duplicate Job File ID Found; Process Fail.</span>");
							break;
						case 7:
							echo("<span>This Product Code Dost Not Exits</span>");
							break;			
						default:
							echo "";
						}
					}	
          
         mysql_close ($db_link); 
				?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
	  	</table>
	   </form>	
	</fieldset>
	</div>
	<div class="spacer"></div>
</body>

</html>
