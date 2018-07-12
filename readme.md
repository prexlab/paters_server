
メールの受信
-------------------------------------

## ムームーメール
line+***@dqc.jp
https://muumuu-mail.com/login

## sakuraメール
line-email@line-email.dqc.jp
https://secure.sakura.ad.jp/rscontrol/rs/mailsettings

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


Line Managerの使い方
-------------------------------------

メッセージ
--------------

## 友達追加メッセージ（機能しない？） 
https://admin-official.line.me/11766100/account/message/



ホーム（家マークを押したとき）
--------------

## ホーム設定
https://admin-official.line.me/11766100/home/profile/
- カバー画像

## 投稿一覧
https://admin-official.line.me/11766100/home/
- 投稿済みコンテンツ（コメント管理から削除可能）


アカウントページ（ホームからアカウント情報を見る）
--------------

## カバーデザイン
https://admin-official.line.me/11766100/page/design
- カバー画像
- ボタン色（トークルームの色も変わる）【重要】

## アカウント紹介
- プロフィール紹介文


アカウント設定（ポップアップ的な画面）
--------------

## 基本設定
https://admin-official.line.me/11766100/account/
- アイコン設定
- アカウント名（7日間変えられない）
- ステータスメッセージ（1時間変えられない）
- トークルームにメニューを表示しない【重要】
- QRコードのタグ
