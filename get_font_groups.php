<?php
include 'includes/Database.php';
include 'includes/FontGroup.php';

$db = Database::getInstance()->getConnection();
$fontGroup = new FontGroup($db);

$groups = $fontGroup->getGroups();
echo json_encode($groups);
?>
