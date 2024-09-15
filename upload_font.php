<?php
include 'includes/Database.php';
include 'includes/Font.php';

$db = Database::getInstance()->getConnection();
$font = new Font($db);

if(isset($_FILES['file'])) {
    $response = $font->uploadFont($_FILES['file']);
    echo json_encode($response);
} else {
    echo json_encode(array('status' => false, 'message' => 'No file uploaded.'));
}
?>
