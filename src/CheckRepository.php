<?php

namespace Hexlet\Code;
use Carbon\Carbon;
class CheckRepository
{

    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function save(Check $check): void
    {
        $sql = "INSERT INTO url_checks (url_id, status_code, h1, title, description) VALUES (:url_id, :code, :h1, :title, :description)";
        $stmt = $this->conn->prepare($sql);
        $urlId = $check->getUrlId();
        $code = $check->getCode();
        $h1 = $check->getH1();
        $title = $check->getTitle();
        $description = $check->getDescription();
        $stmt->bindParam(':url_id', $urlId);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':h1', $h1);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
        $id = (int) $this->conn->lastInsertId();
        $check->setId($id);
    }

    public function getChecksDataByUrlId($urlId): array
    {
        $checks = [];
        $sql = "SELECT * FROM url_checks WHERE url_id = ? ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$urlId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getLastChecks(): array
    {
        $checks = [];
        $sql = "SELECT DISTINCT ON(url_id)
            id,
            url_id,
            status_code,
            created_at
        FROM url_checks
        ORDER BY url_id, created_at DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}