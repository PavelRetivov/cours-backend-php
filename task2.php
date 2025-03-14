<?php
function readHttpLikeInput() {
    $f = fopen( 'php://stdin', 'r' );
    $store = "";
    $toRead = 0;
    while( $line = fgets( $f ) ) {
        $store .= preg_replace("/\r/", "", $line);
        if (preg_match('/Content-Length: (\d+)/',$line,$matches))
            $toRead=$matches[1]*1;
        if ($line == "\r\n")
            break;
    }
    if ($toRead > 0)
        $store .= fread($f, $toRead);
    return $store;
}

$contents = readHttpLikeInput();

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
echo(json_encode($http, JSON_PRETTY_PRINT));
?>