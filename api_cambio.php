<?php

header('Content-Type: application/json; charset=utf-8');

$api_url = 'https://api.frankfurter.dev/v2/rates?base=USD&quotes=DOP,EUR,GBP,JPY';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);

if ($response === false) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Error al conectar con Frankfurter: ' . curl_error($ch)
    ]);

    curl_close($ch);
    exit;
}

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($http_code !== 200) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Frankfurter respondió con código HTTP ' . $http_code
    ]);

    exit;
}

$data = json_decode($response, true);

if (!is_array($data)) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'La respuesta de Frankfurter no es un JSON válido.'
    ]);

    exit;
}


/*
 * Frankfurter devuelve un arreglo:
 *
 * [
 *   {
 *     "base": "USD",
 *     "quote": "DOP",
 *     "rate": 62
 *   },
 *   ...
 * ]
 *
 * Lo convertimos a:
 *
 * {
 *   "DOP": 62,
 *   "EUR": 0.85,
 *   "GBP": 0.74,
 *   "JPY": 157
 * }
 */

$rates = [];

foreach ($data as $item) {

    if (
        isset($item['quote']) &&
        isset($item['rate'])
    ) {

        $rates[$item['quote']] = $item['rate'];

    }

}


/* RESPUESTA PARA NUESTRO SISTEMA */

echo json_encode([

    'success' => true,

    'base' => 'USD',

    'rates' => $rates

], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>