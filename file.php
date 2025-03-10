<?php
$NAME_DATABASE = "dataBase.txt";
$dbInFile = file_get_contents($NAME_DATABASE);
if($dbInFile === false){
    $error = "Error: File not found";
}else{
    $parserDbInFile = explode(":", $dbInFile);
    $counter = $parserDbInFile[1];
    $putCounterInDb = $parserDbInFile[0] . ":" . ($counter + 1);
    file_put_contents($NAME_DATABASE, $putCounterInDb);

}
?>

<div class="wrapper">
    <div class="header">
        <div class="logo position-center">
            <img src="logo.png" alt="logo">
        </div>
        <div class="title position-center">
            <h1>Counter of activities per page</h1>
        </div>
        <div class="nav position-center">
            <a href="#">Home</a>
            <a href="#">About</a>
            <a href="#">Contact</a>
        </div>
    </div>
    <div class="main-content">
        <div class="counter">
            <h1> Page Visit Counter</h1>
            <p> You have visited this page:
                <span> <strong><?= $counter ?? $error ?></strong></span>
            </p>
        </div>
    </div>
    <div class="footer">
        <div class="footer-content"></div>
    </div>
</div>


<style>
    *{
        padding: 0;
        margin: 0;
    }
    .wrapper{
        display: flex;
        flex-direction: column;
        height: 100%;
        width: 100%;
    }
    .header{
        width: 100%;
        height: 25%;
        display: flex;
        justify-content: space-around;
        background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(9,96,121,0.8379726890756303) 35%, rgba(0,212,255,1) 100%);
    }
    .position-center{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .nav a{
        color: white;
        text-decoration: none;
        margin: 0 10px;
        font-size: 25px;
    }

    .nav a:hover{
        color: red;
    }
    .main-content{
        width: 100%;
        height: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f1f1f1;
    }

    .counter{
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: start;
        gap: 25%;
        align-items: center;
        margin: 10% 0 0 0;
    }
    .counter h1{
        font-size: 50px;
        color: #333;
    }
    .counter p{
        font-size: 30px;
        color: #333;
    }
    .footer{
        width: 100%;
        height: 25%;
        background-color: #333;
    }
</style>

