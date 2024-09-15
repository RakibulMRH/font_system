<?php
include 'includes/Database.php';
include 'includes/FontGroup.php';

$db = Database::getInstance()->getConnection();
$fontGroup = new FontGroup($db);

if (isset($_POST['id'])) {
    $group_id = intval($_POST['id']);
    $response = $fontGroup->deleteGroup($group_id);
    echo json_encode($response);
} else {
    echo json_encode(array('status' => false, 'message' => 'No group ID provided.'));
}
?>
