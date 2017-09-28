<?php

// Full-Text Search Example
// http://www.phpfreaks.com/tutorials/129/0.php
// Create the search function:

function searchForm()
{
  // Re-usable form
  
  // variable setup for the form.
  $searchwords = (isset($_GET['words']) ? htmlspecialchars(stripslashes($_REQUEST['words'])) : '');

  $range_start = (isset($_GET['start']) ? htmlspecialchars(stripslashes($_REQUEST['start'])) : '');
  $range_stop  = (isset($_GET['stop'])  ? htmlspecialchars(stripslashes($_REQUEST['stop']))  : '');

  echo '<form method="get" action="'.$_SERVER['PHP_SELF'].'">';
  echo '<input type="hidden" name="cmd" value="search" />';
  echo '<span class="searchcaptions">Text: <input type="text" size="60" name="words" value="'.$searchwords.'" /> ';
  echo '&nbsp;</span>';
  echo '<br /><br />';

  echo 'Start ID: <input type="text" size="6" name="start" value="'.$range_start.'" /> ';
  echo '&nbsp;&nbsp; End ID: <input type="text" size="6" name="stop" value="'.$range_stop.'" /></i></font> ';

  echo '<input type="submit" value="Search" />';

  echo '<br /><br />';

/*
  $boolean = (($_GET['mode'] == 'boolean') ? ' selected="selected"' : '' );
  // $normal = (($_GET['mode'] == 'normal') ? ' selected="selected"' : '' );
  echo '&nbsp;';
  echo '<select name="mode">';
  echo '<option value="boolean"'.$boolean.'>Boolean</option>';
  echo '<option value="normal"'.$normal.'>normal</option>';
  echo '</select> ';
*/
  echo '</form>';
}

?>
