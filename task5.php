<?php

function isUriValid($uri){
    $regex = "/^\/?[a-zA-Z.]+(\/[a-zA-Z.]+)+\/?$/";
    return (bool)preg_match($regex, $uri);
}

function responseFile($domain, $file)
{
    try {
        echo file_get_contents($domain . $file);
    }catch (Exception $e){
        echo json_encode(["status" => "404 Not Found", "message" => "file not found"]);
        return;
    }
}

if($_SERVER['REQUEST_METHOD'] == "GET"){
    $requestString = file_get_contents("php://input");
    $request = json_decode($requestString, true);
    $uri = isUriValid($request['uri']) ? $request['uri'] : null;
    $serverDomain = ["another.shpp.me" => 'another/', "student" => 'student/'];

    if(!$uri) {
        echo json_encode(["status" => "400 Bad Request", "message" => "url does not exist"]);

        return;
    }

    [$requestDomain, $file ] = explode("/", trim($uri, "/"), 2);

    if(!isset($serverDomain[$requestDomain])){
        echo json_encode(["status" => "400 Bad Request", "message" => "url does not exist"]);

        return;
    }

    responseFile($serverDomain[$requestDomain], $file);

    return;
}

echo json_encode(["status" => "400 Bad Request", "message" => "method dont right"]);