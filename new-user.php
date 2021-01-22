<?php
include("common.php");
include("web-common.php");

function make_random_move() {
    $pieces = array("King", "Queen", "Rook", "Bishop", "Pawn");
    $places = array("Queen's Rook", "Queen's Knight", "Queen's Bishop", "Queen", "King", "King's Bishop", "King's Knight", "King's Rook");
    
    shuffle($pieces);
    $piece = $pieces[0];
    shuffle($places);   
    $place = $places[1];
    $position = rand(1, 8);
    $move = $piece . " to " . $place . " " . $position;
    return $move;
}

function get_random_grandmaster() {
    $grandmasters = file("grandmasters.txt", FILE_IGNORE_NEW_LINES);
    shuffle($grandmasters);
    $grandmaster = $grandmasters[2];
    return $grandmaster;
}

//Make a unique moves file and grandmaster for a new user, ensure its unique
do {
    $move = make_random_move();
    $grandmaster = get_random_grandmaster();
    $newfile = try_make_move_from_input($move, true);
    $newfile = get_filename_from_move($newfile);
} while (file_exists($newfile));

//Load the template, populate this user's values, and save as a new file
$templatefile = file_get_contents("template.json"); 
$templatedata = json_decode($templatefile);
$templatedata->notation = $move;
$templatedata->grandmaster = $grandmaster;
file_put_contents($newfile, json_encode($templatedata, JSON_PRETTY_PRINT));

//Run clean-up routine
//TODO: write clean-up routine

class user {};
$newuser = new user();
$newuser->move = $move;
$newuser->grandmaster = $grandmaster;
$newuser->newfile = $newfile;

header('Content-Type: application/json');
print_r (json_encode($newuser));
?>