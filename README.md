# OvhApi
Ask the OVH API easily with PHP, this is simple example to link [my blog post](http://servuc.fr/blog/?p=110).

My version use **curl** instead OVH way.

You can get your app key [here :)](https://eu.api.ovh.com/createApp/).

## Setup project

Just check if you have `php5-cli` and `php5-curl` on your computer for CLI usage :

	sudo apt-get install php5-cli php5-curl

## Use the CLI version

This *cli* scripts permit to get informations about domains linked to your account.

DO NOT FORGET to set you *application key* and your *application secret key* to test it ;)

This use is very simply :

	php cliAskDomains.php -g #For credentials
	php cliAskDomains.php -d #List your domains


## Use the WEB version

**Web version should not be use like my example because there is no security !**

## Help

Try to add in top of example : 

	ini_set('display_errors', 1);
    error_reporting(E_ALL); 