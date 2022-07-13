<?php

//Support nginx, which doesn't have this function
if (!function_exists('getallheaders'))  {
    function getallheaders()
    {
        if (!is_array($_SERVER)) {
            return array();
        }

        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))))] = $value;
            }
        }
        return $headers;
    }
}


function get_visitor_ip() {
    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function get_filename_from_move($move) {
    $file = $move . ".json";
    return "notations/" . $file;
}

function get_function_endpoint($functionName) {
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
        $url = "https://";
    else
        $url = "http://";
    $url.= $_SERVER['HTTP_HOST'];   
    $url.= $_SERVER['REQUEST_URI'];  
    $url = strtok($url, "?");
    $page = basename($_SERVER['PHP_SELF']);
    $url = str_replace($page, $functionName . ".php", $url);
    return $url;
}

function get_authorization() {

    //Make sure we got a valid query
    if (!isset($_GET["move"]))
        die ("Move not specified");
    $move = $_GET["move"];
    $move = try_make_move_from_input($move);

    if (!isset($_GET["grandmaster"])){
        $request_headers = getallheaders();
        if (array_key_exists('Grandmaster', $request_headers)) {
            $grandmaster = base64_decode($request_headers['Grandmaster']);
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
        die ("{\"error\":\"Specified move was not found or could not be opened.\"}");

    try {
        $notations = file_get_contents($file);
        $jsondata = json_decode($notations, true);
    }
    catch (exception $e) {
        die ("{\"error\":\"Notation content could not be loaded.\"}");
    }

    //Make sure the file belongs to the requesting user
    $owner = strtolower($jsondata['grandmaster']);
    if ($grandmaster != $owner)
        die ("{\"error\":\"Illegal move. You are not the Grand Master.\"}");
    
    return $jsondata;
}

function convert_move_to_public_schema($data) {
    class notationdata {};
    $move = new notationdata();
    $move->notation = $data['notation'];
    $move->tasks = $data['moves'];
    return $move;
}

function try_make_move_from_input($input, $new = false) {
    $input = strtolower($input);
    $input = str_replace(" to ", " ", $input);
    $input = str_replace(" to", " ", $input);
    $input = str_replace("to ", " ", $input);
    $input = str_replace("to", " ", $input);
    $input = str_replace(" - ", " ", $input);
    $input = str_replace(" -", " ", $input);
    $input = str_replace("- ", " ", $input);
    $input = str_replace("-", " ", $input);
    $input = str_replace("'", "", $input);
    $inputParts = explode(" ", $input);
    //TODO: also replace shorthands
    if (array_count_values($inputParts) > 1) {
        $move = $inputParts[0] . "-";
        $count = 0;
        foreach ($inputParts as $part)
        {
            if ($count > 0)
                $move .= $part;
            $count++;
        }
    }
    if ($new|| file_exists(get_filename_from_move($move)))
        return $move;
    else
        return false;
}

function base64url_encode($data)
{
  // First of all you should encode $data to Base64 string
  $b64 = base64_encode($data);

  // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
  if ($b64 === false) {
    return false;
  }

  // Convert Base64 to Base64URL by replacing “+” with “-” and “/” with “_”
  $url = strtr($b64, '+/', '-_');

  // Remove padding character from the end of line and return the Base64URL result
  return rtrim($url, '=');
}

function base64url_decode($data, $strict = false)
{
  // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
  $b64 = strtr($data, '-_', '+/');

  // Decode Base64 string and return the original data
  return base64_decode($b64, $strict);
}

?>
