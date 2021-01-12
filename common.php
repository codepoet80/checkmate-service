<?php

function get_filename_from_move($move) {
    $move = strtolower($move);
    $move = str_replace("'", " ", $move);
    $move = str_replace("to", "-", $move);
    $move = str_replace(" ", "", $move);
    $file = $move . ".json";
    return "notations/" . $file;
}

function get_authorization() {

    //Make sure we got a valid query
    if (!isset($_GET["move"]))
        die ("Move not specified");
    $move = $_GET["move"];

    if (!isset($_GET["grandmaster"])){
        $request_headers = getallheaders();
        if (array_key_exists('grandmaster', $request_headers)) {
            $grandmaster = $request_headers['grandmaster'];
        } else {
            die ("Grandmaster not specified");
        }
    }
    else {
        $grandmaster = $_GET["grandmaster"];
    }
    $grandmaster = strtolower($grandmaster);

    return array(
        'move' => $move,
        'grandmaster' => $grandmaster
    );
}

function get_notation_data($file, $grandmaster) {
    if (!file_exists($file))
        die ("Specified move was malformed or could not be opened");

    try {
        $notations = file_get_contents($file);
        $jsondata = json_decode($notations, true);
    }
    catch (exception $e) {
        die ("Move file content could not be loaded");
    }

    //Make sure the file belongs to the requesting user
    $owner = strtolower($jsondata['grandmaster']);
    if ($grandmaster != $owner)
        die ("Illegal move");
    
    return $jsondata;
}

?>