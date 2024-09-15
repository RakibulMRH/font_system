<?php
include 'includes/Database.php';
include 'includes/Font.php';

$db = Database::getInstance()->getConnection();
$font = new Font($db);

$fonts = $font->getFonts();
echo json_encode($fonts);
?>
