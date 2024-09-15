<?php
class Font {
    private $conn;
    private $table_name = "fonts";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function uploadFont($file) {
        $allowed_ext = array('ttf');
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if(!in_array($ext, $allowed_ext)) {
            return array('status' => false, 'message' => 'Only TTF files are allowed.');
        }

        $new_file_name = uniqid() . '.' . $ext;
        $destination = 'fonts/' . $new_file_name;

        if(move_uploaded_file($file_tmp, $destination)) {
            $query = "INSERT INTO " . $this->table_name . " (name, file_name) VALUES (:name, :file_name)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $file_name);
            $stmt->bindParam(':file_name', $new_file_name);
            if($stmt->execute()) {
                return array('status' => true, 'message' => 'Font uploaded successfully.');
            } else {
                return array('status' => false, 'message' => 'Database insertion failed.');
            }
        } else {
            return array('status' => false, 'message' => 'Failed to move uploaded file.');
        }
    }

    public function getFonts() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY uploaded_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $fonts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $fonts;
    }

    public function deleteFont($font_id) {
        try {
            // Check if the font is used in any font groups
            $query = "SELECT COUNT(*) as count FROM font_group_fonts WHERE font_id = :font_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':font_id', $font_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if($result['count'] > 0) {
                return array('status' => false, 'message' => 'Cannot delete font. It is used in one or more font groups.');
            }
    
            // Get file name to delete the file from the server
            $query = "SELECT file_name FROM " . $this->table_name . " WHERE id = :font_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':font_id', $font_id);
            $stmt->execute();
            $font = $stmt->fetch(PDO::FETCH_ASSOC);
            if($font) {
                $filePath = 'fonts/' . $font['file_name'];
                if(file_exists($filePath)) {
                    unlink($filePath);
                }
            }
    
            // Delete from fonts table
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :font_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':font_id', $font_id);
            $stmt->execute();
    
            return array('status' => true, 'message' => 'Font deleted successfully.');
        } catch(Exception $e) {
            return array('status' => false, 'message' => 'Failed to delete font: ' . $e->getMessage());
        }
    }
      
}
?>
