<?php
include("common.php");

$url = get_function_endpoint("read-notation");

//add user's notation
$notationfile = "pawn-queensbishop4";
$url.="?move=" . $notationfile;
//add user's password
$grandmaster = "Alexander Motylev";

//echo $url . "<br>";

//make outbound request to metube
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
}

$data = json_decode($response);
//print_r ($data);

?>
<html>
<head>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=1" />

<title>Check Mate - Your To Do List Anywhere</title>
</head>
<body>
<h2><div><span>Check Mate - <?php echo $data->notation ?></span></div></h2>
<form action="?submit=true" method="post">
<table cellpadding="2" cellspacing="2" border="0" width="80%">
<tr><td colspan="3"><hr></td></tr>
<?php
    $tasks = (array)$data->tasks;
    foreach ($tasks as $task)
    {
        echo "<tr><td><input type='checkbox' id='" . $task->guid . "' name='" . $task->guid . "'><br/>&nbsp;</td>\r\n";
        echo "<td width='100%'><b>" . $task->title . "</b><br/>" . $task->notes . "<br/>&nbsp;</td>\r\n";
        echo "<td><a href='?edit=" . $task->guid . "'>Edit</a>";
        echo "<br><a href='?delete=" . $task->guid . "'> Delete</td></tr>\r\n";
    }
?>
<tr><td colspan="3"><hr></td></tr>
<tr><td>New</td>
<td><input type="text">&nbsp;<br>
<textarea></textarea>
</td></tr>

<tr><td colspan="3" align="right"><input type="submit" value="Save Changes"></td></tr>
</table>
</form>
</body>
</html>