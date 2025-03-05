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
    $parsingContents = explode(PHP_EOL, $string);
    $headers = [];
    $body = '';

    $firstRow = explode(" ", $parsingContents[0]);
    $method = trim($firstRow[0]);
    $uri = trim($firstRow[1]);

    $exp = "/[:]/";
    $i = 1;
    for(; $i < count($parsingContents); $i++) {
        if(preg_match($exp, $parsingContents[$i])){
            $newRow = explode(":", $parsingContents[$i]);
            $headerTitle = trim($newRow[0]);
            $headerBody = trim($newRow[1]);
            $headers[] = [$headerTitle, $headerBody];
            continue;
        }
        break;
    }
    for(; $i < count($parsingContents); $i++) {
        if(str_starts_with($parsingContents[$i], 'bookId')){
            $body = $parsingContents[$i];
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