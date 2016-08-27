# Twitter notification channel for Laravel 5.3

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/twitter.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/twitter)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/twitter/master.svg?style=flat-square)](https://travis-ci.org/laravel-notification-channels/twitter)
[![StyleCI](https://styleci.io/repos/65847386/shield)](https://styleci.io/repos/65847386)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/de277182-faa4-4576-bebb-9f201e27960a.svg?style=flat-square)](https://insight.sensiolabs.com/projects/de277182-faa4-4576-bebb-9f201e27960a)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/twitter.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/twitter)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/twitter/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/twitter/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/twitter.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/twitter)

This package makes it easy to send notifications using [Twitter](https://dev.twitter.com/rest/public) with Laravel 5.3.

## Contents

- [Installation](#installation)
	- [Setting up the Twitter service](#setting-up-the-Twitter-service)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

You can install this package via composer:

``` bash
composer require laravel-notification-channels/twitter
```

Next add the service provider to your `config/app.php`:

```php
...
'providers' => [
	...
	 NotificationChannels\Twitter\TwitterServiceProvider::class,
],
...
```



### Setting up the Twitter service

You will need to [create](https://apps.twitter.com/) a Twitter app in order to use this channel. Within in this app you will find the `keys and access tokens`. Place them inside your `.env` file. In order to load them, add this to your `config/services.php` file:

```php
...
'twitter' => [
	'consumer_key'    => getenv('TWITTER_CONSUMER_KEY'),
	'consumer_secret' => getenv('TWITTER_CONSUMER_SECRET'),
	'access_token'    => getenv('TWITTER_ACCESS_TOKEN'),
	'access_secret'   => getenv('TWITTER_ACCESS_SECRET')
]
...
```

This will load the Twitter app data from the `.env` file. Make sure to use the same keys you have used there like `TWITTER_CONSUMER_KEY`.

## Usage

Follow Laravel's documentation to add the channel to your Notification class:

```php
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;

class  NewForumDiscussionCreated extends Notification
{

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwitterChannel::class];
    }

    public function toTwitter($notifiable) {
        return new TwitterMessage('Why Laravel Notification Channels are awesome -> url:...');
    }
}
```

Take a closer look at the `TwitterMessage` object. Like this, it will send a status update to your Twitter timeline.
````php
public function toTwitter($notifiable) {
    return new TwitterMessage('Why Laravel Notification Channels are awesome -> url:...');
}
```` 
But you can send a Twitter direct message, when you provide a second argument. The first one will be the user, the second one the message:
````php
public function toTwitter($notifiable) {
    return new TwitterMessage('christophrumpel', 'Hey Christoph, whats up?');
}
```` 
Make sure the user is following you on Twitter. If this is not the case, it will throw an Exception.



## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email c.rumpel@kabsi.at instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Christoph Rumpel](https://github.com/christophrumpel)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
