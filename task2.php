<?php
function readHttpLikeInput() {
    $fileHttpRequest = fopen( 'php://stdin', 'r' );
    $store = "";
    $toRead = 0;

    while( $line = fgets( $fileHttpRequest ) ) {
        $store .= preg_replace("/\r/", "", $line);

        if (preg_match('/Content-Length: (\d+)/',$line,$matches)){
            $toRead=$matches[1]*1;
        }

        if ($line == "\r\n"){

            break;
        }
    }

    if ($toRead > 0){
        $store .= fread($fileHttpRequest, $toRead);
    }

    return $store;
}

$contents = readHttpLikeInput();

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
            $headers[$headerTitle] = [$headerBody];

            continue;
        }

        if(str_contains( $parsingContents[$i], '=')){
            echo $parsingContents[$i];
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
echo(json_encode($http, JSON_PRETTY_PRINT));