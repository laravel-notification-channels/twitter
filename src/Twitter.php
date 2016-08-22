<?php

namespace NotificationChannels\Twitter;


use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter
{
    /** @var \Abraham\TwitterOAuth\TwitterOAuth */
    public $connection;

    public function __construct(array $config)
    {
        $this->connection = new TwitterOAuth(
            $config['consumer_key'],
            $config['consumer_secret'],
            $config['access_token'],
            $config['access_secret']
        );
    }
}