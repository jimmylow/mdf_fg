<?php
  if ($var_accadd == 0){
   	echo '<input disabled="disabled" type=submit name = "Submit" value="Save" id="Save" class="butsub" style="width: 60px; height: 32px" >';
  }else{
   	echo '<input type=submit name = "Submit" value="Save" class="butsub" id="Save" onclick="return confirm(\'Are you sure?\')" style="width: 60px; height: 32px" >';
  }
?>
