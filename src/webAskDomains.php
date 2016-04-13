<?php
session_start();
include "OvhApi.php";

//Your app secret keys ;)
$AK = "***********************";
$AS = "***********************";

$myOut = "";

//User doesn't ask something, so menu or connect
if( ! isset($_SESSION["ck"]))
{
		$myAccess = '{"accessRules": [{"method": "GET","path": "/*"},{"method": "POST","path": "/*"},{"method": "PUT","path": "/*"},{"method": "DELETE","path": "/*"}]}';
		OvhApi::getCredential($AK, OvhApi::$roots["ovh-eu"], $myAccess, false);
}
else
{
	if(isset($_GET["ask"]))
	{
		switch($_GET["ask"])
		{
			case "get-domains":
				$myOvh = new OvhApi(OvhApi::$roots["ovh-eu"], $AK, $AS, false);
				$myOut = $myOvh->get("/domain");
				break;
		}
	}
}

?>

<html>
<head>
	<title>OvhApi</title>
</head>
<body>
	<h1>OvhApi by <a href="https://github.com/Servuc">@Servuc</a></h1>
	<?php if(is_array($myOut))
	{
		?>
		<h2>Last request output</h2>
		<div>
			<pre>
				<?php print_r($myOut); ?>
			</pre>
		</div>
		<?php
	}
	?>
	<h2>Call API</h2>
	<ul>
		<li>
			<a href="?ask=get-domains">Domains</a>
		</li>
	</ul>
</body>
</html>
