<?php
    include("../Setting/Configifx.php");
	include("../Setting/Connection.php");

    $return_arr = array();
    $param = $_GET["term"];

    $fetch = mysql_query("SELECT refno FROM invtrcvd_nlg  WHERE refno  REGEXP '^$param' and stat = 'A' and posted <> 'N' order by refno LIMIT 10");

    /* Retrieve and store in array the results of the query.*/
    while ($row = mysql_fetch_array($fetch, MYSQL_ASSOC)) {
    
        $row_array['prcode'] = $row['refno']; 
        $row_array['prdesc'] = $row['description'];       
        
        array_push( $return_arr, $row_array );
    }

   
    /* Toss back results as json encoded array. */
    echo json_encode($return_arr);
?>
