


After you have opened the directory named friend, do the following:



(1) Open the file named friend/config.php and fill in your database connection settings

(2) Open the file named friend/TABLE.sql, copy the queries to your server and run them to create the required tables and default data for this system.

(3) Upload the directory named friend and its entire content to your server, location this directory via your web browser to see the system and you are done.


Detailed Description



The first table is where the details of the users in the system are stored and retrieved during Sign-up and Login process.The second table is where the details of the users who are confirmed as friends are stored for friendship purpose. The third table is where the details of every sent friend request are stored waiting for an acceptance or rejection by the user who the request was sent.

When a user sends a friend request by clicking on the Add as Friend button, this button will automatically change to Request Sent on successful sending of the request with an option to Cancel the sent request in case the user who sent the request wishes to stop the request.

Once a user who has been sent a friend request logs into the system or accesses the system, a notification for the request will be shown to this user from the top of the page with options to Accept the sent request or Decline the request depending on the user's choice of action.

If the user who has been sent a friend request chooses to accept the request, the request will be deleted from the Friends Request table and be stored in the Confirmed Friends table and both the user who sent the request and the user who the request was sent to will now see each other in their friends list.

If a user who has been sent a friend request chooses to decline the request, the request will be deleted from the Friends Request table.
