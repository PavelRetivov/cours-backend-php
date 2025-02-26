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

function outputHttpResponse($statuscode, $statusmessage, $headers, $body) {

    echo "HTTP/1.1 $statuscode\n";
    echo "Server: Apache/2.2.14 (Win32)\n";
    echo "Connection: Closed\n";
    echo "Content-Type: text/html; charset=utf-8\n";
    echo "Content-Length: " . strlen($statusmessage) . "\n";
    echo "\n$statusmessage" ;
}

function processHttpRequest($method, $uri, $headers, $body) {
    $statuscode = '200 OK';

    if($method != "GET"){
        $statuscode = '400 Bad Request';
        $statusmassage = 'not found';
        outputHttpResponse($statuscode, $statusmassage, $headers, $body);
        return;
    }
    if(!str_starts_with($uri, "/sum?nums=")){
        $statuscode = '404 Not Found';
        $statusmassage = 'not found';
        outputHttpResponse($statuscode, $statusmassage, $headers, $body);
        return;
    }


    $numberString  = substr($uri, strlen("/sum?nums="));
    $numbers = explode(",", $numberString);
    if(count($numbers) == 0){
        $statuscode = '400 Bad Request';
        $statusmassage = 'not found';
        outputHttpResponse($statuscode, $statusmassage, $headers, $body);
        return;
    }
    $sum = 0;
    foreach($numbers as $number){
        $sum += intval($number);
    }

    $statusmassage = $sum;
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

        if(str_starts_with($parsingContext[$i], 'bookId')){
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