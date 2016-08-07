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
    
      $var_ordno = $_GET['sorno'];
      $var_menucode = $_GET['menucd'];
      include("../Setting/ChqAuth.php");

    }
    
 if(isset($_POST['UpdHeader'])) {
  if($_POST['UpdHeader'] == "Upd_Header") {
    
      //phpinfo();
      
   	  $vmordno   = $_POST['sordno'];
		  $vmorddte = date('Y-m-d', strtotime($_POST['saorddte']));
		  $vmcustcd = $_POST['sacustcd'];
		  $vmmthyr  = $_POST['samthyr'];
		  $vmperiod = $_POST['speriod'];
      $vmlessamt = $_POST['lessamt'];  
      $vmlesstype = $_POST['lesstype']; 
      
      $vartoday = date("Y-m-d H:i:s");
      $var_ordno = $_POST['sordno'];
      
				//$sql = "Update csalesmas Set scustcd = '$vmcustcd', sorddte ='$vmorddte', smthyr = '$vmmthyr', ";
				$sql = " Update csalesmas Set sorddte ='$vmorddte', ";
        $sql .= " less_type = '$vmlesstype', less_amt = '$vmlessamt', ";
				$sql .= " speriod = '$vmperiod', ";
				$sql .= " modified_by = '$var_loginid', modified_on='$vartoday' ";
				$sql .= " Where sordno ='$vmordno'";
        
				mysql_query($sql) or die ("Cant update : ".mysql_error());
       
      $var_upddata = false;
      
    }
  }    

 if(isset($_POST['Submit'])) {  
    if ($_POST['Submit'] == "Submit") {
    
      //phpinfo();
      
   	  $vmordno   = $_POST['sordno'];
      
			$matcode     = $_POST['prococode'];
			$matseqno    = $_POST['seqno'];
			$matupri     = $_POST['procoupri'];
      $mattype     = $_POST['procotype'];
			$matdoqty    = $_POST['procodoqty'];
			$matsoldqty  = $_POST['procosoldqty'];
			$matrtnqty   = $_POST['procortnqty'];
			$matshortqty = $_POST['procoshortqty'];
			$matoverqty  = $_POST['procooverqty'];
			$matadjqty   = $_POST['procoadjqty'];
			$matendbal   = $_POST['procobalqty'];
      $openingqty  = $_POST['opening'];      
      
      $upd = "Y";
 
     /* //------------------------- chk duplicate prod --------------    
      $sql = " select * from csalesdet ";
      $sql .= " where sordno = '$vmordno'";
      $sql .= " and sprocd = '$matcode'";
	  $sql .= " and sptype = '$mattype'";
	  $sql .= " and sprounipri = '$matupri'";
     
      //echo $sql;
      $result = mysql_query($sql) or die ("Error chk dup : ".mysql_error());
      
      if(mysql_numrows($result) > 0) {
	  
        $sql2 = "SELECT sprocd, sptype FROM csalesdet";
        $sql2 .= " Where sordno ='".$vmordno."'"; 
	    	$sql2 .= " ORDER BY sprocd";  
			  $rs_result = mysql_query($sql2);
        
        $var_cnt = 0;
        while ($row = mysql_fetch_array($rs_result)) {
           $var_cnt += 1;
           if ($row['sprocd'] == $matcode  && $row['sptype'] == $mattype && $row['sprounipri'] == $matupri) {
              break;
           } 
            
        }

     	  $upd = "N";
        echo "<script>";
        echo 'alert("Duplicate Item Found : '.$matcode.' At Row : '.$var_cnt.'");';
        echo "</script>";
        
      }      
      //----------------------------------------------------------
        */
        
      /*
      //------------------------- chk prod type --------------    
      $sql = " select * from salestype_master ";
      $sql .= " where salestype_code = '$mattype'";
     
      $result = mysql_query($sql) or die ("Error chk dup : ".mysql_error());

      if(mysql_numrows($result) == 0) {
     	  $upd = "N";
        echo "<script>";
        echo 'alert("Invalid Product Type : '.$mattype.'");';
        echo "</script>";
        
      }      
      //----------------------------------------------------------
        */
       
      //echo ".......................................".$upd;        
      if($upd == "Y") {    
      
	      $vartoday = date("Y-m-d H:i:s");
        
		if ($matdoqty == "" or empty($matdoqty)){$matdoqty = 0;}
        if ($matsoldqty == "" or empty($matsoldqty)){$matsoldqty = 0;}
        if ($matrtnqty == "" or empty($matrtnqty)){$matrtnqty = 0;}
        if ($matshortqty == "" or empty($matshortqty)){$matshortqty = 0;}
        if ($matoverqty == "" or empty($matoverqty)){$matoverqty = 0;}
        if ($matadjqty == "" or empty($matadjqty)){$matadjqty = 0;}
        //if ($matendbal == "" or empty($matendbal)){$matendbal = 0;}        
		if ($openingqty == "" or empty($openingqty)){$openingqty = 0;}  
		//if ($matseqno == "" or empty($matseqno)){$matseqno = 0;} 

        $matendbal = $openingqty + $matdoqty - $matsoldqty - $matrtnqty - $matshortqty + $matoverqty + $matadjqty;       
        if ($matendbal == "" or empty($matendbal)){$matendbal = 0;} 		
        
 				if ($matcode <> "" ) {
        
             $sqlmx = "select max(sproseq) as seq from csalesdet";
             $sqlmx .= " where sordno ='".$vmordno."'";
             
             $rsmx = mysql_query($sqlmx); 
             
             if(mysql_numrows($rsmx) > 0) {
                $datmx = mysql_fetch_object($rsmx);
                $var_mxno = $datmx->seq;
             }
             if($var_mxno == "") { $var_mxno = 0; }
             $var_mxno += 1;        

							$sql = "INSERT INTO csalesdet values 
						    		('$vmordno', '$matcode', '$matupri', '$mattype', '$matdoqty', '$openingqty', '$matsoldqty', '$matrtnqty',
                     '$matshortqty', '$matoverqty', '$matadjqty', '$matendbal', '$var_mxno')";
                    
							mysql_query($sql) or die ("Cant insert 2 : ".mysql_error());
              
             //echo "<script>";
             //echo 'alert("Insert Successfully");';
             //echo "</script>"; 

         }	          

         $var_upddata = false; 
         
               

       } else {
       
         $var_upddata = false;

      $prodcd = $matcode;
      $prodtype = $mattype;
      $produpri = $matupri;
      $opening = $openingqty;
      $doqty = $matdoqty;
      $soldqty = $matsoldqty;
      $rtnqty = $matrtnqty;
      $shortqty = $matshortqty;
      $overqty = $matoverqty;
      $adjqty = $matadjqty;
      $endqty = $matendbal;  
      $seqno = $matseqno;     
                
       }  // if($upd == "Y)
       
      $var_ordno = $_POST['sordno'];
    }
   }   

 if(isset($_POST['Upd'])) {
  if($_POST['Upd'] == "UpdRec") {
    
      //phpinfo();
      
   	  $vmordno   = $_POST['sordno'];
      
			$matcode     = $_POST['prococode'];
			$matseqno    = $_POST['seqno'];
			$matupri     = $_POST['procoupri'];
      $mattype     = $_POST['procotype'];
			$matdoqty    = $_POST['procodoqty'];
			$matsoldqty  = $_POST['procosoldqty'];
			$matrtnqty   = $_POST['procortnqty'];
			$matshortqty = $_POST['procoshortqty'];
			$matoverqty  = $_POST['procooverqty'];
			$matadjqty   = $_POST['procoadjqty'];
			$matendbal   = $_POST['procobalqty'];
      $openingqty  = $_POST['opening'];
      
      $upd = "Y";
 
      //------------------------- chk duplicate prod --------------    
     /* $sql = " select * from salesentrydet ";
      $sql .= " where sordno = '$vmordno'";
      $sql .= " and sprocd = '$matcode'";
     
      //echo $sql;
      $result = mysql_query($sql) or die ("Error chk dup : ".mysql_error());
      
      if(mysql_numrows($result) > 0) {
     	  $upd = "N";
        echo "<script>";
        echo 'alert("Duplicate Item Found : '.$matcode.'");';
        echo "</script>";
        
      }     
      //----------------------------------------------------------

      //------------------------- chk prod type --------------    
      $sql = " select * from salestype_master ";
      $sql .= " where salestype_code = '$mattype'";
     
      $result = mysql_query($sql) or die ("Error chk dup : ".mysql_error());

      if(mysql_numrows($result) == 0) {
     	  $upd = "N";
        echo "<script>";
        echo 'alert("Invalid Product Type : '.$mattype.'");';
        echo "</script>";
        
      }      
      //----------------------------------------------------------
       */
              
      if($upd == "Y") {    
      
	            $vartoday = date("Y-m-d H:i:s");
        
 				if ($matcode <> "") {

				//$sql .= " Update csalesdet Set doqty = '$matdoqty', soldqty = '$matsoldqty',  ";
				$sql = " Update csalesdet Set sprounipri = '$matupri', sptype ='$mattype', "; 
        $sql .= "                    doqty = '$matdoqty', soldqty = '$matsoldqty',  ";                       
				$sql .= "                    rtnqty = '$matrtnqty', shortqty = '$matshortqty', ";
 				$sql .= "                    overqty = '$matoverqty', adjqty = '$matadjqty', ";
				$sql .= "                    endbal = '$matendbal', openingqty = '$openingqty' ";                
				$sql .= "  Where sordno ='$vmordno' and sprocd = '$matcode'";
        $sql .= " and sproseq = '$matseqno' ";
				//$sql .= " and sptype = '$mattype' and sprounipri = '$matupri'";
                    
						mysql_query($sql) or die ("Cant update salesdet : ".mysql_error());
         }	          

         $var_upddata = false; 
         
        //echo "<script>";
        //echo 'alert("Updated Successfully");';
        //echo "</script>";               

       } else {
       
         $var_upddata = false;
         /*
         $prodcd = $matcode;
         $produom = $matuom;
         $produqty = $_POST['procouqty'];
         $prodtype = $mattype;
         $produpri = $matuprice;
         $prodqty = $matqty;
         $prodtotqty = $_POST['procototpcs'];  */
                
       }  // if($upd == "Y)
       
      $var_ordno = $_POST['sordno'];
    }
   }
   
 if(isset($_POST['Reset'])) {
  if($_POST['Reset'] == "Reset") {
    
      //phpinfo();
      
      $prodcd = "";
      $prodtype = "";
      $produpri = "";
      $opening = "";
      $doqty = "";
      $soldqty = "";
      $rtnqty = "";
      $shortqty = "";
      $overqty = "";
      $adjqty = "";
      $endqty = ""; 
      $seqno = "";
      
      $var_salesamt = "";  
      
      $var_ordno = $_POST['sordno'];  
      $var_upddata = false;
      
    }
  } 
            
    
    if(isset($_GET['act'])) {  $var_action  = $_GET['act'];  }
    if(isset($_GET['sorno']))  {  $var_salesno = $_GET['sorno']; }
    if(isset($_GET['i']))  {  $var_item = $_GET['i']; }
    if(isset($_GET['cust']))  {  $var_cust = $_GET['cust']; }
    if(isset($_GET['mth']))  {  $var_mthyr = $_GET['mth']; } 
    if(isset($_GET['t']))  {  $var_type = $_GET['t']; }
    if(isset($_GET['u']))  {  $var_upri = $_GET['u']; }
    if(isset($_GET['s']))  {  $var_seq = $_GET['s']; }       	
       
     if ($var_action == "del") { 
        if($var_salesno != "" && $var_item != "") { 
        
	       	//mysql_query("delete from `csalesdet` where `sordno` = '$var_salesno' and `sprocd` = '$var_item' and `sptype` = '$var_type'  and `sprounipri` = '$var_upri';", $db_link);
	       	mysql_query("delete from `csalesdet` where `sordno` = '$var_salesno' and `sprocd` = '$var_item' and `sproseq` = '$var_seq';", $db_link);

          $var_upddata = false;
           //$var_pstrefno = $var_selrefno;
           //$var_pstserialno = $var_selserialno;

        }
      }
 
     if ($var_action == "upd") { 
        if($var_salesno != "" && $var_item != "") { 
        
           $var_upddata = true;

        }
      }  
     
    
  if ($var_upddata == true) {
  
      $sql = "select * from csalesdet where sordno = '".$var_salesno."'";
      $sql .= " and sprocd = '".$var_item."'";
      $sql .= " and sproseq = '".$var_seq."'";
	  //$sql .= " and sptype = '".$var_type."'";
	  //$sql .= " and sprounipri = '".$var_upri."'";
      
      $tmprst = mysql_query($sql) or die ("Cant get item det for update : ".mysql_error());
      $rst = mysql_fetch_object($tmprst);      

      $prodcd = $rst->sprocd;
      $prodtype = $rst->sptype;
      $produpri = $rst->sprounipri;
      $opening = $rst->openingqty;
      $doqty = $rst->doqty;
      $soldqty = $rst->soldqty;
      $rtnqty = $rst->rtnqty;
      $shortqty = $rst->shortqty;
      $overqty = $rst->overqty;
      $adjqty = $rst->adjqty;
      $endqty = $rst->endbal;
      $seqno = $rst->sproseq; 
      
      $var_salesamt = number_format($produpri * $soldqty, 2, '.', ',');  
      
      /*
     if($var_mthyr <> "") {
        $tmpmth = explode("/", $var_mthyr);
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
      
        
         $sql2 = " SELECT endbal FROM csalesdet x, csalesmas y ";
         $sql2 .= " WHERE y.scustcd = '".$var_cust."'";
         $sql2 .= " AND y.smthyr = '".$prevmthyr."'";
         $sql2 .= " AND x.sordno = y.sordno";
         $sql2 .= " AND x.sprocd = '".$var_item."'";
          
         //echo $sql2;       
         $tmp2 = mysql_query($sql2) or die ("cant get endbal qty : ".mysql_error());
                
         if(mysql_numrows($tmp2) >0) {
             $rst2 = mysql_fetch_object($tmp2);
             $begbal = $rst2->endbal; 
             if($begbal =="") { $begbal = 0; }
         }  else { $begbal = 0; }       
     } else { $begbal = 0; }                                           
      */
      
   } else {
     if ($matcode == "") {
      $prodcd = "";
      $prodtype = "";
      $produpri = "";
      $doqty = "";
      $soldqty = "";
      $rtnqty = "";
      $shortqty = "";
      $overqty = "";
      $adjqty = "";
      $endqty = ""; 
      $seqno = "";
      
      $var_salesamt = "";    
     }
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


<script type="text/javascript"> 

function decision(msg)
{
	var msg;
	return confirm(msg);
}

function upperCase(x)
{
var y=document.getElementById(x).value;
document.getElementById(x).value=y.toUpperCase();
}

function setup() {

		document.InpPO2.saorddte.focus();
				
 		//Set up the date parsers
        var dateParser = new DateParser("dd-MM-yyyy");
        
		//Set up the DateMasks
		var errorMessage = "Invalid date: ${value}. Expected format: ${mask}";
		var dateMask1 = new DateMask("dd-MM-yyyy", "saorddte");
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

function validateForm2()
{ 
 
  var x=document.forms["InpPO2"]["sacustcd"].value;
	if (x==null || x=="s")
	{
	alert("Customer Must Not Be Blank");
	document.InpPO2.sacustcd.focus;
	return false;
	}

   var x=document.forms["InpPO2"]["saorddte"].value;
	if (x==null || x=="")
	{
	alert("Order Date Must Not Be Blank");
	document.getElementById("saorddte").focus();
	return false;
	}
}

function validateForm()
{
   var x=document.forms["InpPO"]["prococode"].value;
	if (x==null || x=="" || x=="0")
	{
	alert("Item Code Must Not Be Blank");
	document.getElementById("prococode").focus();
	return false;
	}  
    
   var x=document.forms["InpPO"]["procotype"].value;
	if (x==null || x=="" || x=="a")
	{
	alert("Type Must Not Be Blank");
	document.getElementById("procotype").focus();
	return false;
	} 
}


function getItemDet()
{

 var rand = Math.floor(Math.random() * 101);
 var custinfo = document.getElementById("sacustcd").value;
 var iteminfo = document.getElementById("prococode").value;
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
    document.getElementById("procotype").value=result[0];   
    document.getElementById("procoupri").value=result[1]; 
    document.getElementById("procodoqty").value=result[2];
    //document.getElementById("begbal").value=result[3];
    document.getElementById("opening").value=result[4];
    document.getElementById('procosoldqty').focus();

    //if(result[1] == 0) { alert ("Unit Price is 0"); }      
    }
  }
xmlhttp.open("GET","getsalesprice.php?s="+custinfo+"&i="+iteminfo+"&d="+domthyr+"&m="+rand,true);
xmlhttp.send();
}

function getamt () {

 var soldqty = document.getElementById("procosoldqty").value;
 var price = document.getElementById("procoupri").value;
 var soldqty = soldqty | 0;
 
 var totamt = soldqty * price; 
    
 document.getElementById("procosamt").value = totamt.toFixed(2);
 document.getElementById("procosoldqty").value = soldqty;
 document.InpPO.procortnqty.focus();
}

function getbal () {

  //var begbal = parseInt(document.getElementById("begbal").value);
  var opening = parseInt(document.getElementById("opening").value);
  var doqty = parseInt(document.getElementById("procodoqty").value);
  var soldqty = parseInt(document.getElementById("procosoldqty").value);
  var rtnqty = parseInt(document.getElementById("procortnqty").value);
  var shortqty = parseInt(document.getElementById("procoshortqty").value);
  var overqty = parseInt(document.getElementById("procooverqty").value);
  var adjqty = parseInt(document.getElementById("procoadjqty").value);
  
  //if (isNaN(begbal)) { begbal = 0; }
  if (isNaN(opening)) { opening = 0; }
  if (isNaN(doqty)) { doqty = 0; }
  if (isNaN(soldqty)) { soldqty = 0; }
  if (isNaN(rtnqty)) { rtnqty = 0; }
  if (isNaN(shortqty)) { shortqty = 0; }
  if (isNaN(overqty)) { overqty = 0; }
  if (isNaN(adjqty)) { adjqty = 0; }
  
  var begbal = begbal | 0;
  var opening = opening | 0;
  var doqty = doqty | 0;
  var soldqty = soldqty | 0;  
  var rtnqty = rtnqty | 0;
  var shortqty = shortqty | 0;
  var overqty = overqty | 0;
  var adjqty = adjqty | 0;        
    
  
  //alert ("Beg: "+begbal+"DO :"+doqty+"Sold : "+soldqty+"Ret : "+rtnqty+"short : "+shortqty+"Over : "+overqty+"Adj :"+adjqty)
  //var endbal = begbal + doqty - soldqty - rtnqty - shortqty + overqty + adjqty;
  var endbal = opening + doqty - soldqty - rtnqty - shortqty + overqty + adjqty;
  document.getElementById("procobalqty").value = endbal;
  document.getElementById("procortnqty").value = rtnqty;
  document.getElementById("procoshortqty").value = shortqty;
  document.getElementById("procooverqty").value = overqty;
  document.getElementById("procoadjqty").value = adjqty; 
 
}


</script>
</head>

<body onload="setup()" >
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
	  
	  <form name="InpPO2" method="POST" action="<?php echo $_SERVER['PHP_SELF'].'?menucd='.$var_menucode; ?>" onsubmit="return validateForm2()">
	   
		<table style="width: 993px; font-family : verdana, helvetica; font-size : 12px; ">
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
		   	<select name="sacustcd" id="sacustcd" style="width: 268px" class="textnoentry">
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
			<td style="width: 204px">Order Date</td>
			<td style="width: 16px">:</td>
			<td style="width: 284px">
		   <input class="inputtxt" name="saorddte" id ="saorddte" type="text" style="width: 128px;" value="<?php  echo $orddte; ?>">
		   <img alt="Date Selection" src="../images/cal.gif" onclick="javascript:NewCssCal('saorddte','ddMMyyyy')" style="cursor:pointer"></td>
		   </td>
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
		   	<select name="lesstype" id="lesstypecd" >
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
      <?php if($var_less == "N") { echo "readonly"; } ?>>		   
		     </td>
		   <td style="width: 10px"></td>
		   <td style="width: 204px">
       <input type ="submit" name = "UpdHeader" value = "Upd_Header" class="butsub"></td>
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
      </form>
	  <form name="InpPO" method="POST" action="<?php echo $_SERVER['PHP_SELF'].'?menucd='.$var_menucode; ?>" onsubmit="return validateForm()">
		  <table id="itemsTable" style="width: 958px; padding : 6px; font-family : verdana, helvetica; font-size : 12px;">
          	 <tr>      
          	<thead>
          	 <tr>
             <th class="tabheader">#</th>
              <th class="tabheader">Product Code</th>
              <th class="tabheader">Unit Price</th>
              <th class="tabheader">Type</th>
              <th class="tabheader">Opening</th>
              <th class="tabheader">D/O Qty</th>              
              <th class="tabheader">Sold Qty</th>
              <th class="tabheader">Sales Amt</th>
              <th class="tabheader">Rtn Qty</th>
              <th class="tabheader">Short Qty</th>
              <th class="tabheader">Over Qty</th> 
              <th class="tabheader">Adj Qty</th>
              <th class="tabheader">End Balance</th>               
              <th class="tabheader" colspan="2" style="width: 284px">Action</th>              
             </tr>
            </thead>           
            <tbody>
              <td ></td>
              <td >
              <select name="prococode" id="prococode" style="width: 300px" onChange="getItemDet()">
			 <?php
              //$sql = "select productcode, description from product where status = 'A' ORDER BY productcode ASC";
               $sql = "select distinct groupcode as productcode, description FROM product WHERE status in ('A', 'D') and groupcode <> '' order by productcode";
               $sql_result = mysql_query($sql);
              echo "<option size =30 >$prodcd</option>";
                       
			        if(mysql_num_rows($sql_result)) 
		      	  {
			          while($row = mysql_fetch_assoc($sql_result)) 
			          { 
			          	echo '<option value="'.$row['productcode'].'"';
						if($prodcd == $row['productcode']) { echo "selected"; }
                  echo '>'.$row['productcode']." | ".$row['description'].'</option>';
			          } 
		          } 
	         ?>				   
	            </select>
              <input name="sordno" type="hidden"  value = "<?php echo $var_ordno; ?>"> 
              </td>
              <td ><input name="procoupri" id="procoupri" class="tInput" style="width: 60px; text-align : right" value ="<?php echo $produpri; ?>"></td>
              <td >
			  <!-- <input name="procotype" class="tInput" id="procotype" style="border-style: none; width: 48px;" value ="<?php //echo $prodtype; ?>" readonly="readonly"> -->
             <select name="procotype" id="procotype" style="width: 48px" >
			 <?php
               $sql = "select salestype_code from salestype_master ";
               $sql_result = mysql_query($sql);
              echo "<option value='a'>-</option>";
                       
			        if(mysql_num_rows($sql_result)) 
		      	  {
			          while($row = mysql_fetch_assoc($sql_result)) 
			          { 
			          	echo '<option value="'.$row['salestype_code'].'"';
						if($prodtype == $row['salestype_code']) { echo "selected"; }
                  echo '>'.$row['salestype_code'].'</option>';
			          } 
		          } 
	         ?>				   
	            </select>
			  
			  </td>
              <td><input name="opening" id="opening" style="width: 48px; text-align : right" readonly="readonly" value ="<?php echo $opening; ?>" size="20"> </td>
              <td >
              <!-- <input name="procodoqty" id="procodoqty" style="border-style: none; width: 48px; text-align : right" value ="<?php //echo $doqty; ?>" readonly> -->
              <input name="procodoqty" id="procodoqty" style="width: 48px; text-align : right" value ="<?php echo $doqty; ?>" >
              </td>
              <td ><input name="procosoldqty" class="tInput" id="procosoldqty" style="width: 48px; text-align : right"  value ="<?php echo $soldqty; ?>" onBlur="getamt()"></td>
              <td ><input name="procosamt" class="tInput" id="procosamt" style="border-style: none; width: 48px; text-align : right" readonly="readonly" value ="<?php echo $var_salesamt; ?>"></td>
              <td ><input name="procortnqty" class="tInput" id="procortnqty" style="width: 48px; text-align : right" value ="<?php echo $rtnqty; ?>"></td>              
              <td><input name="procoshortqty" class="tInput" id="procoshortqty" style="width: 48px; text-align : right" value ="<?php echo $shortqty; ?>"></td>                
              <td><input name="procooverqty" class="tInput" id="procooverqty" style="width: 48px; text-align : right" value ="<?php echo $overqty; ?>"></td>                
              <td><input name="procoadjqty" class="tInput" id="procoadjqty" style="width: 48px; text-align : right" value ="<?php echo $adjqty; ?>" onBlur="getbal()"></td>                
              <td><input name="procobalqty" class="tInput" id="procobalqty" style="border-style: none; width: 48px; text-align : right" value ="<?php echo $endqty; ?>"></td>              

              <td colspan="2">
              <input name="seqno" type="hidden"  value = "<?php echo $seqno; ?>"> 
 <?php
 
  if ($var_upddata == true) {
      echo '<input type ="submit" name = "Upd" value = "UpdRec" class="butsub">';
      echo '<input type ="submit" name = "Reset" value = "Reset" class="butsub">';
  } else {
          echo '<input type ="submit" name = "Submit" value = "Submit" class="butsub">';
  }

?>              
              </td>              
             </tr>            
             <?php
             
             	$sql = "SELECT * FROM csalesdet";
             	$sql .= " Where sordno ='".$var_ordno."'"; 
	    	      $sql .= " ORDER BY sprocd";  
			  	    $rs_result = mysql_query($sql); 
			   
			    $i = 1;    $var_tot = 0;
				while ($rowq = mysql_fetch_assoc($rs_result)){ 
       
              $var_salesamt = 0;
              $var_salesamt = $rowq['soldqty'] * $rowq['sprounipri'];  
              $var_tot += $var_salesamt;
              $var_salesamt = number_format($var_salesamt, 2, '.', ','); 
        
    if($var_bgcol == "#ffffff") { $var_bgcol = "#efefef"; }
    else { $var_bgcol = "#ffffff"; }
  
  echo "<tr bgcolor=".$var_bgcol." onMouseOver=\"this.bgColor = '#DEE7E7'\" onMouseOut =\"this.bgColor ='".$var_bgcol."'\">";
  echo '<td>'.$i.'</td>'; 
  echo '<td>'.htmlentities($rowq['sprocd']).'</td>'; 
  echo '<td align="right">'.$rowq['sprounipri'].'</td>';         
  echo '<td >'.$rowq['sptype'].'</td>'; 
  echo '<td align="right">'.$rowq['openingqty'].'</td>';
  echo '<td align="right">'.$rowq['doqty'].'</td>';
  echo '<td align="right">'.$rowq['soldqty'].'</td>';
  echo '<td align="right">'.$var_salesamt.'</td>';   
  echo '<td align="right">'.$rowq['rtnqty'].'</td>';
  echo '<td align="right">'.$rowq['shortqty'].'</td>';
  echo '<td align="right">'.$rowq['overqty'].'</td>';
  echo '<td align="right">'.$rowq['adjqty'].'</td>';
  echo '<td align="right">'.$rowq['endbal'].'</td>';
  
     
   echo '<td align = "center" ><a onClick="javascript:return decision(\'Are you sure u want to ';
   echo 'DELETE this item : `'.$rowq['sprocd'].'` ?\')"';  
   echo ' href="'.$_SERVER['PHP_SELF'].'?act=del&sorno='.$var_ordno.'&menucd='.$var_menucode.'&i='.htmlentities($rowq['sprocd']).'&t='.$rowq['sptype'].'&u='.$rowq['sprounipri'].'&s='.$rowq['sproseq'].'">';
   echo '<img src = "../images/b_drop.png" border="0" width="16" height = "16" alt="Delete the Current Record">';
   echo '</a></td>';
                                                                                                       

   echo '<td align="center"><a href = "'.$_SERVER['PHP_SELF'].'?act=upd&sorno='.$var_ordno.'&menucd='.$var_menucode.'&i='.htmlentities($rowq['sprocd']).'&cust='.$custcd.'&mth='.$mthyr.'&t='.$rowq['sptype'].'&u='.$rowq['sprounipri'].'&s='.$rowq['sproseq'].'">';
   echo '<img src = "../images/b_edit.png" border="0" width="16" height = "16" alt="Update the Current Record">';
   echo '</a></td>'; 
   echo '</tr>';
                
                	$i = $i + 1;          
          
             } // while
          ?> 
          <tr >
          <td colspan="7" style = "border-top : 1px dotted; border-bottom : 1px dotted;" align="right">Total :</td>
          <td style = "border-top : 1px dotted; border-bottom : 1px dotted;" align="right"><?php echo number_format($var_tot, 2, '.', ','); ?></td>
           <td colspan="7" style = "border-top : 1px dotted; border-bottom : 1px dotted;" align="right">&nbsp;</td>
          </tr>    
            </tbody>
           </table>
           
     <br />     
		 <table>
		  	<tr>
				<td style="width: 1150px; height: 22px;" align="center">
				<?php
				 $locatr = "m_csales_mas.php?menucd=".$var_menucode;
			
				 echo '<input type="button" value="Back" class="butsub" style="width: 60px; height: 32px" onclick="location.href=\''.$locatr.'\'" >';
					//include("../Setting/btnupdate.php");
          
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
