<?php
	include("../Setting/Configifx.php");
	include("../Setting/Connection.php");
	
	$var_loginid = $_SESSION['sid'];
	$var_menucode = "CONS01";
	include("../Setting/ChqAuth.php");
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */ 
	$aColumns = array('sordno', 'scustcd', 'smthyr', 'speriod', 'stat');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "sordno";
	
	/* DB table to use */
	$sTable = "csalesmas";
	
	/* Database connection information */
	$gaSql['user']       = $var_userid;
	$gaSql['password']   = $var_password;
	$gaSql['db']         = $var_db_name;
	$gaSql['server']     = $var_server;
	
	/* REMOVE THIS LINE (it just includes my SQL connection user/pass) */
	/*include( $_SERVER['DOCUMENT_ROOT']."/datatables/mysql.php" );*/
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * Local functions
	 */
	function fatal_error ( $sErrorMessage = '' )
	{
		header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
		die( $sErrorMessage );
	}

	
	/* 
	 * MySQL connection
	 */
	if ( ! $gaSql['link'] = mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) )
	{
		fatal_error( 'Could not open connection to server' );
	}

	if ( ! mysql_select_db( $gaSql['db'], $gaSql['link'] ) )
	{
		fatal_error( 'Could not select database ' );
	}

	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
			intval( $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	$sOrder = "";
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
				 	mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
			{
				$sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
			}
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= "`".$aColumns[$i]."` LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
		";

	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_error() );
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`)
		FROM   $sTable
	";
	$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "version" )
			{
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] != ' ' )
			{
				/* General output */
				if ($aColumns[$i] == "sordno"){
					$pon = $aRow[ $aColumns[$i] ];
					$row3[] = $aRow[ $aColumns[$i] ];
					$row[] = $aRow[ $aColumns[$i] ];
				}else{
					if ($aColumns[$i] == "scustcd"){
						$pcust = $aRow[ $aColumns[$i] ];
						$sqlcust = "select name from customer_master";
        				$sqlcust .= " where custno = '$pcust'";   
       					$tmpcust = mysql_query($sqlcust) or die ("Cant get custname : ".mysql_error());
        				if (mysql_numrows($tmpcust) >0) {
          					$rstcust = mysql_fetch_object($tmpcust);
          					$var_cname = $rstcust->name;
        				} else { $var_cname = $pcust; }
        				$row[] = $var_cname;
					}else{
						if ($aColumns[$i] == "stat"){
							$pstat = $aRow[ $aColumns[$i] ];
							$row[] = $aRow[ $aColumns[$i] ];
						}else{
							$row[] = $aRow[ $aColumns[$i] ];
						}
					}
				}
			}
		}
		
		$sqlinv = "select invno FROM csalesmas, cinvoicemas ";
        $sqlinv .= " WHERE smthyr = mthyr AND scustcd = custcd "; 
        $sqlinv .= " AND sordno = '".$pon."'";
		$rs_result2 = mysql_query($sqlinv); 
		while ($rowy = mysql_fetch_assoc($rs_result2)) { 			
			$invno = htmlentities($rowy['invno']);
		}
		
		$urlvieinv = 'vm_invoice.php?sorno='.$invno.'&custcd='.$pcust.'&menucd='.$var_menucode;
		if ($var_accvie == 0){
        	$row[] = '<a href="#">'.$invno.'</a>';
        }else{
            $row[] = '<a href="'.$urlvieinv.'">'.$invno.'</a>';'</td>';
        }
        
        $urlvie = 'vm_csales.php?sorno='.$pon.'&custcd='.$pcust.'&menucd='.$var_menucode;
        if ($var_accvie == 0){
            $row[] = '<a href="#">[VIEW]</a>';
        }else{
            $row[] = '<a href="'.$urlvie.'">[VIEW]</a>';
        }
        
        $urlpopoldver = 'upd_csalesoldver.php?sorno='.$pon.'&custcd='.$rowq['scustcd'].'&menucd='.$var_menucode;
        $urlpopnewver = 'upd_csalesnewver.php?sorno='.$pon.'&custcd='.$rowq['scustcd'].'&menucd='.$var_menucode;
		if ($var_accupd == 0){
			$row[] = '<a href="#">[EDIT]</a>';
	    }else{
	    	if ($pstat == "C"){
	    		$row[] = '<a href="#" title="This Counter Sales is Cancelled; Edit Is Not Allow">[EDIT]</a>';
	        }else{ 
		        $row[] = '<a href="'.$urlpopoldver.'">[EDIT_OLD]</a> <a href="'.$urlpopnewver.'">[EDIT_NEW]</a>';
	        }
	    }
	    
		if ($var_accdel == 0){
	    	$row[] = '<input type="checkbox" DISABLED  name="procd[]" value="'.$values.'" />'.'</td>';
	    }else{
	        $values = implode(',', $row3);	
	       	$row[] = '<input type="checkbox" name="salorno[]" value="'.$values.'" />';
    	}
           		
        if ($var_accdel == 0){
	    	$row[] = '<input type="checkbox" DISABLED  name="procd[]" value="'.$values.'" />';
	    }else{
	        $values = implode(',', $row3);	
	        $row[] = '<input type="checkbox" name="salorno[]" value="'.$values.'" />';
    	} 		
		//----------------------------------------------------
		
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
?>

