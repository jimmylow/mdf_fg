<?php

	include("../Setting/Configifx.php");
	include("../Setting/Connection.php");


  $sql1 = " select rcvdno";
  $sql1 .= " from invtrcvd";
  $sql1 .= " where rcvdno between 0020 and 0118";
  
  $tmp1 = mysql_query($sql1) or die ("Cant get no : ".mysql_error());
  
  if (mysql_num_rows($tmp1) > 0) {
   while ($row1 = mysql_fetch_array($tmp1)) {

    $vmordno   = $row1['rcvdno'];

    echo $vmordno;
		if ($vmordno <> "") {
    			
        $vartoday = date("Y-m-d H:i:s"); 
        
        $sql = "select rcvddte from invtrcvd";
				$sql .= "  Where rcvdno ='$vmordno'";
				$tmprst = mysql_query($sql) or die ("Cant query master : ".mysql_error());        

        $row = mysql_fetch_array($tmprst);
        $rcvddte = date('Y-m-d', strtotime($row['rcvddte']));
                
        
				$sql =  "select * from invtrcvddet";
				$sql .= "  Where rcvdno ='$vmordno'";
        $sql .= " order by proseq";
				
				$tmprst = mysql_query($sql) or die ("Cant query details : ".mysql_error());        

        if(mysql_numrows($tmprst) > 0) {

          while ($row = mysql_fetch_array($tmprst)) {
              $procd = $row['procd'];
              $proqty = $row['proqty'];
              
							$sql = "INSERT INTO invthist values 
						    		('RC', '$vmordno', '$rcvddte', '$vartoday', '$procd', '$proqty', '0')";
                    
							mysql_query($sql) or die ("Cant insert hist : ".mysql_error());
              
              echo "<br >Ins : ".$sql;
            }  
         }
         
        $sql = "update invtrcvd";
        $sql .= " set posted = 'Y'";
				$sql .= "  Where rcvdno ='$vmordno'";
				
        $tmprst = mysql_query($sql) or die ("Cant update master : ".mysql_error());          	
         
         echo "<br >Ins : ".$sql;   
			 }               
      }
    }
       
?>

