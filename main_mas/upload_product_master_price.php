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

function download_send_headers($filename) {
    //disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");
        
    // force download
    //header("Content-Type: application/force-download");
    //header("Content-Type: application/octet-stream");
    //header("Content-Type: application/download");
    header('Content-Type: text/csv; charset=utf-8');
    
    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

function array2csv(array &$array) {
    if (count($array) == 0) {
        return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    //fputcsv($df, array_keys(reset($array)));
    foreach ($array as $row) {
        fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
}

function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);
        
        echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}

if(isset($_POST['importSubmit'])){
    
    //validate whether uploaded file is a csv file
    $csvMimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'],$csvMimes)){
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            
            //open uploaded csv file with read only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            //skip first line
            $header = fgetcsv($csvFile);           
            
            //parse data from csv file line by line
            while(($line = fgetcsv($csvFile)) !== FALSE){                
                $productcode = $line[0];
                $vartoday = date("Y-m-d H:i:s");
                
                debug_to_console( "productcode:$productcode" );
                if (!empty($productcode)) {
                    //delete all existing
                    debug_to_console( "delete all existing" );
                    $delQuery = "DELETE FROM prodprice WHERE productcode = '$productcode'";
                    mysql_query($delQuery) or die("delete query:".mysql_error()); 
                    
                    foreach($header as $x=>$x_value)
                    {
                        if ($x > 0) {
                            debug_to_console( "insert data: $x: $x_value, $line[$x]" );
                            if (!empty($x_value) && !empty($line[$x])) {
                                $sql = "INSERT INTO prodprice values ";
                                $sql .=	"('$productcode', '$x_value', '$line[$x]')";
                        
                                mysql_query($sql) or die("query 1 :".mysql_error());
                            }
                        }
                    }
                }
            }
            
            //close opened csv file
            fclose($csvFile);
            
            $statusMsgClass = 'alert-success';
            $statusMsg = 'Upload successfully.';
        }else{
            $statusMsgClass = 'alert-danger';
            $statusMsg = 'Some problem occurred, please try again.';
        }
    }else{
        $statusMsgClass = 'alert-danger';
        $statusMsg = 'Please upload a valid CSV file.';
    }
}

if ($_POST['donwloadSubmit'] == "Download Product Master Price CSV") {     
    // headers
    $sql = "select price_code, price_desc from price_master ";
    $sql .= "order by price_code";
    $priceSQL = mysql_query ($sql) or die("cant get price master : ".mysql_error());
    
    $prices[] = 'Product Code';
    if (mysql_numrows($priceSQL) > 0) {
        while ($row = mysql_fetch_array($priceSQL)) {
            $prices[] = $row['price_code'];
        }
    }
    
    $sql = "SELECT p.productcode, pp.pricecode, pp.uprice FROM product p ";
    $sql .= "INNER JOIN prodprice pp ON pp.productcode = p.productcode ";
    $sql .= "ORDER BY p.productcode";
    
    $masterSQL = mysql_query($sql) or die ("Cant get product master price : ".mysql_error());
    
   
    $result[] = $prices;
    $productcode = "";
    if(mysql_numrows($masterSQL) > 0) {
        while ($row = mysql_fetch_array($masterSQL)) {
            //debug_to_console( "productcode:" .$productcode );
            if ($productcode == $row['productcode']) {
                $data = $result[$productcode];
                $data[$row['pricecode']] = $row['uprice'];
                //debug_to_console( "insert data:" .print_r($data) );
            }
            else {
                foreach($prices as $x=>$x_value)
                {
                    if ($x_value == "Product Code") {
                        $data[$x_value] = $row['productcode'];
                    }
                    else if ($x_value == $row['pricecode']) {
                        $data[$row['pricecode']] = $row['uprice'];
                    }
                    else {
                        $data[$x_value] = '';
                    }
                } 
                $productcode = $row['productcode'];
                //debug_to_console( "ll productcode:" .$productcode);
            }
            $result[$row['productcode']] = $data;
            

        }        
    }
     
    download_send_headers("product_master_price_" . date("Y-m-d") . ".csv");
    echo array2csv($result);
    die();
    
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

	
</script>
</head>
    <?php include("../topbarm.php"); ?> 
<body>

  <div class="contentc">


	<fieldset name="Group1" style=" width: 800px;" class="style2">
	 <legend class="title">Upload Product Master Price</legend>
	  <br>
	 	<div class="container">
    <?php if(!empty($statusMsg)){
        echo '<div class="alert '.$statusMsgClass.'">'.$statusMsg.'</div>';
    } ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Upload Product Master Price
        </div>
        <div class="panel-body">
        	<table>
        		<tr>
        			<td width="50%">
            	<form action="<?php echo $_SERVER['PHP_SELF'].'?menucd='.$var_menucode;; ?>" method="post" enctype="multipart/form-data" id="importFrm">
                <input type="file" name="file" />
                <br></br>
                <input type="submit" class="btn btn-primary" name="importSubmit" value="IMPORT">
                </form>
                	</td>
                <td>
                	<form name="frmDownload" method="POST" action="<?php echo $_SERVER['PHP_SELF'].'?menucd='.$var_menucode;; ?>">
                	<input type="submit" class="btn btn-primary" name="donwloadSubmit" value="Download Product Master Price CSV"></input>                	
            		</form>
            	</td>
            </tr>            
        </div>
    </div>
</div>


	   </fieldset>
	  </div>	
	  <div class="spacer"></div>
	
</body>

</html>
