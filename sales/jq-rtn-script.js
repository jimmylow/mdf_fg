$(document).ready(function(){
		
    // Use the .autocomplete() method to compile the list based on input from user
    $('#procd1').autocomplete({
        source: 'get_rtn_cd.php',
        minLength: 0,
        select: function(event, ui) {
            var $itemrow = $(this).closest('tr');
                    // Populate the input fields from the returned values
                    $itemrow.find('#procd1').val(ui.item.prod_code);
                    $itemrow.find('#proconame1').val(ui.item.prdesc);
                    $itemrow.find('#prouom1').val(ui.item.pruom);
                   
                    getUprice(1);
                    
                    // Give focus to the next input field to recieve input from user
                    $('#proordqty1').focus();

            return false;
	    }
    // Format the list menu output of the autocomplete
    }).data( "autocomplete" )._renderItem = function( ul, item ) {
        return $( "<li></li>" )
            .data( "item.autocomplete", item )
            .append( "<a>" + item.prod_code + " - " + item.prdesc + "</a>" )
            .appendTo( ul );
    };

    // Use the .autocomplete() method to compile the list based on input from user
    $('#rejprocd1').autocomplete({
        source: 'get_rtn_cd.php',
        minLength: 0,
        select: function(event, ui) {
            var $itemrow = $(this).closest('tr');
                    // Populate the input fields from the returned values
                    $itemrow.find('#rejprocd1').val(ui.item.prod_code);
                    $itemrow.find('#prorejuom1').val(ui.item.pruom);
                      
                    chkuom(1);                   
                    // Give focus to the next input field to recieve input from user
                    //$('#proothqty1').focus();

            return false;
	    }
    // Format the list menu output of the autocomplete
    }).data( "autocomplete" )._renderItem = function( ul, item ) {
        return $( "<li></li>" )
            .data( "item.autocomplete", item )
            .append( "<a>" + item.prod_code + " - " + item.prdesc + "</a>" )
            .appendTo( ul );
    };

    // Get the table object to use for adding a row at the end of the table
    var $itemsTable = $('#itemsTable');

   
    // Add row to list and allow user to use autocomplete to find items.
    $("#addRow").bind('click',function(){
    	var table = document.getElementById('itemsTable');
	    var rowCount = table.rows.length; 

    	 // Create an Array to for the table row. ** Just to make things a bit easier to read.
    var idprocd = "procd"+rowCount;
    var idproname = "proconame"+rowCount;
    var idproordqty = "proordqty"+rowCount;
    var idprorejqty = "prorejqty"+rowCount;
    var idrejprocd = "rejprocd"+rowCount;
    var idproothqty = "proothqty"+rowCount;
    var idprouom = "prouom"+rowCount;
    var idprorejuom = "prorejuom"+rowCount;
    var idprooupri = "prooupri"+rowCount;
    var idprotqty = "protqty"+rowCount;
    var idproouamt = "proouamt"+rowCount;
    
    var rowTemp = [
          '<tr class="item-row">',
          '<td><input name="seqno[]" id="seqno" value="1" readonly="readonly" style="width: 27px; border:0;"></td>',
          '<td><input name="procd[]" value="" tProCd'+rowCount+'="1" id="'+idprocd+'" class="autosearch" style="width: 100px" onchange ="upperCase(this.id)" ></td>',
          '<td><input name="procdname[]" value="" class="tInput" id="'+idproname+'" readonly="readonly" style="border-style: none; border-color: inherit; border-width: 0; width: 150px;"></td>',
          '<td><input name="prouom[]"  id="'+idprouom+'" readonly="readonly" style="width: 50px; border-style: none; border-color: inherit; border-width: 0;"></td>',
		      '<td><input name="prooupri[]" id="'+idprooupri+'" style="width: 70px; text-align:right;"></td>',
          '<td><input name="proorqty[]" id="'+idproordqty+'" style="width: 50px; text-align:center;"></td>',
          '<td><input name="prorejqty[]" id="'+idprorejqty+'" style="width: 50px; text-align:center;"></td>',
          '<td><input name="rejprocd[]" value=""  id="'+idrejprocd+'" class="autosearch" style="width: 100px" onchange ="upperCase(this.id)" ></td>',
          '<td><input name="prorejuom[]"  id="'+idprorejuom+'" readonly="readonly" style="width: 50px; border-style: none; border-color: inherit; border-width: 0;"></td>',
          '<td><input name="proothqty[]" id="'+idproothqty+'" onBlur="calcAmt('+rowCount+');" style="width: 50px; text-align:center;"></td>',
          '<td><input name="protqty[]" id="'+idprotqty+'" readonly style="width: 50px; border-style: none; border-color: inherit; border-width: 0; text-align:center;"></td>',
		      '<td><input name="proouamt[]" id="'+idproouamt+'" style="width: 80px; border-style: none; border-color: inherit; border-width: 0; text-align:right;">	</td>',
          '</tr>'
    ].join('');
	
    
        var $row = $(rowTemp);

        // save reference to inputs within row
        var $seqno  	        = $row.find('#seqno');
        var $procd   	        = $row.find('#procd'+rowCount);
        var $proconame	      = $row.find('#proconame'+rowCount);
        var $proorqty	        = $row.find('#proorqty'+rowCount);
        var $prouom           = $row.find('#prouom'+rowCount);
        var $prooupri	        = $row.find('#prooupri'+rowCount);
        var $prorejqty	      = $row.find('#prorejqty'+rowCount);
        var $rejprocd   	    = $row.find('#rejprocd'+rowCount);
        var $prorejuom        = $row.find('#prorejuom'+rowCount);
        var $proothqty	      = $row.find('#proothqty'+rowCount);
        var $protqty	        = $row.find('#protqty'+rowCount);
        var $proouamt	        = $row.find('#proouamt'+rowCount);
       
       if ( $('#procd1:last').val() !== '' ) {

            // apply autocomplete widget to newly created row
            $row.find('#procd'+rowCount).autocomplete({
                source: 'get_rtn_cd.php',
                minLength: 0,
                select: function(event, ui) {
                    $procd.val(ui.item.prod_code);
                    $proconame.val(ui.item.prdesc);
                  	$prouom.val(ui.item.pruom);
                    	
                    getUprice(rowCount);  
                    // Give focus to the next input field to recieve input from user
                    //$proorqty.focus();
                    $('#proordqty'+rowCount).focus();

                    return false;
                }
            }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                    .data( "item.autocomplete", item )
                    .append( "<a>" + item.prod_code + " - " + item.prdesc + "</a>" )
                    .appendTo( ul );
            };
            
            // apply autocomplete widget to newly created row
            $row.find('#rejprocd'+rowCount).autocomplete({
                source: 'get_rtn_cd.php',
                minLength: 0,
                select: function(event, ui) {
                    $rejprocd.val(ui.item.prod_code);
                    $prorejuom.val(ui.item.pruom);
 
                    chkuom(rowCount); 
                    // Give focus to the next input field to recieve input from user
                    //$proorqty.focus();
                    //$('#proothqty'+rowCount).focus();

                    return false;
                }
            }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )
                    .data( "item.autocomplete", item )
                    .append( "<a>" + item.prod_code + " - " + item.prdesc + "</a>" )
                    .appendTo( ul );
            };            
          	
			// Add row after the first row in table
            $seqno.val(rowCount);
           
            $('.item-row:last', $itemsTable).after($row);
            $($proorqty).focus();

        } // End if last itemCode input is empty
        return false;
    });
    
   
    $('#itemCode').focus(function(){
     //   window.onbeforeunload = function(){ return "You haven't saved your data.  Are you sure you want to leave this page without saving first?"; };
    });
    

}); // End DOM

	