<?php



require_once __DIR__ . '/vendor/autoload.php';

use handle\config\Api;
////$str = "{\"what\":\"login\",\"obj\":\"auth token\"}";
//
//$str = "{\"what\":\"login\",\"obj\":{\"form\":\"\",\"to\":\"\",\"content\":\"\"}}";
//
//$obj = \handle\message\SmsMessage::parse($str);
//
//var_dump($obj);

$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjp7ImlkIjo3OSwiYXZhdGFyIjoiL3VwbG9hZHMvdXNlcl9oZWFkcy83MS5qcGciLCJtb2JpbGUiOiIxNzY3NzA1NDQzNyIsIm5pY2tfbmFtZSI6Ilx1NWRmNFx1ODk3Zlx1NWZjNVx1ODBkYyJ9LCJpc3MiOiJodHRwczovL2hody5oaHcxLmNuIiwiYXVkIjoiaHR0cHM6Ly9oaHcuaGh3MS5jbiIsImlhdCI6MTY4Nzk2MTAyOCwibmJmIjoxNjg3OTYxMDI4LCJleHAiOjE2ODg1NjU4Mjh9.sew4nfhvRkZ0Phs7EAzBP0UwVMGRvbIkG-CMHaKBQyQ";

$http = \handle\util\HttpReprint::getInstance($token);

$res = $http->post(Api::MATCH_DETAIL, ['room_id' => 33972]);
//$res = $http->post(Api::CHECK_LOGIN);
$res = json_decode($res,JSON_UNESCAPED_UNICODE);

var_dump($res);

