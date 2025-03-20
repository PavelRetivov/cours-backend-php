<?php
function processHttpRequest($method, $uri){
    if($method != "GET"){
        echo ["status" => "400 Bad Request", "message" => "method dont right"];
    }
    [$folder, $file ] = explode("/", trim($uri, "/"), 2);
    echo $folder;
    if($folder === "another.shpp.me" || $folder === "student"){
        if($folder === "another.shpp.me"){
            echo file_get_contents("another/" . $file);
        }
        if($folder === "student"){
            echo file_get_contents("student/" . $file);
        }
    }else{
        echo ["status" => "400 Bad Request", "message" => "url dont right"];
    }
};

processHttpRequest("GET", "another.shpp.me/hello.html");