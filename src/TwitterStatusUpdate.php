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
    private ?int $inReplyToStatusId = null;

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
        return 'statuses/update';
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
    public function inReplyTo(int $statusId): self
    {
        $this->inReplyToStatusId = $statusId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getInReplyToStatusId(): ?int
    {
        return $this->inReplyToStatusId;
    }

    /**
     * Build Twitter request body.
     */
    public function getRequestBody(): array
    {
        $body = ['status' => $this->getContent()];

        $mediaIds = collect()
            ->merge($this->imageIds instanceof Collection ? $this->imageIds : [])
            ->merge($this->videoIds instanceof Collection ? $this->videoIds : [])
            ->filter()
            ->values();

        if($mediaIds->count() > 0) {
            $body['media_ids'] = $mediaIds->implode(',');
        }

        if ($this->inReplyToStatusId) {
            $body['in_reply_to_status_id'] = $this->inReplyToStatusId;
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
