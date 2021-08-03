<?php

include __DIR__."/../vendor/autoload.php";


$hello = new \Elitelib\Hello();

 $word = $hello->sayHello("composer");

 echo $word;