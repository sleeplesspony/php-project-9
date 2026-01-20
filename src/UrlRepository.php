<?php

namespace Hexlet\Code;
use Carbon\Carbon;
class UrlRepository
{

    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function save(Url $url): void
    {
        $sql = "INSERT INTO urls (name) VALUES (:name)";
        $stmt = $this->conn->prepare($sql);
        $name = $url->getName();
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $id = (int) $this->conn->lastInsertId();
        $url->setId($id);
    }

    public function getUrlById(int $id): ?Url
    {
        $sql = "SELECT * FROM urls WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        if ($row = $stmt->fetch()) {
            $url = new Url($row['name']);
            $url->setCreatedAt(Carbon::parse($row['created_at']));
            $url->setId($row['id']);
            return $url;
        }

        return null;
    }

    public function getUrlByName(string $name): ?Url
    {
        $sql = "SELECT * FROM urls WHERE name = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$name]);
        if ($row = $stmt->fetch()) {
            $url = new Url($row['name']);
            $url->setCreatedAt(Carbon::parse($row['created_at']));
            $url->setId($row['id']);
            return $url;
        }

        return null;
    }

    public function getUrlsData(): array
    {
        $urls = [];
        $sql = "SELECT * FROM urls ORDER BY id DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }




}