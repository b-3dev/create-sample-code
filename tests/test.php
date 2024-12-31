<?php
require_once 'CreateSampleCode.php';
use CreateSampleCode;

$code = new CreateSampleCode('https://req.wiki-api.ir/apis-1/ChatGPT', 'GET');
$code->setPayloads('url-encode', [
    "q" => "hello AI"
]);
$code->setHeaders([
    "Content-Type" => "application/json"
]);
$code->setTimeout(30, 10);
echo $code->fetchCode(1);

?>