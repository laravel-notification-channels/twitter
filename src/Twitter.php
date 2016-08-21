<?php

namespace NotificationChannels\Twitter;


use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter
{

    /** @var mixed @var string */
    protected $consumer_key;

    /** @var mixed @var string */
    protected $consumer_secret;

    /** @var mixed @var string */
    protected $access_token;

    /** @var mixed @var string */
    protected $access_secret;

    /** @var TwitterOAuth */
    public $connection;

    public function __construct(array $config)
    {
        $this->consumer_key = $config['consumer_key'];
        $this->consumer_secret = $config['consumer_secret'];
        $this->access_token = $config['access_token'];
        $this->access_secret = $config['access_secret'];
        $this->connection = new TwitterOAuth(
            $this->consumer_key,
            $this->consumer_secret,
            $this->access_token,
            $this->access_secret
        );
    }
}