<?php
session_start();
include "OvhApi.php";

//Your app secret keys ;)
$AK = "***********************";
$AS = "***********************";

//Enough parameters
if($argc >= 2)
{
	//First is used to choose what we will done
	switch ($argv[1])
	{
		case "-g" :
			//You MUST do this the first time
            $myAccess = '{"accessRules": [{"method": "GET","path": "/*"},{"method": "POST","path": "/*"},{"method": "PUT","path": "/*"},{"method": "DELETE","path": "/*"}]}';
			OvhApi::getCredential($AK, OvhApi::$roots["ovh-eu"], $myAccess);
		break;

		case "-d" :
        	$myOvh = new OvhApi(OvhApi::$roots["ovh-eu"], $AK, $AS);
			print_r($myOvh->get("/domain"));
		break;
	}

    echo "\n";
}
else
    echo "Usage : " . $argv[0] . " option\n";

?>
