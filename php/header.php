<?php 
if (!isset($_SESSION)) { session_start(); }
$response = array(
    'trusted' => false,
    'server' => "",
    'values'  => array()
);



$response['server'] = $_SERVER["EVE.TRUSTED"];
foreach (headers as $key => $value) {
    if (strpos('HTTP_EVE', $key) === 0) {

        $key = str_replace('HTTP_EVE_', '', $key);
        $key = strtolower($key);

        if ($key === 'trusted') {
            $response['trusted'] = true;
        }

        $response['values'][ $key ] = $value;
    }
}

echo json_encode($response);
?>