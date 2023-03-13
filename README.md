<img width="1473" alt="image" src="https://user-images.githubusercontent.com/1394539/224646721-20af92f0-2a46-45c0-9700-cfdbe9edf76c.png">


# Twitter notification channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/twitter.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/twitter)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![StyleCI](https://styleci.io/repos/65847386/shield)](https://styleci.io/repos/65847386)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/twitter.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/twitter)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/twitter/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/twitter/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/twitter.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/twitter)

This package makes it easy to send Laravel notifications using [Twitter](https://dev.twitter.com/rest/public). (Laravel 8+)

PS: v.7.0.0 only supports Laravel 10 and PHP 8.1. If you have an older Laravel application, or PHP version, you can use an older version of this package. But be aware that these are no longer maintained.

## Contents

- [About](#about)
- [Installation](#installation)
- [Setting up the Twitter service](#setting-up-the-twitter-service)
- [Usage](#usage)
    - [Publish a Twitter status update](#publish-a-twitter-status-update)
    - [Publish Twitter status update with images](#publish-twitter-status-update-with-images)
    - [Publish Twitter status update with videos](#publish-twitter-status-update-with-videos)
    - [Publish Twitter status update with both images and videos](#publish-twitter-status-update-with-both-images-and-videos)
   - [Send a direct message](#send-a-direct-message)
- [Handle multiple Twitter Accounts](#handle-multiple-twitter-accounts)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## About

This package is part of the [Laravel Notification Channels](http://laravel-notification-channels.com/) project. It provides additional Laravel Notification channels to the ones given by [Laravel](https://laravel.com/docs/master/notifications) itself.

The Twitter channel makes it possible to send out Laravel notifications as a `Twitter status update `(post on the timeline) or as a `direct message`.

## Installation

If you prefer a video, there is also [an introduction video](https://christoph-rumpel.com/2018/11/sending-laravel-notifications-via-twitter) available for you. If not, just read on.

You can install this package via composer:

``` bash
composer require laravel-notification-channels/twitter
```

The service provider gets loaded automatically.

### Setting up the Twitter service

You will need to [create](https://developer.twitter.com/apps/) a Twitter app to use this channel. Within this app, you will find the `keys and access tokens`. Place them inside your `.env` file. To load them, add this to your `config/services.php` file:

```php
...
'twitter' => [
    'consumer_key'    => env('TWITTER_CONSUMER_KEY'),
    'consumer_secret' => env('TWITTER_CONSUMER_SECRET'),
    'access_token'    => env('TWITTER_ACCESS_TOKEN'),
    'access_secret'   => env('TWITTER_ACCESS_SECRET')
]
...
```

This will load the Twitter app data from the `.env` file. Make sure to use the same keys you have used there like `TWITTER_CONSUMER_KEY`.

## Usage

To use this package, you need to create a notification class, like `NewsWasPublished` from the example below, in your Laravel application. Make sure to check out [Laravel's documentation](https://laravel.com/docs/master/notifications) for this process.

### Publish a Twitter status update

```php
<?php

use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class NewsWasPublished extends Notification
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

    public function toTwitter(mixed $notifiable): TwitterMessage
    {
        return new TwitterStatusUpdate('Laravel notifications are awesome!');
    }
}
```

Take a closer look at the `toTwitter` method. Here we define what kind of Twitter message we want to trigger. In this case, it is a status update message, which is just a new message in your timeline.

````php
public function toTwitter(mixed $notifiable): TwitterMessage
{
    return new TwitterStatusUpdate('Laravel notifications are awesome!');
}
````
### Publish Twitter status update with images
It is possible to publish images with your status update too. You have to pass the image path to the `withImage` method.
````php
public function toTwitter(mixed $notifiable): TwitterMessage
{
    return (new TwitterStatusUpdate('Laravel notifications are awesome!'))->withImage('marcel.png');
}
````
If you want to use multiple images, just pass an array of paths.
````php
return (new TwitterStatusUpdate('Laravel notifications are awesome!'))->withImage([
    public_path('marcel.png'),
    public_path('mohamed.png')
]);
````
### Publish Twitter status update with videos
It is possible to publish videos with your status update too. You have to pass the video path to the `withVideo` method.
````php
public function toTwitter(mixed $notifiable): TwitterMessage
{
    return (new TwitterStatusUpdate('Laravel notifications are awesome!'))->withVideo('video.mp4');
}
````
If you want to use multiple videos, just pass an array of paths.
````php
return (new TwitterStatusUpdate('Laravel notifications are awesome!'))->withVideo([
    public_path('video1.mp4'),
    public_path('video.gif')
]);
````
### Publish Twitter status update with both images and videos
It is also possible to publish both images and videos with your status by using a mixture of the two methods.
````php
return (new TwitterStatusUpdate('Laravel notifications are awesome!'))->withVideo([
    public_path('video1.mp4'),
    public_path('video.gif')
])->withImage([
    public_path('marcel.png'),
    public_path('mohamed.png')
]);
````
### Publish Twitter status update in reply to another tweet
Additionally, you can publish a status update in reply to another tweet. That is possible using the method `inReplyTo`.
````php
public function toTwitter(mixed $notifiable): TwitterMessage
{
    return (new TwitterStatusUpdate('@christophrumpel Laravel notifications are awesome!'))->inReplyTo(123);
}
````
> Note that the reply status id will be ignored if you omit the author of the original tweet, according to Twitter docs.
### Send a direct message
To send a Twitter direct message to a specific user, you will need the `TwitterDirectMessage` class. Provide the Twitter user handler as the first parameter and the message as the second one.
````php
public function toTwitter(mixed $notifiable): TwitterMessage
{
     return new TwitterDirectMessage('marcelpociot', 'Hey Marcel, it was nice meeting you at the Laracon.');
}
````

You can also provide the `user ID` instead of the `screen name`. This would prevent an extra Twitter API call. Make sure to pass it as an integer when you do.

````php
public function toTwitter(mixed $notifiable): TwitterMessage
{
     return new TwitterDirectMessage(12345, 'Hey Marcel, it was nice meeting you at the Laracon.');
}
````

## Handle multiple Twitter Accounts

There might be cases where you need to handle multiple Twitter accounts. This means you need to be able to change the provided keys and tokens of your Twitter app. Luckily, [Laravel](https://laravel.com/docs/master/notifications#customizing-the-recipient) can help you here. In your notifiable model, you can define the `routeNotifiactionForTwitter` method. Here you can override the provided settings.

````php
public function routeNotificationForTwitter($notification)
{
   return [
      'TWITTER_CONSUMER_KEY',
      'TWITTER_CONSUMER_SECRET',
      'TWITTER_ACCESS_TOKEN',
      'TWITTER_ACCESS_SECRET',
   ];
}
````

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security-related issues, please email c.rumpel@kabsi.at instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Christoph Rumpel](https://github.com/christophrumpel)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
