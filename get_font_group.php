<?php
include 'includes/Database.php';
include 'includes/FontGroup.php';

$db = Database::getInstance()->getConnection();
$fontGroup = new FontGroup($db);

if(isset($_GET['id'])) {
    $groupId = $_GET['id'];
    $group = $fontGroup->getGroupById($groupId);
    echo json_encode($group);
} else {
    echo json_encode(null);
}
?>
