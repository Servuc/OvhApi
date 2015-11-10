# OvhApi
Ask the OVH API easily with PHP, this is simple example to link [my blog post](http://servuc.fr/blog/?p=110).

My version use **curl** instead another the way of OVH.

## Setup project

Just check if you have `php5-cli` and `php5-curl` on your computer :

```
sudo apt-get install php5-cli php5-curl
```

## Use the CLI version

This *cli* scripts permit to get informations about domains linked to your account.

DO NOT FORGET to set you *application key* and your *application secret key* to test it ;)

This use is very simply :

```
php cliAskDomains.php -g #For credentials
php cliAskDomains.php -d #List your domains
```
