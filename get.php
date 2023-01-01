<?php

include "assets/fetch/fetch.php";

$logindata = json_decode(file_get_contents("assets/config/auth.json"), true);
$services = fetch($logindata['customer_id'], $logindata['license_key']);

echo $services;

var_dump($services);