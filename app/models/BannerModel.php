<?php
class BannerModel
{
    private $conn;

    public function __construct($db) { $this->conn = $db; }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM banner ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActive()
    {
        $stmt = $this->conn->query("SELECT * FROM banner WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM banner WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create($title, $subtitle, $image, $buttonText, $buttonLink, $sortOrder, $isActive)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO banner (title, subtitle, image, button_text, button_link, sort_order, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $subtitle, $image, $buttonText, $buttonLink, (int)$sortOrder, (int)$isActive]);
        return $this->conn->lastInsertId();
    }

    public function update($id, $title, $subtitle, $image, $buttonText, $buttonLink, $sortOrder, $isActive)
    {
        $stmt = $this->conn->prepare(
            "UPDATE banner SET title=?, subtitle=?, image=?, button_text=?, button_link=?, sort_order=?, is_active=? WHERE id=?"
        );
        return $stmt->execute([$title, $subtitle, $image, $buttonText, $buttonLink, (int)$sortOrder, (int)$isActive, $id]);
    }

    public function delete($id)
    {
        $row = $this->getById($id);
        if ($row && $row['image']) {
            $path = __DIR__ . '/../../public/images/banners/' . $row['image'];
            if (file_exists($path)) unlink($path);
        }
        $stmt = $this->conn->prepare("DELETE FROM banner WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
