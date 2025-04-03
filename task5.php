<?php

function isUriValid($uri){
    $regex = "/^\/?[a-zA-Z.]+(\/[a-zA-Z.]+)+\/?$";
    return (bool)preg_match($regex, $uri);
}

if($_SERVER['REQUEST_METHOD'] == "GET"){
    $requestString = file_get_contents("php://input");
    $request = json_decode($requestString, true);

    $uri = isUriValid($request['uri']) ? $request['uri'] : null;
    if($uri){
        [$folder, $file ] = explode("/", trim($uri, "/"), 2);
        if(in_array($folder, ["another.shpp.me", "student"])){
            if($folder === "another.shpp.me"){
                try {
                    echo file_get_contents("another/" . $file);
                }catch (Exception $e){
                    echo json_encode(["status" => "404 Not Found", "message" => "file not found"]);
                    return;
                }
            }
            if($folder === "student"){
                try {
                    echo file_get_contents("student/" . $file);
                }catch (Exception $e){
                    echo json_encode(["status" => "404 Not Found", "message" => "file not found"]);
                    return;
                }
            }
        }else{
            echo json_encode(["status" => "400 Bad Request", "message" => "uri don`t right"]);
        }
    }else{
        echo json_encode(["status" => "400 Bad Request", "message" => "uri don`t right"]);
    }
}else{
    echo json_encode(["status" => "400 Bad Request", "message" => "method dont right"]);
};

