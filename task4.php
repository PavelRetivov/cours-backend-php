<?php

$STATUS_CODE = [
    '200' => '200 OK',
    '400' => '400 Bad Request',
    '404' => '404 Not Found',
    '500' => ' 500 Internal Server Error'
];
function readHttpLikeInput() {
    $f = fopen( 'php://stdin', 'r' );
    $store = "";
    $toread = 0;
    while( $line = fgets( $f ) ) {
        $store .= preg_replace("/\r/", "", $line);
        if (preg_match('/Content-Length: (\d+)/',$line,$m))
            $toread=$m[1]*1;
        if ($line == "\r\n")
            break;
    }
    if ($toread > 0)
        $store .= fread($f, $toread);
    return $store;
}

$contents = readHttpLikeInput();

function outputHttpResponse($statuscode, $statusmessage, $headers, $body) {

    echo "HTTP/1.1 $statuscode\n";
    echo "Server: Apache/2.2.14 (Win32)\n";
    echo "Content-Type: text/html; charset=utf-8\n";
    echo "Connection: Closed\n";
    echo "Content-Length: " . strlen($statusmessage) . "\n";
    echo "\n$statusmessage" ;
}

function processHttpRequest($method, $uri, $headers, $body) {
    global $STATUS_CODE;
    $statuscode = '200 OK';

    if($method != "POST"){
        $statuscode = '400 Bad Request';
        $statusmassage = 'not found';
        outputHttpResponse($statuscode, $statusmassage, $headers, $body);
        return;
    }
    if(!str_starts_with($uri, "/api/checkLoginAndPassword")){
        $statuscode = '404 Not Found';
        $statusmassage = 'not found';
        outputHttpResponse($statuscode, $statusmassage, $headers, $body);
        return;
    }

    $parsingBody = explode("&", $body);
    $login = explode("=", $parsingBody[0])[1];
    $password = explode("=",$parsingBody[1])[1];

    $dbPasswords = file_get_contents("passwords.txt");

    if($dbPasswords === false){
        $statuscode = $STATUS_CODE['500'];
        $statusmassage = 'not found';
        outputHttpResponse($statuscode, $statusmassage, $headers, $body);
        return;
    }

    $parsingDbPasswords = explode("\n", $dbPasswords);

    $result = false;

    foreach ($parsingDbPasswords as $dbPassword) {
        if($dbPassword == $login . ":" . $password){
            $result = true;
            break;
        }
    }


    if($result) {
        $statusmassage = '<h1 style="color:green">FOUND</h1>';
    }else{
        $statusmassage = 'not found';
    }
    outputHttpResponse($statuscode, $statusmassage, $headers, $body);
}

function parseTcpStringAsHttpRequest($string) {
    $parsingContext = explode("\n", $string);
    $method = '';
    $uri = '';
    $headers = [];
    $body = '';

    for($i = 0; $i < count($parsingContext); $i++) {
        if($i == 0 ) {
            $firstRow = explode(" ", $parsingContext[$i]);
            $method = trim($firstRow[0]);
            $uri = trim($firstRow[1]);
            continue;
        }
        if(strpos($parsingContext[$i], ":")){
            $newRow = explode(":", $parsingContext[$i]);
            $headerTitle = trim($newRow[0]);
            $headerBody = trim($newRow[1]);
            $headers[] = [$headerTitle, $headerBody];
            continue;
        }

        if(count($parsingContext) > 0){
            $body = $parsingContext[$i];
        }
    }

    return array(
        "method" => $method,
        "uri" => $uri,
        "headers" => $headers,
        "body" => $body
    );
}

$http = parseTcpStringAsHttpRequest($contents);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);