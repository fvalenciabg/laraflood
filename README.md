# Laraflood - Antiflood system for Laravel using Cache

This tool designed for limiting some user requests.

This tool is based on: [ircop/Laraflood](https://github.com/ircop/antiflood), I have added / modified things that have been useful for my current project and I publish it in case it can be useful to someone!



## INSTALL

1: install via composer

```
composer require vincendev/laraflood
```
If you're using laravel 5.5: steps 2 and 3 are not necessary.

2: add service provider to `providers` array in `config/app.php`:
```
Vincendev\Laraflood\LarafloodServiceProvider::class,
```

3: add facade alias to `aliases` array in `config/app.php`:
```
'Laraflood' => Vincendev\Laraflood\FacadeLaraflood::class,
```



## Usage

Checking for record existance with given identity and add attempt.

$identity - can be ip-address, user id, anything unique. Default is ip-address.

$action - What is the user trying to do?  Submit-post, Login, Search, etc. Default is 'default'.

$maxAttempts - How many attempts do you want to give the user? Default is 5.

$minutes - Waiting time after attempts are over. Default is 5 minutes.


```php

Laraflood::check( $identity = 'ip', $action = 'default', $maxAttempts = 5, $minutes = 5 );
Laraflood::check();
Laraflood::check( $user->id , 'submit-comment');
Laraflood::check( $user->id , 'report-comment', 1, 5);

/* Return bool */

```
Only check without add attempt.
```php
Laraflood::checkOnly( $identity = 'ip', $action = 'default', $maxAttempts = 5, $minutes = 5 );
Laraflood::checkOnly();
Laraflood::checkOnly( $user->id , 'submit-comment');
Laraflood::checkOnly( $user->id , 'report-comment', 1, 5);

/* Return bool */

```


Add attempt for given $identity with ·$action on given $minutes.
'ip' by default is the real user ip.
```php
Laraflood::addAttempt( $identity = 'ip', $action = 'default', $minutes = 5 );
Laraflood::addAttempt();
Laraflood::addAttempt('ip', 'like-post');
Laraflood::addAttempt( $user->id ,'default', 5 );

/* Void */

```

Get time left for given identity & action.
```php

Laraflood::timeLeft( $identity = 'ip', $action = 'default');
Laraflood::timeLeft();
Laraflood::timeLeft( 'ip', 'like-post');

/* Return string */
	# X hours
	# X minutes
	# X seconds



```

Returns array for given identity & action
```php

Laraflood::get($identity = 'ip', $action = 'default')
Laraflood::get();
Laraflood::get( 'ip', 'like-post');

/*
array:3 [▼
  "action" => "default"
  "attempts" => 1
  "expiration" => "2019-09-18 19:51:01.175237"
]
*/

```


## Examples:

#### Limiting wrong login attempts for given IP address:


This example limiting wrong login attempts from one ip-address to 5 tryes per 20 minutes:

```php
public function postLogin()
{
	
/**
* If thes user ip has >= 5 failed login attempts in last 5 minutes, redirect user
* back with error:
*/

	if( Laraflood::check( 'ip' , 'login', 5, 5 ) === FALSE )
		return redirect()->back()->withErrors(["AntiFlood Protection! Try again in ".Laraflood::timeLeft('ip','login')." ."]);
	
	/**
	* Your code here..
	*/

}
```


#### Increment post views .

This code shows how increment post views only 1 view every 24 hours..
```php
public function incrementPostViews()
{
		$action = "post-visit:" . $post->id; // Post unique ID
		$visitsPerUser = 1;
		$minutes = 1440; // 24Hours
        if(Laraflood::checkOnly('ip', $action, $visitsPerUser)){
			/**
			* Increment post views
			* Your code here..
			*/
			Laraflood::addAttempt('ip', $action, $minutes);
        }

}
```

