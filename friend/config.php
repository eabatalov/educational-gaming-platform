<?php
//Database Connection Settings
define ('hostnameorservername','localhost'); //Your Server Name or host Name goes in here
define ('serverusername','root'); //Your database Username goes in here
define ('serverpassword','nosql'); //Your database Password goes in here
define ('databasenamed','friend'); //Your database Name goes in here

global $connection;
$connection = @mysql_connect(hostnameorservername,serverusername,serverpassword) or die('Connection could not be made to the SQL Server. Please report this system error at <font color="blue">info@servername.com</font>');
@mysql_select_db(databasenamed,$connection) or die('Connection could not be made to the database. Please report this system error at <font color="blue">info@servername.com</font>');	
?>
