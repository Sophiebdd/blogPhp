<?php

$articleDB = require_once './database/models/Article.php';

$_GET = filter_input_array(INPUT_GET, FILTER_VALIDATE_INT);
$id = $_GET['id'] ?? '';

if ($id) {
    $articleDB->deleteOne($id);
};


header('Location: /');
exit;