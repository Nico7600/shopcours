<?php
require_once('connect.php');

$checkColumnSql = "SHOW COLUMNS FROM `liste` LIKE 'image';";
$checkColumnQuery = $db->prepare($checkColumnSql);
$checkColumnQuery->execute();
$columnExists = $checkColumnQuery->fetch();

if (!$columnExists) {
    $sql = 'ALTER TABLE `liste` ADD `image` VARCHAR(255) NULL;';
    $query = $db->prepare($sql);

    if ($query->execute()) {
        echo "Column 'image' added successfully.";
    } else {
        echo "Failed to add column 'image'.";
    }
} else {
    echo "Column 'image' already exists.";
}

require_once('close.php');
?>