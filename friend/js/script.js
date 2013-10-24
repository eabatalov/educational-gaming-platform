


//Auto call Notification Function
$(document).ready(function()
{
	setInterval(function() { notification_display(); }, 3000);
});


//Notification Function
function notification_display()
{
	var page_owner = $("#page_owner").val();
	var logged_in_username = $("#logged_in_username").val();
	var dataString = "logged_in_username=" + logged_in_username + "&page_owner=" + page_owner + "&page=check_friends_request";
	$.ajax({  
		type: "POST",  
		url: "add_or_cancel_friend_ship.php",  
		data: dataString,
		cache: false,
		beforeSend: function() {},  
		success: function(response)
		{
			if(response == "") 
			{}
			else
			{
				$("#notification_wrapper").hide().show().html(response);
			}
		}
	});
}


//Perform the Following and Unfollowing work
function add_or_cancel_friend_ship(logged_in_username,page_owner,action)
{
    var dataString = "logged_in_username=" + logged_in_username + "&page_owner=" + page_owner + "&page=" + action;
    $.ajax({  
        type: "POST",  
        url: "add_or_cancel_friend_ship.php",  
        data: dataString,
		cache: false,
        beforeSend: function() 
        {
            if ( action == "add_as_friend" )
            {
                $("#add_as_friend").hide();
                $("#loading_friend_ship_activities").html('<img src="images/loading.gif" align="absmiddle" alt="Loading...">');
            }
            else if ( action == "cancel_friendship" )
            {
				$("#request_sent").html('');
                $("#cancel_friendship").hide();
                $("#loading_friend_ship_activities").html('<img src="images/loading.gif" align="absmiddle" alt="Loading...">');
            }
            else { }
        },  
        success: function(response)
        {
            if ( action == "cancel_friendship" )
			{
                $("#loading_friend_ship_activities").html('');
                $("#add_as_friend").show();
				if(response == "cancelled_successfully")
				{
					$("#add_page_owner_id"+page_owner).show();
					$("#page_owner_friends_id"+logged_in_username).hide();
				}
            }
            else if ( action == "add_as_friend" )
			{
                $("#loading_friend_ship_activities").html('');
				$("#add_page_owner_id"+page_owner).hide();
				$("#page_owner_friends_id"+logged_in_username).show();
				
				if(response == "friend_ship_confirmed")
				{
					$("#request_sent").html('');
                	$("#cancel_friendship").show();
					
				}
				else if(response == "request_sent_successfully")
				{
					$("#request_sent").html('<span class="general_button_g" style="float:none;opacity:0.5; cursor:default;">Request Sent</span><br clear="all"><br clear="all"><br clear="all"><br clear="all">');
                	$("#cancel_friendship").show();
				}
            }
            else { }
			$("#notification_wrapper").hide();
        }
    }); 
}