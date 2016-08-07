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
		$refno    = $_POST['refno'];
		$prorevdte= date('Y-m-d', strtotime($_POST['prorevdte']));
		$remark   = $_POST['remark'];
	            
            
		if ($refno <> "") {
		
			$sysno = '';
     		$sqlchk = " select noctrl from ctrl_sysno ";
     		$sqlchk.= " where `descrip` = 'INVTADJ' and counter = 'HQ';";
     
     		$dumsysno= mysql_query($sqlchk) or die(mysql_error());
     		while($row = mysql_fetch_array($dumsysno))
     		{
     			$sysno = $row['noctrl'];        
     		}
     		if ($sysno ==NULL)
     		{
     			$sysno = '0';
     					$sysno_sql = "INSERT INTO ctrl_sysno values ('INVTADJ', 'HQ', 1)";

     			mysql_query($sysno_sql);

     		}
     		$newsysno = $sysno + 1;
     		
     		$adj_sysno  = str_pad($newsysno , 4, '0', STR_PAD_LEFT);
     		$adj_sysno = "ADJ".$adj_sysno;


         	$vartoday = date("Y-m-d H:i:s");
				$sql = "INSERT INTO invtadj values 
						('$adj_sysno', '$refno','$prorevdte', '$remark', 
						 '$var_loginid', '$vartoday','$var_loginid', '$vartoday', 'A', 'N')";
				mysql_query($sql);
				
				if(!empty($_POST['procomat']) && is_array($_POST['procomat'])) 
				{	
					foreach($_POST['procomat'] as $row=>$matcd ) {
						$matcode    = $matcd;
						$seqno      = $_POST['seqno'][$row];
						$matdesc    = mysql_real_escape_string($_POST['procodesc'][$row]);
						$matuom     = mysql_real_escape_string($_POST['procouom'][$row]);
						$physicalqty  = $_POST['issueqty'][$row];
						$onhandbal  = $_POST['procomark'][$row];
            $adjqty     = $_POST['adjqty'][$row];
											
						if ($matcode <> "")
						{
							if ($physicalqty== ""){ $physicalqty= 0;}
							if ($onhandbal== ""){ $onhandbal= 0;}
              if ($adjqty== ""){ $adjqty= 0;}
							//$adjqty = $physicalqty - $onhandbal;
							//$negadjqty = 0 - $adjqty;
							
							$sql = "INSERT INTO invtadjdet values 
						    		('$adj_sysno', '$seqno', '$matcode', '$matdesc', '$matuom','$adjqty','$onhandbal', '$physicalqty')";
							mysql_query($sql) or die("Can't Insert Transaction ".mysql_error());
							
							/*
							$sql = "INSERT INTO rawmat_tran values 
						    		('ADJ', '$adj_sysno', '$refno','$remark', '$prorevdte', '$matcode', '0', '$matdesc', '$onhandbal', '$adjqty ','$var_loginid', '$vartoday','$var_loginid', '$vartoday')";
							mysql_query($sql) or die("Can't Insert History ".mysql_error());
               */
               
           				}	
					}
				}
				
				$updsysno_sql = "UPDATE ctrl_sysno SET noctrl = '$newsysno' where `descrip` = 'INVTADJ' and counter = 'HQ'";

		     	 mysql_query($updsysno_sql);
				
				$backloc = "../invt/m_adj_mas.php?menucd=".$var_menucode;
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
<script  type="text/javascript" src="jq-adj-script.js"></script>


<script type="text/javascript"> 

function setup() {

		document.InpJobFMas.refnoid.focus();
						
 		//Set up the date parsers
        var dateParser = new DateParser("dd-MM-yyyy");
      
		//Set up the DateMasks
		var errorMessage = "Invalid date: ${value}. Expected format: ${mask}";
		var dateMask1 = new DateMask("dd-MM-yyyy", "prorevdte");
		dateMask1.validationMessage = errorMessage;		
}

$(document).ready(function(){
	var ac_config = {
		source: "autocomscrpro1.php",
		select: function(event, ui){
			$("#prod_code").val(ui.item.prod_code);
			$("#promodesc").val(ui.item.prod_desc);
			$("#totallabcid").val(ui.item.prod_labcst);
	
		},
		minLength:1
		
	};
	$("#prod_code").autocomplete(ac_config);
});

function upperCase(x)
{
var y=document.getElementById(x).value;
document.getElementById(x).value=y.toUpperCase();
}

function getCost(vid)
{   
    var vphyqtyid = "issueqtyid"+vid;  
    var vonhandid = "procomark"+vid;
    var vadjqtyid = "adjqtyid"+vid;	
    var col1 = document.getElementById(vphyqtyid).value;
    var onhand = document.getElementById(vonhandid).value;
     
	if (col1 != ""){
     
		if(isNaN(col1)) {
    	   alert('Please Enter a valid number for Adjust Qty:' + col1);
    	   col1 = 0;
    	}
      //alert (col1+" "+onhand); 
      var adjqty = col1 - onhand; 
      //alert (col1+" "+onhand+" "+adjqty);  adjqtyid1
      //alert(vadjqtyid);
    	document.getElementById(vphyqtyid).value = parseInt(col1);
      document.getElementById(vadjqtyid).value = parseInt(adjqty);
      
    }
  
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

    var x=document.forms["InpJobFMas"]["refno"].value;
	if (x==null || x=="")
	{
	alert("Ref No Must Not Be Blank");
	document.InpJobFMas.refno.focus();
	return false;
	}

    var x=document.forms["InpJobFMas"]["prorevdte"].value;
	if (x==null || x=="")
	{
	alert("Date Must Not Be Blank");
	document.InpJobFMas.prorevdte.focus();
	return false;
	}
	
	// to chk return qty is not more than onhand qty---------------------------------------------------
	var table = document.getElementById('itemsTable');
	var rowCount = table.rows.length;  
	var mylist = new Array();	  

	for (var j = 1; j < rowCount; j++){
	    var idrowItem = "procomat"+j; // raw mat item
        var rowItem = document.getElementById(idrowItem).value;	 
        
        var idrowItem2 = "adjqtyid"+j; // issue qty
        var rowItem2 = document.getElementById(idrowItem2).value;	
        
        var idrowItem3 = "procomark"+j; //onhand qty
        var rowItem3 = document.getElementById(idrowItem3).value;

		//if (parseFloat(rowItem3) < 0 ){			
		//	alert ("Onhand Balance For Item " + rowItem + " is Negative");		   
		//    return false;
		//}
		
		//if (parseFloat(rowItem3) == 0 ){			
		//	alert ("Cannot adj Item " +rowItem + ". Onhand Balance is ZERO ");		   
		//    return false;
		//}		
		
		if (parseFloat(rowItem2) == 0 ){			
			alert ("adj Qty Cannot Be ZERO For Item : " +rowItem);		   
		    return false;
		}	       
       
		//if (parseFloat(rowItem2) > parseFloat(rowItem3) ){			
		//	alert ("adj Qty Cannot More Than Onhand Balance For Item : " + rowItem);		   
		//    return false;
		//}	
    }
    //---------------------------------------------------------------------------------------------------


	//Check the list of mat item no got duplicate item no------------------------------------------------
	var table = document.getElementById('itemsTable');
	var rowCount = table.rows.length;  
	var mylist = new Array();	    

	for (var j = 1; j < rowCount; j++){

	    var idrowItem = "procomat"+j;
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
 var iteminfo = document.getElementById("procomat"+str).value;
 //var iteminfo = str;
 
 //alert (iteminfo);

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
      //alert ("Insufficient Balance : "+onhand);
      document.getElementById("procomark"+str).value = onhand;    
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
	 <legend class="title">STOCK ADJUSTMENT</legend>
	  <br>	 
	  
	  <form name="InpJobFMas" method="POST" action="<?php echo $_SERVER['PHP_SELF'].'?menucd='.$var_menucode; ?>" onsubmit="return validateForm()">
	   
		<table style="width: 886px">
	   	   <tr>
	   	    <td></td>
	  	    <td style="width: 126px">Ref No.</td>
	  	    <td style="width: 13px">:</td>
	  	    <td style="width: 239px">
			<input class="inputtxt" name="refno" id="refnoid" type="text" maxlength="10" style="width: 84px;" onchange ="upperCase(this.id)" tabindex="0">
			</td>
			<td style="width: 29px"></td>
			<td style="width: 136px">Date</td>
			<td style="width: 16px">:</td>
			<td style="width: 270px">
		   <input class="inputtxt" name="prorevdte" id ="prorevdte" type="text" style="width: 128px;" value="<?php  echo date("d-m-Y"); ?>" tabindex="0" >
		   <img alt="Date Selection" src="../images/cal.gif" onclick="javascript:NewCssCal('prorevdte','ddMMyyyy')" style="cursor:pointer">
		   </td>
	  	  </tr>  
	  	  <tr>
	  	   <td></td>
	  	   <td style="width: 126px"></td>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 239px"></td>
	  	  </tr>
	  	  <tr>
	   	    <td></td>
	  	    <td style="width: 126px">Remark</td>
	  	    <td style="width: 13px">:</td>
	  	    <td colspan="5">
			<input class="inputtxt" name="remark" id="remark" type="text" maxlength="100" style="width: 634px;" onchange ="upperCase(this.id)" tabindex="0">
			</td>
	  	  </tr> 
	  	  		  	
	  	  </table>
		 
		  <br><br>
		  <table id="itemsTable" class="general-table" style="width: 841px">
          	<thead>
          	 <tr>
              <th class="tabheader">#</th>
              <th class="tabheader">Product Code</th>
              <th class="tabheader">Description</th>
              <th class="tabheader">UOM</th>
              <th class="tabheader">Onhand Bal</th>              
              <th class="tabheader">Physical Qty</th>              
              <th class="tabheader">Adjustment Qty</th>
             </tr>
            </thead>
            <tbody>
            
             <tr class="item-row">
                <td style="width: 30px">
				<input name="seqno[]" id="seqno" value="1" readonly="readonly" style="width: 27px; border:0;"></td>
                <td>
				<input name="procomat[]" value="" tProItem1=1 id="procomat1" tabindex="0" class="autosearch" style="width: 161px" onchange ="upperCase(this.id);"></td>
                <td>
				<input name="procodesc[]" value="" class="tInput" id="procodesc1" readonly="readonly" style="border-style: none; border-color: inherit; border-width: 0; width: 303px;"></td>
                <td>
				<input name="procouom[]" value="" class="tInput" id="procouom1" readonly="readonly" style="border-style: none; border-color: inherit; border-width: 0; width: 48px;"></td>
                <td>
                <input name="procomark[]" value="0" class="tInput" tMark="1" id="procomark1" readonly="readonly"  style="width: 75px; border-width: 0;"> </td>
				<td>
				<input name="issueqty[]" value="0" class="tInput" id="issueqtyid1" onBlur="getCost(1);" style="width: 75px"></td>  
				<td>
				<input name="adjqty[]" value="0" class="tInput" id="adjqtyid1" readonly="readonly" style="border-style: none; border-color: inherit; border-width: 0; width: 48px;"></td>  
             </tr>
            </tbody>
           </table>
           
         <a href="#" id="addRow" class="button-clean large"><span><img src="../images/icon-plus.png" alt="Add" title="Add Row"> Add Item</span></a>
         <a href="javascript:deleteRow('itemsTable')" id="addRow" class="button-clean large"><span><img src="../images/icon-minus.png" alt="Add" title="Delete Row">Delete Row</span></a>

	
		 <table>
		  	<tr>
				<td style="width: 1150px; height: 22px;" align="center">
				<?php
				 $locatr = "m_adj_mas.php?menucd=".$var_menucode;
			
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
