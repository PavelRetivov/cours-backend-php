<?php
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

function outputHttpResponse($statusCode, $statusMessage, $headers, $body) {
    echo "HTTP/1.1 $statusCode" . PHP_EOL;
    echo "Server: Apache/2.2.14 (Win32)" . PHP_EOL;
    echo "Connection: Closed" . PHP_EOL;
    echo "Content-Type: text/html; charset=utf-8" . PHP_EOL;
    echo "Content-Length: " . strlen($statusMessage)  . PHP_EOL;
    echo  PHP_EOL . "$statusMessage" ;
}

function processHttpRequest($method, $uri, $headers, $body) {
    if($method != "GET"){
        outputHttpResponse('400 Bad Request', 'not found', $headers, $body);
        return;
    }
    if(!str_starts_with($uri, "/sum?nums=")){
        outputHttpResponse('404 Not Found', 'not found', $headers, $body);
        return;
    }
    $numberString  = substr($uri, strlen("/sum?nums="));
    $numbers = explode(",", $numberString);
    if(count($numbers) == 0){
        outputHttpResponse('400 Bad Request', 'not found', $headers, $body);
        return;
    }
    outputHttpResponse('200 OK', array_sum($numbers), $headers, $body);

}

function parseTcpStringAsHttpRequest($string) {
    $parsingContext = explode(PHP_EOL, $string);
    $headers = [];
    $body = '';

    $firstRow = explode(" ", $parsingContext[0]);
    $method = trim($firstRow[0]);
    $uri = trim($firstRow[1]);

    $exp = "/[:]/";
    $i = 1;
    for(; $i < count($parsingContext); $i++) {
        if(preg_match($exp, $parsingContext[$i])){
            $newRow = explode(":", $parsingContext[$i]);
            $headerTitle = trim($newRow[0]);
            $headerBody = trim($newRow[1]);
            $headers[] = [$headerTitle, $headerBody];
            continue;
        }
        break;
    }
    for(; $i < count($parsingContext); $i++) {
        if(str_starts_with($parsingContext[$i], 'bookId')){
            $body = $parsingContext[$i];
        }
    }

    return [
        "method" => $method,
        "uri" => $uri,
        "headers" => $headers,
        "body" => $body
    ];
}

$http = parseTcpStringAsHttpRequest($contents);
processHttpRequest($http["method"], $http["uri"], $http["headers"], $http["body"]);