<?php

namespace BrefLaravelBroadcast\Events;

class BroadcastReceive
{
    /**
     * @var string
     */
    private string $type;

    /**
     * @var array
     */
    private array $body;

    /**
     * @var string
     */
    private string $connectionId;

    /**
     * @var string
     */
    private string $apiId;

    /**
     * @var string
     */
    private string $region;

    /**
     * @var string
     */
    private string $stage;

    /**
     * @var int
     */
    private int $responseCode = 200;

    /**
     * @var string
     */
    private string $responseText = 'ok';

    /**
     * BroadcastReceive constructor.
     *
     * @param string $type
     * @param string $connectionId
     * @param string $apiId
     * @param string $region
     * @param string $stage
     * @param array|null $body
     */
    public function __construct(
        string $type,
        string $connectionId,
        string $apiId,
        string $region,
        string $stage,
        ?array $body = null
    ) {
        $this->type = $type;
        $this->connectionId = $connectionId;
        $this->apiId = $apiId;
        $this->region = $region;
        $this->stage = $stage;

        if ($body !== null) {
            $this->body = $body;
        }
    }

    /**
     * @return array|null
     */
    public function getBody(): ?array
    {
        return $this->body ?? null;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getConnectionId(): string
    {
        return $this->connectionId;
    }

    /**
     * @return string
     */
    public function getApiId(): string
    {
        return $this->apiId;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return $this->stage;
    }

    /**
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    /**
     * @return string
     */
    public function getResponseText(): string
    {
        return $this->responseText;
    }

    /**
     * @param int $responseCode
     *
     * @return self
     */
    public function setResponseCode(int $responseCode): self
    {
        $this->responseCode = $responseCode;
        return $this;
    }

    /**
     * @param string $responseText
     *
     * @return self
     */
    public function setResponseText(string $responseText): self
    {
        $this->responseText = $responseText;
        return $this;
    }
}
