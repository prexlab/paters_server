
メールの受信
-------------------------------------

## ムームーメール
line+***@dqc.jp

## sakuraメール
line-email@line-email.dqc.jp

## .mailfilter
cc "| /usr/local/bin/php -q /home/dtv77899/www/line_email/public/relay.php"

## relay.php
$url = 'https://'. DOMAIN . '/api/receive_email/' . $name;
file_get_contents($url);

## LineService.php
line+[****]@dqc.jp から tokenを割り出し、user_idを割り出し、lineにPOSTする。


lineのやりとり
-------------------------------------

## console
https://developers.line.me/console/

## callback
https://line-email.dqc.jp/api/callback

## LineService
内容に応じて、replyする


メール取り込みテスト
-------------------------------------

http://localhost:9940/api/receive_email_test/all
http://localhost:9940/api/receive_email_test/*************.txt


その他
-------------------------------------

## 挨拶文の変更
https://admin-official.line.me/11766100/account/message/

