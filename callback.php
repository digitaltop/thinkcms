<?php
 header('Content-Type:text/html; charset=utf-8');
$get = base64_decode(str_replace('cju943LKJvdwlkjt3987','=',str_replace('jcv8934glwejOYft','+',str_replace('Ii498vIK4io0', '/', $_GET['key']))));
echo($get);