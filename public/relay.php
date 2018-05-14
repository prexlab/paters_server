<?php



// ここは config/app.php と合わせること
define('TRANS_TOKEN_REGEXP', '/r\+(.{32})@line.her.jp/');
define('STORAGE_PATH', __DIR__ . '/../storage/emails/');
define('DOMAIN', 'line-email.dqc.jp');


if(!empty($_GET['f'])){
    $stdin = file_get_contents(STORAGE_PATH . $_GET['f']);
}else{
    $stdin = file_get_contents("php://stdin");
}


file_put_contents(STORAGE_PATH . 'debug.txt', $stdin);

if(preg_match(TRANS_TOKEN_REGEXP, $stdin, $mc)){


    file_put_contents(STORAGE_PATH . 'debug.txt', print_r($mc, 1), FILE_APPEND);

    $token = $mc[1];

    $name =  sprintf('%s_%s.txt', $token, date('ymdHis'));

    $path = STORAGE_PATH . $name;

    file_put_contents($path, $stdin);


    $url = 'https://'. DOMAIN . '/api/receive_email/' . $name;

    file_get_contents($url);
}

