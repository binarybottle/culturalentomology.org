<?php
// From: http://www.coderslexicon.com/really-simple-php-login-logout-script-example/
// First start a session. This should be right at the top of your login page.
session_start();

// Check to see if this run of the script was caused by our login submit button being clicked.
if (isset($_POST['login-submit'])) {

    // Also check that our email address and password were passed along. If not, jump
    // down to our error message about providing both pieces of information.
    if (isset($_POST['user']) && isset($_POST['pass'])) {
        $user = $_POST['user'];
        $pass = $_POST['pass'];
												
        // Load username and password for verification:
        include_once("../db/culturalentomology_db.php");

        // If successful, now set up session variables for the user 
        // and store a flag to say they are authorized.
      	// These values follow the user around the site and will be tested on each page.  
//        if ($username == $user && $password == hash('sha256', $pass)) {
        if ($username == $user && $password == $pass) {

    	    // is_auth is important here because we will test this to make sure 
            // they can view other pages that are needing credentials.
	    $_SESSION['is_auth'] = true;
            // Once the sessions variables have been set, redirect them.
            header('location: admin_edit.php');
            exit;

        } else {
    	    $error = "Invalid credentials.";
  	}
    } else {
        $error = "Please enter a username and password to login.";
    }
}

?>


<?php
include_once("shared/header.html");
include_once("shared/banner.html");
?>
<title>Administrator login</title>
<div class="main">
<h1>Administer the Cultural Entomology Database</h1>


<!-- This form will post to current page and trigger our PHP script. -->
<form method="post" action="">
  <div class="login-body">
   <?php
    if (isset($error)) {
	echo "<div class='errormsg'>$error</div>";
    }
   ?>
   <div class="form-row">
    <label for="user">Username:</label>
    <input type="text" name="user" id="user" placeholder="Username" maxlength="100">
   </div>
   <div class="form-row">
    <label for="pass">Password:</label>
    <input type="password" name="pass" id="pass" placeholder="Password" maxlength="100">
   </div>
   <div class="login-button-row">
    <input type="submit" name="login-submit" id="login-submit" value="Login" title="Login now">
   </div>
  </div>
</form>

</div>

<? include_once("shared/footer.php"); ?>
