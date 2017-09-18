<?php

	include("../Setting/Configifx.php");
	include("../Setting/Connection.php");
	$var_loginid = $_SESSION['sid'];
	
	set_time_limit(0);
    
    if($var_loginid == "") { 
      echo "<script>";   
      echo "alert('Not Log In to the system');"; 
      echo "</script>"; 

      echo "<script>";
      echo 'top.location.href = "../index.php"';
      echo "</script>";
    } else {
    
      $var_ordno = $_GET['sorno'];
      $var_menucode = $_GET['menucd'];
      include("../Setting/ChqAuth.php");
    }
    
    if ($_POST['Submit'] == "Update") {
    
   	    $vmordno   = $_POST['sordno'];
		$vmorddte = date('Y-m-d', strtotime($_POST['saorddte']));
		$vmcustcd = $_POST['sacustcd'];
		$vmmthyr  = $_POST['samthyr'];
		$vmperiod = $_POST['speriod'];
        $vmlessamt = $_POST['lessamt'];  
        $vmlesstype = $_POST['lesstype'];           
            
		if ($vmordno <> "") {
    			
        $vartoday = date("Y-m-d H:i:s"); 
        
				//$sql = "Update csalesmas Set scustcd = '$vmcustcd', sorddte ='$vmorddte', ";
				$sql = "Update csalesmas Set sorddte ='$vmorddte', ";
                $sql .= " less_type = '$vmlesstype', less_amt = '$vmlessamt', ";
				$sql .= " smthyr = '$vmmthyr', speriod = '$vmperiod', ";
				$sql .= " modified_by = '$var_loginid', modified_on='$vartoday ' ";
				$sql .= " Where sordno ='$vmordno'";
        
				mysql_query($sql) or die ("Cant update : ".mysql_error());
        
				$sql =  "Delete From csalesdet";
				$sql .= "  Where sordno ='$vmordno'";
				
				mysql_query($sql) or die ("Cant delete details : ".mysql_error());        
				
				if(!empty($_POST['prococode']) && is_array($_POST['prococode'])) 
				{	
					foreach($_POST['prococode'] as $row=>$matcd ) {
						$matcode     = $matcd;
						$matseqno    = $_POST['seqno'][$row];
						$matupri     = $_POST['procoupri'][$row];
            			$mattype     = $_POST['procotype'][$row];
						$matdoqty    = $_POST['procodoqty'][$row];
						$matsoldqty  = $_POST['procosoldqty'][$row];
						$matrtnqty   = $_POST['procortnqty'][$row];
						$matshortqty = $_POST['procoshortqty'][$row];
						$matoverqty  = $_POST['procooverqty'][$row];
						$matadjqty   = $_POST['procoadjqty'][$row];
						//$matendbal   = $_POST['procobalqty'][$row];
						$openingqty  = $_POST['opening'][$row];
						
                     $matendbal = $openingqty + $matdoqty - $matsoldqty - $matrtnqty - $matshortqty + $matoverqty + $matadjqty;       
                     if ($matendbal == "" or empty($matendbal)){$matendbal = 0;} 							
						
					
						if ($matcode <> "")
						{
							if ($matrtnqty ==NULL) { $matrtnqty =0; }
							if ($matdoqty==NULL) { $matdoqty=0; }
							if ($matsoldqty==NULL) { $matsoldqty =0; }
							if ($matshortqty==NULL) { $matshortqty=0; }
							if ($matoverqty==NULL) { $matoverqty=0; }
							if ($matadjqty==NULL) { $matadjqty=0; }
							if ($matadjqty==NULL) { $matadjqty=0; }
							if ($openingqty==NULL) { $openingqty=0; }

							$sql = "INSERT INTO csalesdet values 
						    		('$vmordno', '$matcode', '$matupri', '$mattype', '$matdoqty','$openingqty', 
						    		 '$matsoldqty', '$matrtnqty', '$matshortqty', '$matoverqty', '$matadjqty', 
						    		 '$matendbal', '$matseqno')";
                    
							//mysql_query($sql) or die ("Cant insert 2 : ".mysql_error());
							mysql_query($sql) or die("Error in csalesdet :".mysql_error(). ' Failed SQL is --> '. $sql);
           				}	
					}
				}
				
				$backloc = "../cons/m_csales_mas.php?menucd=".$var_menucode;
           		echo "<script>";
           		echo 'location.replace("'.$backloc.'")';
            	echo "</script>"; 	
					
		}else{
			$backloc = "../cons/upd_csales.php?stat=4&menucd=".$var_menucode;
           	echo "<script>";
           	echo 'location.replace("'.$backloc.'")';
            echo "</script>"; 
		}  
    }  
 if ($_POST['printgrn'] == "Print Goods Return Note") {
        $grnno = $_POST['grnno'];
        $grndate = $_POST['grndate'];
               
        #----------------------------------------------------------
        $sql = "Delete from tmpinvform where usernm = '$var_loginid'";
        mysql_query($sql) or die ("Cant Delete 1 : ".mysql_error());
        $sql = "Delete from tmpinvformtot where username = '$var_loginid'";
        mysql_query($sql) or die ("Cant Delete 2 : ".mysql_error());
        
        $sqlu = "update csalesmas set grn_no = '$grnno' where sordno = '$var_ordno'";
        mysql_query($sqlu) or die ("Cant Update 3 : ".mysql_error());
        
        $sqlu = "update csalesmas set grn_date = '$grndate' where sordno = '$var_ordno'";
        mysql_query($sqlu) or die ("Cant Update 3 : ".mysql_error());
        
        $sql = "select scustcd from csalesmas where sordno = '$var_ordno'";
        $sql_result = mysql_query($sql);
        $row = mysql_fetch_array($sql_result);
        $custcode = mysql_real_escape_string($row['scustcd']);
                     
        $sql1 = "select * from csalesdet where endbal > 0 and sordno = '$var_ordno'";
        $rs_result1 = mysql_query($sql1);
        while ($rowq1 = mysql_fetch_assoc($rs_result1)){
            $spro = htmlentities($rowq1['sprocd']);           
            $suni = $rowq1['sprounipri'];
            $sqty = $rowq1['endbal'];
                          
            $sql2 = "select sprounipri, sptype from salesentrydet where sordno = '$sord' and sprocd = '$spro'";
            $sql_result2 = mysql_query($sql2);
            $row2 = mysql_fetch_array($sql_result2);
            $suni2 = htmlentities($row2['sprounipri']);
            $ssty = htmlentities($row2['sptype']);
            
            if(empty($sqty)){$sqty = 0;}
            if(empty($suni)){$suni = 0;}
            $samt = $suni * $sqty;
            
            $sql2 = "select description, exunit from product where groupcode = '$spro'";
            $sql_result2 = mysql_query($sql2);
            $row2 = mysql_fetch_array($sql_result2);
            $spde = mysql_real_escape_string($row2['description']);
            $suom = htmlentities($row2['exunit']);
            
            $sql3 = "select salestype_desc from salestype_master where salestype_code = '$ssty'";
            $sql_result3 = mysql_query($sql3);
            $row3 = mysql_fetch_array($sql_result3);
            $sstde = htmlentities($row3['salestype_desc']);
            
            $sql4 = "select disctype, discamt from salesentrydisct where sordno = '$sord' and sptype = '$ssty'";
            $sql_result4 = mysql_query($sql4);
            $row4 = mysql_fetch_array($sql_result4);
            $disctyp = htmlentities($row4['disctype']);
            $discamt = htmlentities($row4['discamt']);
            if(empty($discamt)){$discamt = 0;}
            
            if ($disctyp == ''){
                $disdes = "Discount";
            }else{
                if ($disctyp == '1'){
                    $disdes = "Discount (%)";
                }else{
                    $disdes = "Discount (RM)";
                }
            }
            
            if (!empty($spro)){
                $sqli  = "insert into tmpinvform values ('$spro', '$spde', '$sqty', '$suom', '$suni', ";
                $sqli .= "        '$samt', '$ssty', '$sstde', '$discamt', '$disctyp', '$var_loginid', '$disdes', '0')";
                //echo $sqli."<br>";
                mysql_query($sqli) or die ("Cant Insert 1 : ".mysql_error());
            }
        }
               
        #----------------------------------------------------------
        $sql = "select distinct salestyp, stypdisv, stypcat from tmpinvform where usernm = '$var_loginid'";
        $rs_result = mysql_query($sql);
        while ($rowq = mysql_fetch_assoc($rs_result)){
            $styp    = htmlentities($rowq['salestyp']);
            $stypdis = htmlentities($rowq['stypdisv']);
            $scat    = htmlentities($rowq['stypcat']);
            
            if (empty($scat)){
                $sqli  = "update tmpinvform set netstypdisv = '0' ";
                $sqli .= " where salestyp = '$styp' and usernm = '$var_loginid'";
                mysql_query($sqli) or die ("Cant Update 1 : ".mysql_error());
            }else{
                if ($scat == '2'){
                    $sqli  = "update tmpinvform set netstypdisv = '$stypdis' ";
                    $sqli .= " where salestyp = '$styp' and usernm = '$var_loginid'";
                    mysql_query($sqli) or die ("Cant Update 2 : ".mysql_error());
                }else{
                    $sqldd  = "select sum(amt) from tmpinvform where salestyp = '$styp' and usernm = '$var_loginid'";
                    $sql_resultdd = mysql_query($sqldd);
                    $rowdd = mysql_fetch_array($sql_resultdd);
                    $dsalestypc = htmlentities($rowdd['sum(amt)']);
                    
                    $discamt = $dsalestypc * ($stypdis / 100);
                    $sqli  = "update tmpinvform set netstypdisv = '$discamt' ";
                    $sqli .= " where salestyp = '$styp' and usernm = '$var_loginid'";
                    mysql_query($sqli) or die ("Cant Update 3 : ".mysql_error());
                }
            }
        }
        //$sqldd  = "select discount, sec_disct, add_deduction, freight, transport, gst from invmas where invno = '$pinvno'";
        //$sql_resultdd = mysql_query($sqldd);
        //$rowdd = mysql_fetch_array($sql_resultdd);
        $fdis    = 0;//$rowdd['discount'];
        $secdis  = 0;//$rowdd['sec_disct'];
        $deduper = 0;//$rowdd['add_deduction'];
        $frie    = 0;//$rowdd['freight'];
        $trans   = 0;//$rowdd['transport'];
        $gstper  = 0;//$rowdd['gst'];
        if (empty($fdis)){$fdis = 0;}
        if (empty($secdis)){$secdis = 0;}
        if (empty($deduper)){$deduper = 0;}
        if (empty($frie)){$frie = 0;}
        if (empty($trans)){$trans = 0;}
        if (empty($gstper)){$gstper = 0;} 
        
        $sqldd  = "select sum(amt) from tmpinvform where usernm = '$var_loginid'";
        $sql_resultdd = mysql_query($sqldd);
        $rowdd = mysql_fetch_array($sql_resultdd);
        $sumamt = htmlentities($rowdd['sum(amt)']);
        if (empty($sumamt)){$sumamt = 0;}
        
        $sqldd  = "select sum(distinct netstypdisv) from tmpinvform where usernm = '$var_loginid' and netstypdisv <> 0";
        $sql_resultdd = mysql_query($sqldd);
        $rowdd = mysql_fetch_array($sql_resultdd);
        $disamt = htmlentities($rowdd['sum(distinct netstypdisv)']);
        if (empty($disamt)){$disamt = 0;}
        
        $netamt = $sumamt - $disamt;
        $gamt = $netamt;
        if ($fdis <> 0){
            $fdisamt = ($fdis /100) * $netamt;
            $netamt = $netamt - $fdisamt;
        }
        if (empty($fdisamt)){$fdisamt = 0;}
        if($secdis <> 0){
            $sdisamt = ($secdis /100) * $netamt;
            $netamt = $netamt - $sdisamt;
        }
        if (empty($sdisamt)){$sdisamt = 0;}
        if ($deduper <> 0){
            $deduamt = ($deduper /100) * $netamt;
            $netamt = $netamt - $deduamt;
        }
        if (empty($deduamt )){$deduamt  = 0;}
        if ($frie <> 0){
            $netamt = $netamt + $frie;
        }
        if ($trans <> 0){
            $netamt = $netamt + $trans;
        }
        $gstamt = $gstper * $netamt / 100;
        $netamt = $netamt + $gstamt;
        $sqli  = "insert into tmpinvformtot values ('$gamt', '$var_loginid', '$fdis', '$fdisamt', '$secdis',";
        $sqli .= " '$sdisamt', '$deduper', '$deduamt', '$frie', '$trans', '$gstper', '$gstamt', '$netamt')";
        mysql_query($sqli) or die ("Cant Update 3 : ".mysql_error());       
        
        $sqldd  = "select apphea_txt from apphea_set";
        $sql_resultdd = mysql_query($sqldd);
        $rowdd = mysql_fetch_array($sql_resultdd);
        $comphea = htmlentities($rowdd['apphea_txt']);
        $fname = "goodsreturnnote.rptdesign&__title=myReport";
        
        $dest = "http://".$var_prtserver.":8080/birtfg/frameset?__report=".$fname."&grnno=".$grnno."&grndate=".$grndate."&custcode=".$custcode."&menuc=".$var_menucode."&dbsel=".$varrpturldb."&usernm=".$var_loginid;
        $dest .= urlencode(realpath($fname));
        
        echo "<script language=\"javascript\">window.open('".$dest."','name','height=1000,width=1000,left=200,top=200');</script>";
    }
 /*   
  //----------- get special authority ------------------//
  $sqlauth = " select * from progauth";
  $sqlauth .= " where username = '$var_loginid'";
  $sqlauth .= " and program_name = '99'";
  
  $tmpauth = mysql_query($sqlauth) or die ("Cant get auth : ".mysql_error());
  
  if (mysql_numrows($tmpauth) > 0) {
     $speauth = "Y";
  } else {  $speauth = "N"; }
 */
        
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


<!-- Our jQuery Script to make everything work -->
<script  type="text/javascript" src="jq-ac-script.js"></script>


<script type="text/javascript"> 
$(document).ready(function(){
	var ac_config = {
		source: "autocomscrpro1.php",
		select: function(event, ui){
			$("#prod_code").val(ui.item.prod_code);
			//$("#promodesc").val(ui.item.prod_desc);
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

function setup() {

		document.InpPO.sacustcd.focus();
				
 		//Set up the date parsers
        var dateParser = new DateParser("dd-MM-yyyy");
        
		//Set up the DateMasks
		var errorMessage = "Invalid date: ${value}. Expected format: ${mask}";
		var dateMask1 = new DateMask("dd-MM-yyyy", "saorddte");
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

  var x=document.forms["InpPO"]["sacustcd"].value;
	if (x==null || x=="s")
	{
	alert("Customer Must Not Be Blank");
	document.InpPO.sacustcd.focus;
	return false;
	}

   var x=document.forms["InpPO"]["saorddte"].value;
	if (x==null || x=="")
	{
	alert("Order Date Must Not Be Blank");
	document.InpPO.saorddte.focus;
	return false;
	}

  
	//Check the list of mat item no is Valid-------------------------------------------------------------
	var flgchk = 1;	
	var table = document.getElementById('itemsTable');
	var rowCount = table.rows.length;  
         
    for (var j = 1; j < rowCount; j++){
       var idrowItem = "prococode"+j;
       var rowItem = document.getElementById(idrowItem).value;	 
              
       if (rowItem != ""){
       	var strURL="aja_chk_subCodeCount.php?rawmatcdg="+rowItem;
	   	var req = getXMLHTTP();
        if (req)
	  	{
			req.onreadystatechange = function()
			{
				if (req.readyState == 4)
				{
					// only if "OK"
					if (req.status == 200)
					{
						if (req.responseText == 0)
						{
						   flgchk = 0;
						   alert ('Invalid Item : '+ rowItem + ' At Row '+j);
						   return false;
						}
					} else {
						//alert("There was a problem while using XMLHTTP:\n" + req.statusText+req.status);
						return false;
					}
				}
			}	 
		  }
		
		  req.open("GET", strURL, false);
		  req.send(null);
	    }	  
    }
     if (flgchk == 0){
	   return false;
	}
    //---------------------------------------------------------------------------------------------------

    
	//Check the list of mat item no got duplicate item no------------------------------------------------
	var table = document.getElementById('itemsTable');
	var rowCount = table.rows.length;  
	var mylist = new Array();	    

	for (var j = 1; j < rowCount; j++){

	    var idrowItem = "prococode"+j;
        var rowItem = document.getElementById(idrowItem).value;	 
        if (rowItem != ""){ 
        	mylist.push(rowItem);   
	    }		
    }		
	
	mylist.sort();
	var last = mylist[0];
	
	for (var i=1; i < mylist.length; i++) {
		if (mylist[i] == last){ 
			//alert ("Duplicate Item Found; " + last);
			// return false;
		}	
		last = mylist[i];
	}   
	//---------------------------------------------------------------------------------------------------
	
	
	//Check input price is Valid-------------------------------------------------------
	var table = document.getElementById('itemsTable');
	var rowCount = table.rows.length;  
	
	  for (var j = 1; j < rowCount; j++){

	    var idrowbook = "procoupri"+j;
        var rowItemc = document.getElementById(idrowbook).value;	 
        
        if (rowItemc != ""){ 
        	if(isNaN(rowItemc)) {
    	   		alert('Please Enter a valid number for Unit Price :' + rowItemc + " Line No :"+j);
    	   		document.itemsTable.idrowbook.focus();
    	   		return false;
    	    }    
    	}
       }		
    //---------------------------------------------------------------------------------------------------
  

}

function validateGRN()
{

  var x=document.forms["printgrn"]["grnno"].value;
	if (x==null || x=="")
	{
    	alert("GRN no Not Be Blank");
    	document.printgrn.grnno.focus;
    	return false;
	}

   var x=document.forms["printgrn"]["grndate"].value;
	if (x==null || x=="")
	{
	alert("GRN Date Must Not Be Blank");
	document.printgrn.grndate.focus;
	return false;
	}
}

function deleteRow(tableID) {
	try {
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length-1;
         
        if (rowCount > 2){
             table.deleteRow(rowCount - 1);
        }else{
             alert ("No More Row To Remove");
        }
	}catch(e) {
		alert(e);
	}
  
}


function showoveDecimal(vterms){
 	if (vterms != ""){
		if(isNaN(vterms)) {
    	   alert('Please Enter a number for Terms :' + vterms);
    	   document.InpPO.terms.focus();
    	   return false;
    	}
    }
}

function getUprice(str)
{

 var rand = Math.floor(Math.random() * 101);
 var custinfo = document.getElementById("sacustcd").value;
 var iteminfo = document.getElementById("prococode"+str).value;
 var domthyr  = document.getElementById("samthyrid").value;

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
    var priamt = xmlhttp.responseText;
    
    var result = priamt.split("~");
    //alert (result[0]+" : "+result[1]+" : "+result[2]);
    document.getElementById("procotype"+str).value=result[0];   
    document.getElementById("procoupri"+str).value=result[1]; 
    document.getElementById("opening"+str).value=result[4];
    document.getElementById("procodoqty"+str).value=result[2];
    document.getElementById("begbal"+str).value=result[3];     
    }
  }
xmlhttp.open("GET","getsalesprice.php?s="+custinfo+"&i="+iteminfo+"&d="+domthyr+"&m="+rand,true);
xmlhttp.send();
}

function getLess()
{

 var rand = Math.floor(Math.random() * 101);
 var custinfo = document.getElementById("sacustcd").value;

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
    var lessflg = xmlhttp.responseText;
    lessflg = lessflg.replace(/\s+/g, "");

    //alert("K"+lessflg+"K");
    
    if (lessflg == 'N') {
      document.getElementById("lessamtid").value=0;   
      document.getElementById("lessamtid").readonly=true; 
      } else {
      document.getElementById("lessamtid").value="";   
      //document.getElementById("lessamt").readonly=true;       
      } 
    }
  }
xmlhttp.open("GET","getless.php?s="+custinfo+"&m="+rand,true);
xmlhttp.send();
}

function getamt (str) {

	 var soldqty = document.getElementById("procosoldqty"+str).value;
	 var price = document.getElementById("procoupri"+str).value;
	 
	 var totamt = soldqty * price; 
	    
	 document.getElementById("procosamt"+str).value = totamt.toFixed(2);
	 getTotalAmt()
	}

function getTotalAmt() {
	var gtot = 0;   
	var table = document.getElementById('itemsTable');
	var rowCount = table.rows.length-1;  
	for (var j = 1; j <= rowCount; j++){
	    var idsubamt = "procosamt"+j;
        var subamtobj = document.getElementById(idsubamt);
        if (subamtobj) {
        	var subamt = subamtobj.value;
        	if (subamt !== "") { 
          		subamt = parseFloat(subamt); 
        		gtot = gtot + subamt; 
        	}
	    }		
    }	
    document.getElementById("gtot").value = gtot.toFixed(2);
}

function getbal (str) {

  var begbal = parseInt(document.getElementById("begbal"+str).value);
  var opening = parseInt(document.getElementById("opening"+str).value);
  var doqty = parseInt(document.getElementById("procodoqty"+str).value);
  var soldqty = parseInt(document.getElementById("procosoldqty"+str).value);
  var rtnqty = parseInt(document.getElementById("procortnqty"+str).value);
  var shortqty = parseInt(document.getElementById("procoshortqty"+str).value);
  var overqty = parseInt(document.getElementById("procooverqty"+str).value);
  var adjqty = parseInt(document.getElementById("procoadjqty"+str).value);
  
  
  if (isNaN(begbal)) { begbal = 0; }
  if (isNaN(opening)) { opening = 0; }
  if (isNaN(doqty)) { doqty = 0; }
  if (isNaN(soldqty)) { soldqty = 0; }
  if (isNaN(rtnqty)) { rtnqty = 0; }
  if (isNaN(shortqty)) { shortqty = 0; }
  if (isNaN(overqty)) { overqty = 0; }
  if (isNaN(adjqty)) { adjqty = 0; }
  
  //alert ("Beg: "+begbal+"DO :"+doqty+"Sold : "+soldqty+"Ret : "+rtnqty+"short : "+shortqty+"Over : "+overqty+"Adj :"+adjqty)
  var endbal = opening + doqty - soldqty - rtnqty - shortqty + overqty + adjqty;
  var endbalobj = document.getElementById("procobalqty"+str);
  if (endbalobj) {
	  endbalobj.value = endbal;
  }
 
}


</script>
</head>

<body onload="setup()">
	  <?php include("../topbarm.php"); ?> 
<!--  <?php include("../sidebarm.php"); ?> -->

  <?php
  	 $sql = "select * from csalesmas";
     $sql .= " where sordno ='".$var_ordno."'";
     $sql_result = mysql_query($sql);
     $row = mysql_fetch_array($sql_result);

     $custcd = $row['scustcd'];
     $orddte = date('d-m-Y', strtotime($row['sorddte']));
     $mthyr = $row['smthyr'];
     $period = $row['speriod'];
     $lessamt = $row['less_amt'];
     $lesstype = $row['less_type'];
     $grnno = $row['grn_no'];
     $grndate = $row['grn_date'];
     
     if(empty($grnno)){$grnno = "";}
     if(empty($grndate)){$grndate = date("d-m-Y");}
     
     $sql = " select pro_less from counter ";
     $sql .= " where counter = '$custcd'";
     
     $result = mysql_query($sql) or die ("Error proless : ".mysql_error());
     
     if(mysql_numrows($result) >0) {
       $data = mysql_fetch_object($result); 
     
       $var_less = trim($data->pro_less);     
       if($var_less == "") { $var_less = "N"; }     
      } else { $var_less = "N"; }

  ?> 
  
  <div class="contentc">

	<fieldset name="Group1" style=" width: 973px;" class="style2">
	 <legend class="title">UPDATE COUNTER SALES ENTRY</legend>
	  <br>	 
	  
	  <form name="InpPO" method="POST" action="<?php echo $_SERVER['PHP_SELF'].'?menucd='.$var_menucode; ?>" onsubmit="return validateForm()">
	   
		<table style="width: 993px; ">
		  <tr>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 122px">Order No</td>
	  	   <td style="width: 13px">:</td>
	  	   <td style="width: 201px">
			<input class="textnoentry" name="sordno" id="sordnoid" type="text" readonly style="width: 204px;" value = "<?php echo $var_ordno; ?>">         
		   </td>
		   <td style="width: 10px"></td>
		   <td style="width: 204px">&nbsp;</td>
		   <td>&nbsp;</td>
		   <td style="width: 284px">
		   &nbsp;</td>
	  	  </tr>

	   	   <tr>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 122px">Customer</td>
	  	   <td style="width: 13px">:</td>
	  	   <td style="width: 201px">
		   	<select name="sacustcd" id="sacustcd" style="width: 268px" class="textnoentry" disabled="">
			 <?php
              $sql = "select custno, name from customer_master";
              $sql .= " where type = 'C'";
              $sql .= " ORDER BY custno ASC";
              $sql_result = mysql_query($sql);
              echo "<option size =30 selected></option>";
                       
			  if(mysql_num_rows($sql_result)) 
			  {
			   while($row = mysql_fetch_assoc($sql_result)) 
			   { 
				echo '<option value="'.$row['custno'].'"';
        if ($custcd == $row['custno']) { echo "selected"; }
        echo '>'.$row['custno']." | ".$row['name'].'</option>';
			   } 
		      } 
	         ?>				   
	       </select>

		   </td>
			<td style="width: 10px"></td>
			<td style="width: 204px">Key In Date</td>
			<td style="width: 16px">:</td>
			<td style="width: 284px">
		   <input class="inputtxt" name="saorddte" id ="saorddte" type="text" style="width: 128px;" value="<?php  echo $orddte; ?>">
		   <img alt="Date Selection" src="../images/cal.gif" onclick="javascript:NewCssCal('saorddte','ddMMyyyy')" style="cursor:pointer"></td>
	  	  </tr>  
	  	  <tr>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 122px"></td>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 201px"><div id="msgcd"></div></td>
	  	  </tr>
		  <tr>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 122px">MM/YYYY</td>
	  	   <td style="width: 13px">:</td>
	  	   <td style="width: 201px">
			<input class="textnoentry" readonly name="samthyr" id="samthyrid" type="text" maxlength="45" style="width: 100px;" value="<?php echo $mthyr; ?>"></td>		   
		   <td style="width: 10px"></td>
		   <td style="width: 204px">Period</td>
		   <td>:</td>
		   <td style="width: 284px">
		   	<select name="speriod" id="speriodcd" >
			 <?php
				echo '<option value="1"';
        if ($period == '1') { echo "selected"; }
        echo '>1 | FIRST HALF MONTH</option>';
        
				echo '<option value="2"';
        if ($period == '2') { echo "selected"; }
        echo '>2 | SECOND HALF MONTH</option>';        
 
	         ?>				   
	       </select>

		   </td>
		  </tr>
		  <tr>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 122px">Less</td>
	  	   <td style="width: 13px">:</td>
	  	   <td style="width: 201px">
	  	   <?php 
	  	   	if ($var_less == 'N'){
		   		echo '<select name="lesstype" id="lesstypecdx" disabled="">';
		   	}else{
		   		echo '<select name="lesstype" id="lesstypecdy" >';
		   	}
		   	?>
			 <?php
				echo '<option value="1"';
        if ($lesstype == '1') { echo "selected"; }
        echo '>NO</option>';
        
				echo '<option value="2"';
        if ($lesstype == '2') { echo "selected"; }
        echo '>%</option>'; 
        
				echo '<option value="3"';
        if ($lesstype == '3') { echo "selected"; }
        echo '>AMT</option>';                
                    
	         ?>				   
	       </select>         
         
			<input class="inputtxt" name="lessamt" id="lessamtid" type="text" maxlength="10" style="width: 50px;text-align : right" value="<?php echo $lessamt; ?>"
      <?php if($var_less == "N") { echo "readonly disable =''"; } ?>>		   
		     </td>
		   <td style="width: 10px"></td>
		   <td style="width: 204px">&nbsp;</td>
		   <td>&nbsp;</td>
		   <td style="width: 284px">
		   </td>
	  	  </tr>       
		  <tr>
	  	   <td style="width: 13px"></td>
	  	   <td style="width: 122px">&nbsp;</td>
	  	   <td style="width: 13px">&nbsp;</td>
	  	   <td style="width: 201px">
		   &nbsp;</td>
		   <td style="width: 10px"></td>
		   <td style="width: 204px">&nbsp;</td>
		   <td>&nbsp;</td>
		   <td style="width: 284px">
		   &nbsp;</td>
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
		 
		  <br><br>
		  <table id="itemsTable" class="general-table" style="width: 958px">
          	<thead>
          	 <tr>
              <th class="tabheader">#</th>
              <th class="tabheader">Group Code</th>
              <th class="tabheader">Unit Price</th>
              <th class="tabheader">Type</th>
              <th class="tabheader">Opening Qty</th>              
              <th class="tabheader">D/O Qty</th>              
              <th class="tabheader">Sold Qty</th>
              <th class="tabheader">Sales Amt</th>
              <th class="tabheader">Rtn Qty</th>
              <th class="tabheader">Short Qty</th>
              <th class="tabheader">Over Qty</th> 
              <th class="tabheader">Adj Qty</th>
              <th class="tabheader">End Balance</th>               
             </tr>
            </thead>
            <tbody>
             <?php
             
          		$domthyr  = $mthyr;
              if($domthyr <> "") {
                $tmpmth = explode("/", $domthyr);
                $domth = intval($tmpmth[0]);
                $doyear = $tmpmth[1]; 
                
                if($domth == 12) {
                  $prevmth = 1;
                  $prevyr = intval($doyear) - 1;
                }  else {
                  $prevmth = $domth - 1;
                  $prevyr = $doyear;
                  $prevmthyr =  vsprintf("%02d",$prevmth)."/".$prevyr; 
                }

              }             
             
             	$sql = "SELECT * FROM csalesdet";
             	$sql .= " Where sordno ='".$var_ordno."'"; 
	    		    $sql .= " ORDER BY sproseq";  
			  	    $rs_result = mysql_query($sql); 
			   
			    $i = 1;
				while ($rowq = mysql_fetch_assoc($rs_result)){ 
              
              $var_salesamt = 0;
              $var_salesamt = number_format($rowq['soldqty'] * $rowq['sprounipri'], 2, '.', ',');  
              $begbal = $rowq['endbal'];
              
             /* if($domthyr <> "") {

                $sql2 = " SELECT SUM(x.sproqty) as tot FROM salesentrydet x, salesentry y";
                $sql2 .= " WHERE y.scustcd = '".$custcd."'";
                $sql2 .= " AND x.sordno = y.sordno";
                $sql2 .= " AND y.sordno IN (SELECT sordno FROM salesdo WHERE ";
                $sql2 .= " MONTH(delorddte) = ".$domth;
                $sql2 .= " AND YEAR (delorddte) = ".$doyear.")";
                $sql2 .= " AND x.sprocd = '".$rowq['sprocd']."'";
                
                //echo $sql2;
                $tmp2 = mysql_query($sql2) or die ("cant get do qty : ".mysql_error());
                
                if(mysql_numrows($tmp2) >0) {
                   $rst2 = mysql_fetch_object($tmp2);
                   $doqty = $rst2->tot; 
                   if($doqty =="") { $doqty = 0; }
                 }
                 
                /* $sql2 = " SELECT endbal FROM csalesdet x, csalesmas y ";
                $sql2 .= " WHERE y.scustcd = '".$custcd."'";
                $sql2 .= " AND y.smthyr = '".$prevmthyr."'";
                $sql2 .= " AND x.sordno = y.sordno";
                $sql2 .= " AND x.sprocd = '".$rowq['sprocd']."'";
                
                //echo $sql2;
                $tmp2 = mysql_query($sql2) or die ("cant get do qty : ".mysql_error());
                
                if(mysql_numrows($tmp2) >0) {
                   $rst2 = mysql_fetch_object($tmp2);
                   $begbal = $rst2->endbal; 
                   if($begbal =="") { $begbal = 0; }
                 } else { $begbal = 0; } */
                 
                 
               /*} else {
                   $doqty = 0; 
                   $begbal = 0;
               } */
                             
                                          
             ?>            
             <tr class="item-row">
                <td style="width: 30px">
                <input id="begbal<?php echo $i; ?>" type="hidden" value ="<?php echo $begbal; ?>" >
				<input name="seqno[]" id="seqno" readonly="readonly" style="width: 27px; border:0;" value ="<?php echo $i; ?>"></td>
                <td>
				<input name="prococode[]" tProItem1=1 id="prococode<?php echo $i; ?>" tabindex="0" class="autosearch" style="width: 161px" onchange ="upperCase(this.id)" value ="<?php echo htmlentities($rowq['sprocd']); ?>"></td>
                <td>
				<input name="procoupri[]" id="procoupri<?php echo $i; ?>" class="tInput" style="width: 60px" value ="<?php echo $rowq['sprounipri']; ?>"></td>
                <td>
        <input name="procotype[]" class="tInput" id="procotype<?php echo $i; ?>" style="width: 48px;" value ="<?php echo $rowq['sptype']; ?>"></td>                
                <td>
				<input name="opening[]" id="opening<?php echo $i; ?>" readonly="readonly" style="width: 48px; text-align : right" value ="<?php echo $rowq['openingqty']; ?>" ></td>
                <td>
				<input name="procodoqty[]" id="procodoqty<?php echo $i; ?>" style="width: 48px; text-align : right" value ="<?php echo $rowq['doqty']; ?>" onBlur="getbal(<?php echo $i; ?>)"></td>
				
                <td>
        <input name="procosoldqty[]" class="tInput" id="procosoldqty<?php echo $i; ?>" style="width: 48px; text-align : right"  value ="<?php echo $rowq['soldqty']; ?>" onBlur="getamt(<?php echo $i; ?>); getbal(<?php echo $i; ?>)"></td>                
                <td>
        <input name="procosamt[]" class="tInput" id="procosamt<?php echo $i; ?>" style="border-style: none; width: 48px; text-align : right" readonly="readonly" value ="<?php echo $var_salesamt; ?>"></td>                
                <td>
        <input name="procortnqty[]" class="tInput" id="procortnqty<?php echo $i; ?>" style="width: 48px; text-align : right" value ="<?php echo $rowq['rtnqty']; ?>" onBlur="getbal(<?php echo $i; ?>)"></td>                
                <td>
        <input name="procoshortqty[]" class="tInput" id="procoshortqty<?php echo $i; ?>" style="width: 48px; text-align : right" value ="<?php echo $rowq['shortqty']; ?>" onBlur="getbal(<?php echo $i; ?>)"></td>                
                <td>
        <input name="procooverqty[]" class="tInput" id="procooverqty<?php echo $i; ?>" style="width: 48px; text-align : right" value ="<?php echo $rowq['overqty']; ?>" onBlur="getbal(<?php echo $i; ?>)"></td>                
                <td>
        <input name="procoadjqty[]" class="tInput" id="procoadjqty<?php echo $i; ?>" style="width: 48px; text-align : right" value ="<?php echo $rowq['adjqty']; ?>" onBlur="getbal(<?php echo $i; ?>)"></td>                
                <td>
        <input name="procobalqty[]" class="tInput" id="procobalqty<?php echo $i; ?>" style="border-style: none; width: 48px; text-align : right" value ="<?php echo $rowq['endbal']; ?>"></td>              
             </tr>
             
          <?php 
          
                	$i = $i + 1;          
          
             } // while
          ?>     
          <?php
            if ($i == 1){ ?>
            	 <tr class="item-row">
                <td style="width: 30px">
                <input id="begbal<?php echo $i; ?>" type="hidden" value ="<?php echo $begbal; ?>" >
				<input name="seqno[]" id="seqno" readonly="readonly" style="width: 27px; border:0;" value ="<?php echo $i; ?>"></td>
                <td>
				<input name="prococode[]" tProItem1=1 id="prococode<?php echo $i; ?>" tabindex="0" class="autosearch" style="width: 161px" onchange ="upperCase(this.id)" value ="<?php echo htmlentities($rowq['sprocd']); ?>"></td>
                <td>
				<input name="procoupri[]" id="procoupri<?php echo $i; ?>" class="tInput" style=" width: 60px" value ="<?php echo $rowq['sprounipri']; ?>"></td>
                <td>
        <input name="procotype[]" class="tInput" id="procotype<?php echo $i; ?>" style="width: 48px;" value ="<?php echo $rowq['sptype']; ?>"></td>                
                <td>
				<input name="opening[]" id="opening<?php echo $i; ?>" readonly="readonly" style="width: 48px; text-align : right" value ="<?php echo $rowq['openingqty']; ?>" ></td>
                <td>
				<input name="procodoqty[]" id="procodoqty<?php echo $i; ?>" style="width: 48px; text-align : right" value ="<?php echo $rowq['doqty']; ?>" onBlur="getbal(<?php echo $i; ?>)"></td>
				
                <td>
        <input name="procosoldqty[]" class="tInput" id="procosoldqty<?php echo $i; ?>" style="width: 48px; text-align : right"  value ="<?php echo $rowq['soldqty']; ?>" onBlur="getamt(<?php echo $i; ?>); getbal(<?php echo $i; ?>)"></td>                
                <td>
        <input name="procosamt[]" class="tInput" id="procosamt<?php echo $i; ?>" style="border-style: none; width: 48px; text-align : right" readonly="readonly" value ="<?php echo $var_salesamt; ?>"></td>                
                <td>
        <input name="procortnqty[]" class="tInput" id="procortnqty<?php echo $i; ?>" style="width: 48px; text-align : right" value ="<?php echo $rowq['rtnqty']; ?>" onBlur="getbal(<?php echo $i; ?>)"></td>                
                <td>
        <input name="procoshortqty[]" class="tInput" id="procoshortqty<?php echo $i; ?>" style="width: 48px; text-align : right" value ="<?php echo $rowq['shortqty']; ?>" onBlur="getbal(<?php echo $i; ?>)"></td>                
                <td>
        <input name="procooverqty[]" class="tInput" id="procooverqty<?php echo $i; ?>" style="width: 48px; text-align : right" value ="<?php echo $rowq['overqty']; ?>" onBlur="getbal(<?php echo $i; ?>)"></td>                
                <td>
        <input name="procoadjqty[]" class="tInput" id="procoadjqty<?php echo $i; ?>" style="width: 48px; text-align : right" value ="<?php echo $rowq['adjqty']; ?>" onBlur="getbal(<?php echo $i; ?>)"></td>                
                <td>
        <input name="procobalqty[]" class="tInput" id="procobalqty<?php echo $i; ?>" style="border-style: none; width: 48px; text-align : right" value ="<?php echo $rowq['endbal']; ?>"></td>              
             </tr>
		  <?php
            }
          ?>         
            </tbody>
          <tfoot>
          <tr>
          <td colspan="7" style = "border-top : 1px dotted; border-bottom : 1px dotted;" align="right">Total :</td>
          <td style = "border-top : 1px dotted; border-bottom : 1px dotted;" align="right">
				  <input name="gtot" id="gtot" class="tInput" readonly="readonly" style="border-style: none; border-color: inherit; border-width: 0; width: 48px; text-align : right" value ="<?php echo $rowq['sprounipri']; ?>"></td>
          <td colspan="5" style = "border-top : 1px dotted; border-bottom : 1px dotted;" align="right">&nbsp;</td>
          </tr>    
          </tfoot>         
            
           </table>
           
         <a href="#" id="addRow" class="button-clean large"><span><img src="../images/icon-plus.png" alt="Add" title="Add Row"> Add Item</span></a>
         <a href="javascript:deleteRow('itemsTable')" id="addRow" class="button-clean large"><span><img src="../images/icon-minus.png" alt="Add" title="Delete Row">Delete Row</span></a>

     <br /><br />
     
		 <table>
		  	<tr>
				<td style="width: 1150px; height: 22px;" align="center">
				<?php
				 $locatr = "m_csales_mas.php?menucd=".$var_menucode;
			
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
		</table>
		</form>
		<fieldset name="Group1">
    	 <legend class="title">GOODS RETURN NOTE</legend>
    		<form name="printgrn" method="POST" action="<?php echo $_SERVER['PHP_SELF'].'?sorno='.$var_ordno.'&menucd='.$var_menucode; ?>" onsubmit="return validateGRN()">
    	  	<table style="width: 993px; ">
    			<tr>
        	  	   <td style="width: 13px"></td>
        	  	   <td style="width: 122px">GRN No: </td>
        	  	   <td style="width: 201px">
        				<input name="grnno" id="grnno" type="text" required style="width: 204px;" value = "<?php echo $grnno; ?>">         
        		   </td>
        		   <td style="width: 10px"></td>
        		   <td style="width: 204px">GRN Date: </td>
        		   <td style="width: 284px">
        		   		<input name="grndate" id ="grndate" type="text" required style="width: 128px;" value="<?php  echo $grndate ?>">
    		   			<img alt="Date Selection" src="../images/cal.gif" onclick="javascript:NewCssCal('grndate','ddMMyyyy')" style="cursor:pointer">			
        		   </td>
        	  	</tr>
        	  	<tr>
        	  		<td align="center" colspan="6">
        	  			<input type="Submit" name="printgrn" value="Print Goods Return Note" required class="butsub" style="height: 32px";
        	  		</td>
        	  	</tr>    			
    	  	</table>
    		</form>	
	   </fieldset>
	</fieldset>
	</div>
	<div class="spacer"></div>
</body>

<script type="text/javascript">
getTotalAmt();
</script>
</html>
