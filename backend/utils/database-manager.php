<?php
class DatabaseManager
{
    private $pdo;

    public function __construct($pdo = null)
    {
        if ($pdo === null) {
            global $pdo;
        }
        $this->pdo = $pdo;

        // Initialize tables if needed
        $this->initializeTables();
    }

    private function initializeTables()
    {
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS files (
                id INT PRIMARY KEY AUTO_INCREMENT,
                file_name VARCHAR(255) NOT NULL,
                file_size INT NOT NULL,
                mime_type VARCHAR(100) NOT NULL,
                upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                user_id INT,
                file_type ENUM('profile', 'room') NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(userID) ON DELETE CASCADE
            )");

            $this->pdo->exec("CREATE TABLE IF NOT EXISTS file_contents (
                id INT PRIMARY KEY AUTO_INCREMENT,
                file_id INT NOT NULL,
                file_content LONGBLOB NOT NULL,
                FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE
            )");
        } catch (PDOException $e) {
            error_log("Database Initialization Error: " . $e->getMessage());
        }
    }

    public function uploadFile($file, $userId = null, $fileType = 'profile')
    {
        try {
            // Validate file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Upload error: " . $file['error']);
            }

            $content = file_get_contents($file['tmp_name']);
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);

            // Allowed image types
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
            }

            // Max file size (5MB)
            $maxFileSize = 5 * 1024 * 1024;
            if ($file['size'] > $maxFileSize) {
                throw new Exception("File is too large. Maximum size is 5MB.");
            }

            $this->pdo->beginTransaction();

            // First insert metadata
            $stmtMeta = $this->pdo->prepare("
                INSERT INTO files (file_name, file_size, mime_type, user_id, file_type)
                VALUES (:file_name, :file_size, :mime_type, :user_id, :file_type)
            ");

            $stmtMeta->execute([
                ':file_name' => $file['name'],
                ':file_size' => $file['size'],
                ':mime_type' => $mimeType,
                ':user_id' => $userId,
                ':file_type' => $fileType
            ]);

            $fileId = $this->pdo->lastInsertId();

            // Then insert the blob content
            $stmtContent = $this->pdo->prepare("
                INSERT INTO file_contents (file_id, file_content)
                VALUES (:file_id, :content)
            ");

            $stmtContent->execute([
                ':file_id' => $fileId,
                ':content' => $content
            ]);

            $this->pdo->commit();
            return $fileId;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("File Upload Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function getFileByUserId($userId, $fileType = 'profile')
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT fc.file_content, f.mime_type
                FROM files f
                JOIN file_contents fc ON f.id = fc.file_id
                WHERE f.user_id = ? AND f.file_type = ?
                ORDER BY f.upload_date DESC
                LIMIT 1
            ");
            $stmt->execute([$userId, $fileType]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get File Error: " . $e->getMessage());
            return null;
        }
    }

    public function deleteOldFiles($userId, $fileType = 'profile')
    {
        try {
            $this->pdo->beginTransaction();

            // Delete old files for this user and type, keeping the most recent
            $stmt = $this->pdo->prepare("
                DELETE f, fc FROM files f
                LEFT JOIN file_contents fc ON f.id = fc.file_id
                WHERE f.user_id = ? AND f.file_type = ?
                AND f.id NOT IN (
                    SELECT id FROM (
                        SELECT MAX(id) as id 
                        FROM files 
                        WHERE user_id = ? AND file_type = ?
                    ) AS latest
                )
            ");
            $stmt->execute([$userId, $fileType, $userId, $fileType]);

            $this->pdo->commit();
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Delete Old Files Error: " . $e->getMessage());
        }
    }
}
