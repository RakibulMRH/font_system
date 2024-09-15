<?php
include 'includes/Database.php';
include 'includes/Font.php';

$db = Database::getInstance()->getConnection();
$font = new Font($db);

if(isset($_POST['id'])) {
    $fontId = $_POST['id'];
    $response = $font->deleteFont($fontId);
    echo json_encode($response);
} else {
    echo json_encode(array('status' => false, 'message' => 'No font ID provided.'));
}
?>
