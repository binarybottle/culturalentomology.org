<?php

// Full-Text Search Example
// http://www.phpfreaks.com/tutorials/129/0.php
// Create the search function:

function searchForm()
{
  // Re-usable form
  
  // variable setup for the form.
  $searchwords = (isset($_GET['words']) ? htmlspecialchars(stripslashes($_REQUEST['words'])) : '');
  $boolean = (($_GET['mode'] == 'boolean') ? ' selected="selected"' : '' );
//  $normal = (($_GET['mode'] == 'normal') ? ' selected="selected"' : '' );
  
  echo '<form method="get" action="'.$_SERVER['PHP_SELF'].'">';
  echo '<input type="hidden" name="cmd" value="search" />';
  echo '<span class="searchcaptions"><input type="text" size="30" name="words" value="'.$searchwords.'" /> ';
  echo '&nbsp;</span>';
  echo '<input type="submit" value="Search" />';
/*
  echo '&nbsp;';
  echo '<select name="mode">';
  echo '<option value="boolean"'.$boolean.'>Boolean</option>';
  echo '<option value="normal"'.$normal.'>normal</option>';
  echo '</select> ';
*/
  echo '</form>';
}

?>
