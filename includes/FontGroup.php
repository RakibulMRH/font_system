<?php
class FontGroup {
    private $conn;
    private $table_name = "font_groups";
    private $pivot_table = "font_group_fonts";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createGroup($groupName, $fontIds, $customFontNames) {
        try {
            $this->conn->beginTransaction();
    
            // Insert into font_groups
            $query = "INSERT INTO " . $this->table_name . " (name) VALUES (:name)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $groupName);
            $stmt->execute();
            $group_id = $this->conn->lastInsertId();
    
            // Insert into font_group_fonts
            $query = "INSERT INTO " . $this->pivot_table . " (group_id, font_id, custom_font_name) VALUES (:group_id, :font_id, :custom_font_name)";
            $stmt = $this->conn->prepare($query);
            foreach($fontIds as $index => $font_id) {
                $stmt->bindParam(':group_id', $group_id);
                $stmt->bindParam(':font_id', $font_id);
                $stmt->bindParam(':custom_font_name', $customFontNames[$index]);
                $stmt->execute();
            }
    
            $this->conn->commit();
            return array('status' => true, 'message' => 'Font group created successfully.');
        } catch(Exception $e) {
            $this->conn->rollBack();
            return array('status' => false, 'message' => 'Failed to create font group: ' . $e->getMessage());
        }
    }
    
    
    public function updateGroup($group_id, $groupName, $fontIds, $customFontNames) {
        try {
            $this->conn->beginTransaction();
    
            // Update font_groups
            $query = "UPDATE " . $this->table_name . " SET name = :name WHERE id = :group_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $groupName);
            $stmt->bindParam(':group_id', $group_id);
            $stmt->execute();
    
            // Delete existing fonts in the group
            $query = "DELETE FROM " . $this->pivot_table . " WHERE group_id = :group_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':group_id', $group_id);
            $stmt->execute();
    
            // Insert new fonts into font_group_fonts
            $query = "INSERT INTO " . $this->pivot_table . " (group_id, font_id, custom_font_name) VALUES (:group_id, :font_id, :custom_font_name)";
            $stmt = $this->conn->prepare($query);
            foreach($fontIds as $index => $font_id) {
                $stmt->bindParam(':group_id', $group_id);
                $stmt->bindParam(':font_id', $font_id);
                $stmt->bindParam(':custom_font_name', $customFontNames[$index]);
                $stmt->execute();
            }
    
            $this->conn->commit();
            return array('status' => true, 'message' => 'Font group updated successfully.');
        } catch(Exception $e) {
            $this->conn->rollBack();
            return array('status' => false, 'message' => 'Failed to update font group: ' . $e->getMessage());
        }
    }
    
    

    public function getGroups() {
        $query = "SELECT fg.id, fg.name, GROUP_CONCAT(fgf.custom_font_name SEPARATOR ', ') as fonts, COUNT(f.id) as count
                  FROM " . $this->table_name . " fg
                  JOIN " . $this->pivot_table . " fgf ON fg.id = fgf.group_id
                  JOIN fonts f ON fgf.font_id = f.id
                  GROUP BY fg.id
                  ORDER BY fg.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $groups;
    }
    
    

    public function deleteGroup($group_id) {
        try {
            // Delete from font_groups; cascading will handle font_group_fonts
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :group_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
            $stmt->execute();
    
            return array('status' => true, 'message' => 'Font group deleted successfully.');
        } catch (Exception $e) {
            return array('status' => false, 'message' => 'Failed to delete font group: ' . $e->getMessage());
        }
    }
    
    

    public function getGroupById($group_id) {
        $query = "SELECT fg.name, fgf.font_id, fgf.custom_font_name
                  FROM " . $this->table_name . " fg
                  JOIN " . $this->pivot_table . " fgf ON fg.id = fgf.group_id
                  WHERE fg.id = :group_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':group_id', $group_id);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if(count($results) > 0) {
            $groupName = $results[0]['name'];
            $fontIds = array_column($results, 'font_id');
            $customFontNames = array_column($results, 'custom_font_name');
            return array('id' => $group_id, 'name' => $groupName, 'font_ids' => $fontIds, 'custom_font_names' => $customFontNames);
        } else {
            return null;
        }
    }
    
    
}
?>
