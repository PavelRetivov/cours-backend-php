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
        http_response_code(404);
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
        http_response_code(400);
        echo json_encode(["status" => "400 Bad Request", "message" => "url does not exist"]);

        return;
    }

    [$requestDomain, $file ] = explode("/", trim($uri, "/"), 2);

    try {
        responseFile($serverDomain[$requestDomain], $file);

    }catch (Exception $e){
        http_response_code(400);
        echo json_encode(["status" => "400 Bad Request", "message" => "url does not exist"]);

        return;
    }

    return;
}
http_response_code(400);
echo json_encode(["status" => "400 Bad Request", "message" => "method dont right"]);
