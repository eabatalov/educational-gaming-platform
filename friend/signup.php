<?php

session_start();
ob_start();

//This function will uppercase every first letter of users fullnames
function format_fullnames($name=NULL) 
{
	if (empty($name))
		return false;
	$name = strtolower($name);
	$names_array = explode('-',$name);
	for ($i = 0; $i < count($names_array); $i++) {	
		if (strncmp($names_array[$i],'mc',2) == 0 || preg_match('/^[oO]\'[a-zA-Z]/',$names_array[$i])) 
		{
			$names_array[$i][2] = strtoupper($names_array[$i][2]);
		}
		$names_array[$i] = ucfirst($names_array[$i]);
	}
	$name = implode('-',$names_array);
	return ucwords($name);
}

//Include the database connection file
include "config.php";

//Check to see if the submit button has been clicked to process data
if(isset($_POST["submitted"]) && $_POST["submitted"] == "yes")
{
	//Variables Assignment
	$fullname = format_fullnames(trim(strip_tags($_POST['fullname'])));
	$username = trim(strip_tags($_POST['username']));
	$user_email = trim(strip_tags($_POST['email']));
	$user_password = trim(strip_tags($_POST['passwd']));
	$encrypted_md5_password = md5($user_password);
	
	$check_username_for_duplicates = mysql_query("select * from `friendship_system_users_table` where `username` = '".mysql_real_escape_string($username)."'");
	
	$check_email_for_duplicates = mysql_query("select * from `friendship_system_users_table` where `email` = '".mysql_real_escape_string($user_email)."'");
	
	//Validate against empty fields
	if($fullname == "" || $username == "" || $user_email == "" || $user_password == "")
	{
		$error = '<br><div class="info">Sorry, all fields are required to create a new account. Thanks.</div><br>';
	}
	elseif(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $user_email))
	{
		$error = '<br><div class="info">Sorry, Your email address is invalid, please enter a valid email address to proceed. Thanks.</div><br>';
	}
	else if(mysql_num_rows($check_username_for_duplicates) > 0) //Username address is unique within this system and must not be more than one
	{
		$error = '<br><div class="info">Sorry, the username <b>'.$username.'</b> already exist in our database which means it has been taken by someone else.<br>Please enter a different username of your choice to proceed. Thanks.</div><br>';
	}
	else if(mysql_num_rows($check_email_for_duplicates) > 0) //Email address is unique within this system and must not be more than one
	{
		$error = '<br><div class="info">Sorry, your email address already exist in our database and duplicate email addresses are not allowed for security reasons.<br>Please enter a different email address to proceed or login with your existing account. Thanks.</div><br>';
	}
	else
	{   $dbc = mysqli_connect(hostnameorservername,serverusername,serverpassword,databasenamed)
        or die('Error connecting database');
        $query1 = "Insert into friendship_system_users_table (fullname,username,email,password,date) values('$fullname', '$username', '$user_email', '$encrypted_md5_password', 'date()')";
		if(mysqli_query($dbc, $query1))
		{
			$_SESSION["VALID_USER_ID"] = $username;
			$_SESSION["USER_FULLNAME"] = strip_tags($fullname);
			header("location: index.php?page_owner=".base64_encode($username));
		}
		else
		{
			$error = '<br><div class="info">Sorry, your account could not be created at the moment. Please try again or contact this website admin to report the error message if this problem persist. Thanks.</div><br>';
		}
	}
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Educational Network</title>
<link href="../../css/demonstration_body_background.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="../../images/favicon.ico" >
<link rel="icon" type="image/gif" href="../../images/animated_favicon1.gif" >




<!-- Required header file -->
<link href="css/style.css" rel="stylesheet" type="text/css">





</head>
<body>
<center>
<div id="all_centered">
<center>
<div id="centered"><br>

<div id="vasp" style="">Connect with others and learn faster!</div><br clear="all" /><br clear="all" /><br clear="all" />





<!-- Code Begins -->
<center>
<div class="main_wrapper">

<br clear="all">
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<h2 align="left" style="margin-top:0px;">Users Registration</h2><br />

<div align="left" style="font-family:Verdana, Geneva, sans-serif; font-size:11px; margin-bottom:10px;">Please complete the form provided below to demonstrate this system.</div><br />

<div style="width:115px; padding-top:10px;float:left;" align="left">Your Fullname:</div>
<div style="width:300px;float:left;" align="left"><input type="text" name="fullname" id="fullname" value="<?php echo strip_tags(@$_POST["fullname"]); ?>" class="textAreaBoxInputs"></div><br clear="all"><br clear="all">


<div style="width:115px; padding-top:10px;float:left;" align="left">Your Username:</div>
<div style="width:300px;float:left;" align="left"><input type="text" name="username" id="username" value="<?php echo strip_tags(@$_POST["username"]); ?>" class="textAreaBoxInputs"></div><br clear="all"><br clear="all">


<div style="width:115px; padding-top:10px;float:left;" align="left">Email Address:</div>
<div style="width:300px;float:left;" align="left"><input type="text" name="email" id="email" value="<?php echo strip_tags(@$_POST["email"]); ?>" class="textAreaBoxInputs"></div><br clear="all"><br clear="all">


<div style="width:115px; padding-top:10px;float:left;" align="left">Desired Password:</div>
<div style="width:300px;float:left;" align="left"><input type="password" name="passwd" id="passwd" value="" class="textAreaBoxInputs"></div><br clear="all"><br clear="all">


<div style="width:115px; padding-top:10px;float:left;" align="left">&nbsp;</div>
<div style="width:300px;float:left;" align="left">
<input type="hidden" name="submitted" id="submitted" value="yes">
<input type="submit" name="submit" id="" value="Submit" style="margin-right:50px;" class="general_button_g">
<a href="login.php" style="text-decoration:none;" class="general_button_g">Back to Login</a>

</div>

</form>
<br clear="all"><br clear="all">
<div style="width:450px;float:left;" align="left"><?php echo @$error; ?></div><br clear="all">

</div>
</center>
<!-- Code Ends -->















<p style="margin-bottom:160px;">&nbsp;</p>
</div>
</center>
</div>
</center>
</body>
</html>