<?php

namespace Hexlet\Code;
use Carbon\Carbon;
use Hexlet\Code\CheckRepository;

class Check
{
    private ?int $id = null;
    private int $urlId;
    private ?int $code = null;
    private ?Carbon $createdAt = null;
    private ?string $h1 = null;
    private ?string $title = null;
    private ?string $description = null;

    public function __construct(string $urlId, $checkData = [])
    {
        $this->urlId = $urlId;

        if (isset($checkData["code"]))
            $this->code = $checkData["code"];

        if (isset($checkData["h1"])) {
            $this->h1 = $checkData["h1"];
        }

        if (isset($checkData["title"])) {
            $this->title = $checkData["title"];
        }

        if (isset($checkData["description"])) {
            $this->description = $checkData["description"];
        }
    }

    public function getUrlId(): ?string
    {
        return $this->urlId;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setH1(?string $h1): void
    {
        $this->h1 = $h1;
    }

    public function getH1(): ?string
    {
        return $this->h1;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->h1;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

}