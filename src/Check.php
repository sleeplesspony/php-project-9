<?php

namespace Hexlet\Code;
use Carbon\Carbon;
use Hexlet\Code\CheckRepository;

class Check
{
    private ?int $id = null;
    private int $urlId;
    private ?Carbon $createdAt = null;

    public function __construct(string $urlId)
    {
        $this->urlId = $urlId;
    }

    public function getUrlId(): ?string
    {
        return $this->urlId;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

}