<?php

use Illuminate\Support\Facades\Http;


function getTimeZone()
{
    $ip_address = Http::get('https://api.ipify.org')->body();
    $get_ip_data = Http::get('http://ip-api.com/json/' . $ip_address)->body();

    return json_decode($get_ip_data)->timezone;
}
