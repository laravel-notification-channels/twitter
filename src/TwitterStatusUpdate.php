<?php

namespace NotificationChannels\Twitter;

use Illuminate\Support\Collection;
use Kylewm\Brevity\Brevity;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;

class TwitterStatusUpdate extends TwitterMessage
{
    public ?Collection $imageIds = null;
    public ?Collection $videoIds = null;
    private ?array $images = null;
    private ?array $videos = null;
    private ?int $inReplyToTweetId = null;

    /**
     * @throws CouldNotSendNotification
     */
    public function __construct(string $content)
    {
        parent::__construct($content);

        if ($exceededLength = $this->messageIsTooLong(new Brevity())) {
            throw CouldNotSendNotification::statusUpdateTooLong($exceededLength);
        }
    }

    public function getApiEndpoint(): string
    {
        return 'tweets';
    }

    /**
     * Set Twitter media files.
     *
     * @return $this
     */
    public function withImage(array|string $images): static
    {
        $images = is_array($images) ? $images : [$images];

        collect($images)->each(function ($image) {
            $this->images[] = new TwitterImage($image);
        });

        return $this;
    }

    /**
     * Set Twitter media files.
     *
     * @return $this
     */
    public function withVideo(array|string $videos): static
    {
        $videos = is_array($videos) ? $videos : [$videos];

        collect($videos)->each(function ($video) {
            $this->videos[] = new TwitterVideo($video);
        });

        return $this;
    }

    /**
     * Get Twitter images list.
     */
    public function getImages(): ?array
    {
        return $this->images;
    }

    /**
     * Get Twitter videos list.
     */
    public function getVideos(): ?array
    {
        return $this->videos;
    }

    /**
     * @param  int  $statusId
     * @return $this
     */
    public function inReplyTo(int $tweetId): self
    {
        $this->inReplyToTweetId = $tweetId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getInReplyToTweetId(): ?int
    {
        return $this->inReplyToTweetId;
    }

    /**
     * Build Twitter request body.
     */
    public function getRequestBody(): array
    {
        $body = ['text' => $this->getContent()];

        $mediaIds = collect()
            ->merge($this->imageIds instanceof Collection ? $this->imageIds : [])
            ->merge($this->videoIds instanceof Collection ? $this->videoIds : [])
            ->values();

        if ($mediaIds->count() > 0) {
            $body['media'] = [
                'media_ids' => $mediaIds->toArray()
            ];
        }

        if ($this->inReplyToTweetId) {
            $body['in_reply_to_tweet_id'] = $this->inReplyToTweetId;
        }

        return $body;
    }

    /**
     * Check if the message length is too long.
     *
     * @return int How many characters the max length is exceeded or 0 when it isn't.
     */
    private function messageIsTooLong(Brevity $brevity): int
    {
        $tweetLength = $brevity->tweetLength($this->content);
        $exceededLength = $tweetLength - 280;

        return max($exceededLength, 0);
    }
}
