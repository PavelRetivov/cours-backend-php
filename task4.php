<?php
function readHttpLikeInput() {
    $f = fopen( 'php://stdin', 'r' );
    $store = "";
    $toRead = 0;

    while(!feof($f)) {
        $line = fgets($f);
        $store .= preg_replace("/\r/", "", $line);

        if (preg_match('/Content-Length: (\d+)/',$line,$matches)){
            $toRead=$matches[1]*1;
        }

        if ($line == "\r\n"){

            break;
        }
    }

    if ($toRead > 0){
        $store .= fread($f, $toRead);
    }

    return $store;
}

$contents = readHttpLikeInput();

function outputHttpResponse($statusCode, $statusMessage) {
    echo "HTTP/1.1 $statusCode" . PHP_EOL;
    echo "Server: Apache/2.2.14 (Win32)" . PHP_EOL;
    echo "Connection: Closed" . PHP_EOL;
    echo "Content-Type: text/html; charset=utf-8" . PHP_EOL;
    echo "Content-Length: " . strlen($statusMessage)  . PHP_EOL;
    echo  PHP_EOL . "$statusMessage" ;
}

function parseKeyValue($param)
{
    return explode("=", $param)[1];
}

function processHttpRequest($method, $uri, $body) {
    if($method != "POST"){
        outputHttpResponse('400 Bad Request', 'the method is incorrect');

        return;
    }

    if(!str_starts_with($uri, "/api/checkLoginAndPassword")){
        outputHttpResponse('404 Not Found', 'url does not exist');

        return;
    }

    try {
        [$loginParam, $passwordParam] = explode("&", $body);

        if(!$loginParam || !$passwordParam){
            outputHttpResponse('400 Bad Request', 'data no correct');

            return;
        }

        $login = parseKeyValue($loginParam);
        $password = parseKeyValue($passwordParam);
    }catch (Exception){
        outputHttpResponse('400 Bad Request', 'not found');

        return;
    }
    $db_users = fopen("users.txt", 'r');

    if($db_users === false){
        outputHttpResponse("500 Internal Server Error", 'Internal Server Error');

        return;
    }

    $result = false;
    while (!feof($db_users)) {
        $dbUserInfo = fgets($db_users);
        [$dbLoginUser, $dbPasswordUser] = explode(":", $dbUserInfo, 2);

        if($dbLoginUser === $login){
            $result = (password_verify($password, $dbPasswordUser));

            break;
        }
    }

    $statusMassage = $result ? '<h1 style="color:green">FOUND</h1>' : 'password or login dont correct';
    outputHttpResponse('200 OK', $statusMassage);
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
processHttpRequest($http["method"], $http["uri"], $http["body"]);