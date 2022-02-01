<?php

namespace NotificationChannels\Twitter;

use Illuminate\Support\Collection;
use Kylewm\Brevity\Brevity;
use NotificationChannels\Twitter\Exceptions\CouldNotSendNotification;

class TwitterStatusUpdate extends TwitterMessage
{
    public ?Collection $imageIds = null;
    private ?array $images = null;
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
     * Get Twitter images list.
     */
    public function getImages(): ?array
    {
        return $this->images;
    }

    /**
     * @param int $statusId
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

        if ($this->imageIds instanceof Collection) {
            $body['media_ids'] = $this->imageIds->implode(',');
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
