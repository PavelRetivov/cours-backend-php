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
echo(json_encode($http, JSON_PRETTY_PRINT));
?>