<?php
include("common.php");

$auth = get_authorization();

//Make sure the file exists and can be loaded
$file = get_filename_from_move($auth['move']);
$jsondata = get_notation_data($file, $auth['grandmaster']);

//Load and return only the task list
$movedata = convert_move_to_public_schema($jsondata);
header('Content-Type: application/json');
print_r (json_encode($movedata));
exit();
?>