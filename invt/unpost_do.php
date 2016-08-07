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
		$remark   = $_POST['remark'];
	            
            
		//if ($refno <> "") {
		
			$sysno = '';
     		$sqlchk = " select noctrl from ctrl_sysno ";
     		$sqlchk.= " where `descrip` = 'UPDO' and counter = 'HQ';";
     
     		$dumsysno= mysql_query($sqlchk) or die(mysql_error());
     		while($row = mysql_fetch_array($dumsysno))
     		{
     			$sysno = $row['noctrl'];        
     		}
     		if ($sysno ==NULL)
     		{
     			$sysno = '0';
     					$sysno_sql = "INSERT INTO ctrl_sysno values ('UPDO', 'HQ', 1)";

     			mysql_query($sysno_sql);

     		}
     		$newsysno = $sysno + 1;
     		
     		$trf_sysno  = str_pad($newsysno , 5, '0', STR_PAD_LEFT);
     		$trf_sysno = "UPDO".$trf_sysno;


         	$vartoday = date("Y-m-d H:i:s");
				
				if(!empty($_POST['procofrm']) && is_array($_POST['procofrm'])) 
				{	
					foreach($_POST['procofrm'] as $row=>$matcd ) {
						$frmcode    = $matcd;
						$seqno      = $_POST['seqno'][$row];
						$rmk        = $_POST['rmk'][$row];
							
							$sql = "INSERT INTO unpos_do_det values 
						    		('$trf_sysno', '$frmcode', '$rmk','$vartoday','$var_loginid')";
							mysql_query($sql) or die("Can't UNPOST DO ".mysql_error());
							
					}
				}
				
				 //---TO UNPOST DO---//
				 $unpost_sql = "UPDATE invtrcvd_nlg  SET posted = 'N'  WHERE refno = '$frmcode'";

		     	 mysql_query($unpost_sql);
		     	 
		     	 $del_hist_sql = "DELETE FROM invthist  WHERE refid = '$frmcode' AND reftype = 'RC' ";

		     	 mysql_query($del_hist_sql);
		     	 //---END---//

				
				$updsysno_sql = "UPDATE ctrl_sysno SET noctrl = '$newsysno' where `descrip` = 'UPDO' and counter = 'HQ'";

		     	 mysql_query($updsysno_sql);
				
				$backloc = "../invt/m_unpost_do.php?menucd=".$var_menucode;
           		echo "<script>";
           		echo 'location.replace("'.$backloc.'")';
            	echo "</script>"; 	
			//}		
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

.general-table #procomat                        { border: 1px solid #ccc; font-size: 12px; font-weight: bold;}
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


<!-- Our jQuery Script to make everything work -->
<script  type="text/javascript" src="jq-updo-script.js"></script>


<script type="text/javascript"> 

function setup() {

		document.InpJobFMas.trfdte.focus();
						
 		//Set up the date parsers
        var dateParser = new DateParser("dd-MM-yyyy");
      
		//Set up the DateMasks
		var errorMessage = "Invalid date: ${value}. Expected format: ${mask}";
		var dateMask1 = new DateMask("dd-MM-yyyy", "trfdte");
		dateMask1.validationMessage = errorMessage;		
}


function upperCase(x)
{
var y=document.getElementById(x).value;
document.getElementById(x).value=y.toUpperCase();
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

    var x=document.forms["InpJobFMas"]["trfdte"].value;
	if (x==null || x=="")
	{
	alert("Date Must Not Be Blank");
	document.InpJobFMas.trfdte.focus();
	return false;
	}  
  
	
	//Check the list of mat item no got duplicate item no------------------------------------------------
	var table = document.getElementById('itemsTable');
	var rowCount = table.rows.length;  
	var mylist = new Array();	    

	for (var j = 1; j < rowCount; j++){

	    var idrowItem = "procofrm"+j;
        var rowItem = document.getElementById(idrowItem).value;	 
        if (rowItem != ""){ 
        	mylist.push(rowItem);   
	    }		
    }		
	
	mylist.sort();
	var last = mylist[0];
	
	for (var i=1; i < mylist.length; i++) {
		if (mylist[i] == last){ 
			alert ("Duplicate Item Found; " + last);
			 return false;
		}	
		last = mylist[i];
	}
	//---------------------------------------------------------------------------------------------------   
  
}

function deleteRow(tableID) {
	try {
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
         
        if (rowCount > 2){
             table.deleteRow(rowCount - 1);
        }else{
             alert ("No More Row To Remove");
        }
	}catch(e) {
		alert(e);
	}

}

function onhand_checking(str)
{
 
 var rand = Math.floor(Math.random() * 101);
 var iteminfo = document.getElementById("procofrm"+str).value;
 var ordqty = document.getElementById("issueqtyid"+str).value;

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
      //alert (xmlhttp.responseText);
      var onhand = xmlhttp.responseText;
      if(parseInt(onhand) < parseInt(ordqty)) {  
         alert ("Insufficient Balance : "+onhand);
         document.getElementById("issueqtyid"+str).focus;
       }     
    }
  }
xmlhttp.open("GET","getonhand.php?i="+iteminfo+"&m="+rand,true);
xmlhttp.send();
}


</script>
</head>

<body onload= "setup()">
  <?php include("../topbarm.php"); ?> 
  <!--<?php include("../sidebarm.php"); ?>--> 

  <div class="contentc">

	<fieldset name="Group1" style=" width: 857px;" class="style2">
	 <legend class="title">UNPOST D/O</legend>
	  <br>	 
	  
	  <form name="InpJobFMas" method="POST" action="<?php echo $_SERVER['PHP_SELF'].'?menucd='.$var_menucode; ?>" onsubmit="return validateForm()">
	   
		<table style="width: 886px">
	   	   <tr>
	  	   <td></td>
	  	   <td style="width: 126px"></td>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 239px"></td>
	  	  </tr>
	  	  	  	  		  	
	  	  </table>
		 
		  <br><br>
		  <table id="itemsTable" class="general-table" style="width: 841px">
          	<thead>
          	 <tr>
              <th class="tabheader">#</th>
              <th class="tabheader">Delivery Number</th>
              <th class="tabheader">Remarks</th>
             </tr>
            </thead>
            <tbody>
            
             <tr class="item-row">
                <td style="width: 30px">
				<input name="seqno[]" id="seqno" value="1" readonly="readonly" style="width: 27px; border:0;"></td>
                <td>
				<input name="procofrm[]" value="" tProItem1=1 id="procofrm1" tabindex="0" class="autosearch" style="width: 100px" onchange ="upperCase(this.id)"></td>
				<td>
				<input name="rmk[]" value="" class="tInput" id="rmk" style="width: 300px" ;"></td>  
             </tr>
            </tbody>
           </table>
           
         &nbsp;

	
		 <table>
		  	<tr>
				<td style="width: 875px; height: 22px;" align="center">
				<?php
				 $locatr = "m_unpost_do.php?menucd=".$var_menucode;
			
				 echo '<input type="button" value="Back" class="butsub" style="width: 60px; height: 32px" onclick="location.href=\''.$locatr.'\'" >';
					include("../Setting/btnsave.php");
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
