PHP Client for Blizzard Web Api
===============================

Overview
--------
This is a PHP client for Blizzard's Battle.net Web API.

Requirements
------------
* PHP 5.2

Optional
--------
* cURL: Highly recommended to use the cURL Http adapter.
* caching: It's strongly recommended to make use of Apc, Memcache or some other persistant caching system

Quickstart
----------
Because this branch if for PHP 5.2, the auto loader functionality of the origional will not work. to include the library, 

		<?php require-once('/full/path/to/lib/WoW/WoWApi.php'); ?>

Please note that the includes in the library assume the following are defined:

ROOT = Document Root
DS = Directory Separator
APP_DIR = Application Directory

For example, I am using cakePHP for my application. My root is the directory that the cakePHP file structure is in, and app is the 'app' folder. You will need to adapt these defined variables to suit your needs.

Usage:

		<?php
		$wowApi = new WowApi(array('region'=>'eu', 
							   'locale'=>'en_GB',
							   ['publicApiKey' =>'<YOUR-KEY>', 
							   'privateApiKey'=>'<YOUR-KEY>']
							   ));
		?>
or

		<?php
		$wowApi = new WowApi();
		$wowApi->setRegion('us');
		$wowApi->setLocale('en_US');
		$wowApi->setPublicApiKey('<YOUR-KEY>');
		$wowApi->setPrivateApiKey('<YOUR-KEY>');
		?>
Note: Most calls have been tested with Api-keys.

### Realm Status
To get a list of all realms you can use the following code.

	$wowApi->getRealmStatus();
To get the status of a specific realm you will need to use the following code.
The `getRealmStatus` function takes a a string as its argument. 

	$wowApi->getRealmStatus($realmname);
	
### Character Information
To get a characters information

	$wowApi->getCharacter($realm, $name[, $fields])
This will return a stdClass. 

	$wowApi->getCharacter($realm, $name[, $fields[, true]])
Or use this to recieve associative arrays
(Note: this works on every call)

### Guild Information
To get guild information
	
	$wowApi->getGuild($realm, $name, $fields)
To get all guild members aswell

	$wowApi->getGuild($realm, $name, array('members'))

### Item Information
This hasn't been implemented by Blizzard yet, but is in their examples already.

	$wowApi->getItem($itemid);

Caching
--------

To use the caching

	$cache = new \BattleNet\Cache\ApcCache();
	$wowApi = new WowApi(array('region'=>'eu', 'cache'=>$cache));
or

	$cache = new \BattleNet\Cache\ApcCache();
	$wowApi = new WowApi(array('region'=>'eu'));
	$wowApi->setCache($cache);	
	
Todo
----

- Have the Api return Call specific responses. 
When calling getGuild you should get a Guild object as response.
When calling getItem you should get a Item object as response.
- add TTL to Http Curl Adapter as it doesn't return an expires header.
- add TTL ArrayCache
- Add more unit tests
- Document Http Adapters

Contributing Developers
-----------------------

Adding new calls is relatively easy.

First you will need to create a new <Resource>Call class that extends AbstractCall.
The main attribute you will need to change is "_path". This defines what url will be called.

Once you have your Call class setup you can request it.
There are 2 ways to request something.
First you can append the <Game>Api Class with your new <Resource>Call 
or you can request the call a little bit more manually

	$wowApi->request(new <Recourse>Call())->getData();
The request method is public for this reason.   