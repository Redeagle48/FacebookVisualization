<?php

$string = "https://graph.facebook.com/1428411860/posts?limit=25&since=1391773508";

$res = explode("/posts", $string);
echo $res[1];

