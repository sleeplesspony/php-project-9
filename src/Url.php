<?php

namespace Hexlet\Code;
use Carbon\Carbon;
use Hexlet\Code\UrlRepository;

class Url
{
    private ?int $id = null;
    private string $name;
    private ?Carbon $createdAt = null;

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setCreatedAt(Carbon $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}