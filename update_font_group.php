<?php
include 'includes/Database.php';
include 'includes/FontGroup.php';

$db = Database::getInstance()->getConnection();
$fontGroup = new FontGroup($db);

if(isset($_POST['id']) && isset($_POST['fonts']) && isset($_POST['group_name']) && isset($_POST['custom_font_names'])) {
    $groupId = $_POST['id'];
    $groupName = $_POST['group_name'];
    $fontIds = $_POST['fonts'];
    $customFontNames = $_POST['custom_font_names'];

    $response = $fontGroup->updateGroup($groupId, $groupName, $fontIds, $customFontNames);
    echo json_encode($response);
} else {
    echo json_encode(array('status' => false, 'message' => 'Invalid data.'));
}
?>
