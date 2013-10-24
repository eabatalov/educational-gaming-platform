<?php
session_start();
ob_start();

//Include the database connection file
include "config.php"; 
  $dbc = mysqli_connect(hostnameorservername,serverusername,serverpassword,databasenamed)
        or die('Error connecting database');

//Check to be sure that a valid session has been created
if(isset($_SESSION["VALID_USER_ID"]) && !empty($_SESSION["VALID_USER_ID"]))
{
	//This identifies the owners of pages for Adding and Cancelling Friendship activities
	if(isset($_GET["page_owner"]) && !empty($_GET["page_owner"]))
	{
		$page_owner = strip_tags(base64_decode($_GET["page_owner"]));
	}
	else
	{
		$page_owner = strip_tags($_SESSION["VALID_USER_ID"]);
	}
	
	//Check the database table for the logged in user or page owner information
	$check_user_details = mysqli_query($dbc, "select * from `friendship_system_users_table` where `username` = '".mysqli_real_escape_string($dbc, $page_owner)."'");
	
	//Get all the logged in user or page owner information from the database users table
	$get_user_details = mysqli_fetch_array($check_user_details);
	
	$user_id = strip_tags($get_user_details['id']);
	$fullname = strip_tags($get_user_details['fullname']);
	$username = strip_tags($get_user_details['username']);
	$email = strip_tags($get_user_details['email']);
	echo '<input type="hidden" id="logged_in_username" value="'.strip_tags($_SESSION["VALID_USER_ID"]).'">';
	echo '<input type="hidden" id="page_owner" value="'.$page_owner.'">';
	
	
	
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Educational Network</title>




<!-- Required header files -->
<script type="text/javascript" src="js/jquery_1.5.2.js"></script>
<script type="text/javascript" src="js/script.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css">








</head>
<body>
<center>









<!-- Notification Displayer -->
<div id="notification_wrapper"></div>








<!-- Links for Home Page and Logout -->
<div align="center" style="padding-right:20px;">
<?php if($page_owner != strip_tags($_SESSION["VALID_USER_ID"])){
?><span style="margin-right:100px;" class="ccc"><a href="index.php?page_owner=<?php echo base64_encode($_SESSION["VALID_USER_ID"]); ?>"><font style="font-family:Verdana, Geneva, sans-serif; font-size:15px; color:blue;">Home</font></a></span><?php } ?>
<span class="ccc"><a href="logout.php"><font style="font-family:Verdana, Geneva, sans-serif; font-size:15px; color:blue;">Logout</font></a></span>
</div>






<center>
<div style="width:1070px;">
<div style="width:700px; float:left;" align="left">


<!-- Profile Photo Box -->
<div class="profile_photo_wrapper">
<div style=" font-family:Verdana, Geneva, sans-serif;font-size:18px;"><?php echo $fullname; ?></div><br />
<img src="images/big_avatar.jpg" width="190" style="min-height:100px; height:auto;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;" border="0" /><br clear="all" /><br />
</div><br clear="all" />












<!-- Add or Cancel Friendship Activities Starts Here -->
<?php
 //If the logged-in user is not on his or her page then show him or her the add friend or Cancel Friend button otherwise show him or her the Edit Profile button
if($page_owner != strip_tags($_SESSION["VALID_USER_ID"]))
{
	//Check if the logged in user has already sent request to the owner of the page viewed or not
	$check_for_friend_request = mysqli_query($dbc, "select * from `friend_request` where `username` = '".mysqli_real_escape_string($dbc, $_SESSION["VALID_USER_ID"])."' and `friend` = '".mysqli_real_escape_string($dbc, $page_owner)."'");
	
	if(mysqli_num_rows($check_for_friend_request) > 0)
	{
		?>
        <div class="friend_ship_wrapper" style="padding-bottom:13px;">
        <br clear="all" />
        <span id="request_sent"><span class="general_button_g" style="float:none;opacity:0.5;">Request Sent</span>
        <br clear="all"><br clear="all"><br clear="all"><br clear="all"></span>
        
        <span style="float:none;" class="general_button_r" id="cancel_friendship" onClick="add_or_cancel_friend_ship('<?php echo $_SESSION["VALID_USER_ID"]; ?>','<?php echo $page_owner; ?>','cancel_friendship');">Cancel Request</span>
        
        <span style="display:none;float:none;" class="general_button_g" id="add_as_friend" onClick="add_or_cancel_friend_ship('<?php echo $_SESSION["VALID_USER_ID"]; ?>','<?php echo $page_owner; ?>','add_as_friend');">Add as Friend</span>
        <br clear="all" /><br clear="all" />
        </div>
        <?php
	}
	else
	{
		//Check for the friends of the owner of page viewed
		$check_friend_ship = mysqli_query($dbc, "select * from `my_friends` where `username` = '".mysqli_real_escape_string($dbc, $_SESSION["VALID_USER_ID"])."' and `friend` = '".mysqli_real_escape_string($dbc, $page_owner)."'");
		
		?>
		<div class="friend_ship_wrapper" style="padding-bottom:13px;">
		<?php
		
		if(mysqli_num_rows($check_friend_ship) < 1)
		{
			?>
			<br clear="all" />
			<span id="loading_friend_ship_activities"></span>
             <span id="request_sent"></span>
             
			<span class="general_button_g" style="float:none;" id="add_as_friend" onClick="add_or_cancel_friend_ship('<?php echo $_SESSION["VALID_USER_ID"]; ?>','<?php echo $page_owner; ?>','add_as_friend');">Add as Friend</span>
			
			<span style="display:none;float:none;" class="general_button_r" id="cancel_friendship" onClick="add_or_cancel_friend_ship('<?php echo $_SESSION["VALID_USER_ID"]; ?>','<?php echo $page_owner; ?>','cancel_friendship');">Cancel Request</span>
			<br clear="all" /><br clear="all" />
			<?php
		}
		else
		{
			?>
            <br clear="all" />
			<span id="loading_friend_ship_activities"></span>
             <span id="request_sent"></span>
             
			<span class="general_button_r" style="float:none;" id="cancel_friendship" onClick="add_or_cancel_friend_ship('<?php echo $_SESSION["VALID_USER_ID"]; ?>','<?php echo $page_owner; ?>','cancel_friendship');">Cancel Friend</span>
            
			
			<span style="display:none;float:none;" class="general_button_g" id="add_as_friend" onClick="add_or_cancel_friend_ship('<?php echo $_SESSION["VALID_USER_ID"]; ?>','<?php echo $page_owner; ?>','add_as_friend');">Add as Friend</span>
            <br clear="all" /><br clear="all" />
			<?php
		}
		?>
		</div>
		<?php
	}
}
else
{
	//Do not show the Add or Cancel Friends Button if the owner of the viewed page is the logged in user or session
}
?>    
<!-- Add or Cancel Friendship Activities Ends Here -->   










<!-- Explanation content for how to view a user's page and add the other of the page viewed as friend or cancel friendship -->
<br clear="all" />
<?php if($page_owner == strip_tags($_SESSION["VALID_USER_ID"])){ //Only show this content if this page that is viewed belongs to the logged in user
?>
<div style="float:left; font-family:Verdana, Geneva, sans-serif; font-size:11px; width:300px; margin-top:30px; line-height:20px;">
To Connect with others, please click on any name of the people you may want to add as friends or Cancel Friendship at the right side of this page.<br />
<br />
When the page of that user opens, click on the <br /><b>Add as Friend</b> or <b>Cancel Friend</b> button that you will see above.
</div>
<?php } ?>
<br clear="all" /><br clear="all" />








<!-- Friends List -  These are friends of the logged-in user and not friends of the other of the page viewed -->
<?php 
//Check for the logged in users friends
$check_for_logged_in_users_friends = mysqli_query($dbc, "select * from `my_friends` where `username` = '".mysqli_real_escape_string($dbc, $page_owner)."' order by `id` desc");
?>
<div style="width:300px; float:left;" align="left"><br />
<div class="info" style="font-family:Verdana, Geneva, sans-serif;font-size:16px;-webkit-border-radius: 0px;-moz-border-radius: 0px;border-radius: 0px;"><?php if($page_owner == strip_tags($_SESSION["VALID_USER_ID"])) { ?>Your Friends <?php } else { echo $fullname."'s Friends"; } ?> (<?php echo mysqli_num_rows($check_for_logged_in_users_friends); ?>)</div><br clear="all" />
<div style="overflow-x:hidden;overflow-y:auto;height:510px; width:275px; float:left;" align="left">

<?php 
if(mysqli_num_rows($check_for_logged_in_users_friends) > 0)
{
	//Get the logged in users friends
	while($get_logged_in_users_friends = mysqli_fetch_array($check_for_logged_in_users_friends))
	{
		//Check for the logged-in users friends full details from the users table
		$check_friends_full_details = mysqli_query($dbc, "select * from `friendship_system_users_table` where `username` = '".mysqli_real_escape_string($dbc, strip_tags($get_logged_in_users_friends["friend"]))."'");
		
		if(mysqli_num_rows($check_friends_full_details) > 0)
		{
			//Get friends full details from the users table
			while ($get_friends_full_details = mysqli_fetch_array($check_friends_full_details))
			{
				?>
                <div id="page_owner_friends_id<?php echo strip_tags($get_friends_full_details["username"]); ?>">
				<a href="index.php?page_owner=<?php echo base64_encode(strip_tags($get_friends_full_details["username"])); ?>"><div class="people_you_may_want_to_follow_wrapper">
				<div style="float:left; width:90px;"><img src="images/big_avatar.jpg" class="people_you_may_want_to_add_or_cancel_photos" border="0" align="absmiddle" /></div>
				<div style="float:left; width:140px;"><?php echo strip_tags($get_friends_full_details["fullname"]); ?></div>
				</div>
                </a>
                </div>
                <br clear="all" />
				<?php
			}
		}
	}
}
else
{
	echo '<div class="infor" align="left">Currently, <b>'.$fullname.'</b> does not have any friend at the moment.</div>';
}
?>

</div>
<br clear="all" />
</div>




</div>














<div style="width:370px; float:right;" align="right">



<!-- These are people a logged-in user may like to view their page and add them as friends or cancel their friendship if they are already friends -->
<div class="info" style=" width:325px;font-family:Verdana, Geneva, sans-serif;font-size:16px;-webkit-border-radius: 0px;-moz-border-radius: 0px;border-radius: 0px;">
People you may want to add as friends</div><br clear="all" />
<div style="overflow-x:hidden;overflow-y:auto;height:510px; width:300px; float:right;">

<?php		
//Check for all the users who are not friends of the logged-in user in the users table as people a logged-in user may want to add as friend
$check_users_in_the_system = mysqli_query($dbc, "select * from `friendship_system_users_table` where `username` not in('".mysqli_real_escape_string($dbc, $_SESSION["VALID_USER_ID"])."') order by `id` desc");

if(mysqli_num_rows($check_users_in_the_system) > 0)
{
	//Get all the users who are not friends of the logged-in user in the users table as people a logged-in user may want to add as friend
	while ($get_users_in_the_system = mysqli_fetch_array($check_users_in_the_system))
	{
		//Do not show the logged in users info among the people he or she may want to add as friends
		if($get_users_in_the_system["username"] == $_SESSION["VALID_USER_ID"]) {} // Do not show the logged in user in the list of people to add or cancel friendship to avoid adding self as friend 
		else 
		{
			$check_logged_in_user_friends = mysqli_query($dbc, "select * from `my_friends` where `username` = '".mysqli_real_escape_string($dbc, $_SESSION["VALID_USER_ID"])."' and `friend` = '".mysqli_real_escape_string($dbc, strip_tags($get_users_in_the_system["username"]))."'");
			
			if(mysqli_num_rows($check_logged_in_user_friends) > 0) 
			{
				//Do not show people who are already friends with the logged in user in this list or area
			}
			else 
			{
				//Check whether this user has already been sent friends request
				$check_friend_request_sent = mysqli_query($dbc, "select * from `friend_request` where `username` = '".mysqli_real_escape_string($dbc, $_SESSION["VALID_USER_ID"])."' and `friend` = '".mysqli_real_escape_string($dbc, strip_tags($get_users_in_the_system["username"]))."'");
			
				if(mysqli_num_rows($check_friend_request_sent) > 0) 
				{
					//Do not show people that the logged in user has already sent friends request which are still pending
				}
				else 
				{
					?>
					<div id="add_page_owner_id<?php echo strip_tags($get_users_in_the_system["username"]); ?>">
					<a href="index.php?page_owner=<?php echo base64_encode(strip_tags($get_users_in_the_system["username"])); ?>"><div class="people_you_may_want_to_follow_wrapper">
					<div style="float:left; width:90px;"><img src="images/big_avatar.jpg" class="people_you_may_want_to_add_or_cancel_photos" border="0" align="absmiddle" /></div>
					<div style="float:left; width:140px;"><?php echo strip_tags($get_users_in_the_system["fullname"]); ?></div>
					</div></a><br clear="all" />
					</div>
					<?php
				}
			}
		}
	}
}
else
{
	echo '<div class="info" align="left">There is no user in this system at the moment.</div>';
}

?>
</div>
</div>





</div>
















<br clear="all">
</center>
<p style="padding-bottom:100px;">&nbsp;</p>
</center>



</body>
</html>
<?php
}
else
{
	//Redirect user back to login page if there is no valid session created or the created session is an empty field
	header("location: login.php");
}
?>