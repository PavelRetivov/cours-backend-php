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

function parseTcpStringAsHttpRequest($contents) {
    $parsingContents = explode(PHP_EOL, $contents);
    $headers = [];
    $body = '';

    $firstRow = explode(" ", $parsingContents[0]);
    $method = trim($firstRow[0]);
    $uri = trim($firstRow[1]);

    for($i=1; $i < count($parsingContents); $i++) {
        if(str_contains( $parsingContents[$i], ':')){
            $newRow = explode(":", $parsingContents[$i]);
            $headerTitle = trim($newRow[0]);
            $headerBody = trim($newRow[1]);
            $headers[] = [$headerTitle, $headerBody];
            continue;
        }
        if(str_contains( $parsingContents[$i], '=')){
            $body = $parsingContents[$i];
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