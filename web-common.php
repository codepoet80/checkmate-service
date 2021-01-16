<?php

function update_task_data($url, $notationFile, $grandmaster, $jsonData)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_PORT => 80,
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => array(
            "grandmaster: " . $grandmaster,
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
    }
    return $response;
}

function load_task_data($url, $notationFile, $grandmaster) {
    //make outbound request to service
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_PORT => 80,
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 0,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "grandmaster: " . $grandmaster
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "Request error:" . $err;
        die;
    } else {
        return $response;
    }
}

function try_make_move_from_input($input)
{
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
    if (file_exists(get_filename_from_move($move)))
        return $move;
    else
        return false;
}

?>