<?php

/*##########################################################
#１．コマンドラインからPEARライブラリーをインストールする。
#２．メールログ用メールアドレス「maillog」を作成する。
############################################################
`pear config-create ~/ ~/.pearrc`;
`pear -c ~/.pearrc install -o PEAR`;
`pear -c ~/.pearrc install Mail_mimeDecode`; 
`pear -c ~/.pearrc install Net_POP3`;
*/


################################################################################
#120901 uchida ローカルはデフォルトで、sakuraはinitial.php でパスを通している。
# require_once('Net/POP3.php');
# require_once('Mail/mimeDecode.php');


namespace App\Services;


include_once(base_path().'/vendor/pear/mail_mime-decode/Mail/mimeDecode.php');


################################################################################

class MailDecoderService {

    const MAIL_REGEXP = "`[-!#$%&\'*+\\./0-9A-Z^_\`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_\`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_\`a-z{|}~]+`";
    const DEFAULT_LIMITED_SIZE_BYTE = 200000000; #200MB
    const MAILER_DAEMON_STRING = 'MAILER-DAEMON';
    const DECODABLE_SIZE_BYTE = 1000000; #1MB;

    var $ngWords= array(
        'from' => array('dummy.com'),
        'subject' => array('spam'),
    );

    var $pop3;

    function __construct($account = null){

        global $_MST;

        if($account){
            $this->pop3 = new Net_POP3();
            $this->pop3->connect($account['host'], $account['port']);
            $ret = $this->pop3->login($account['username'], $account['password']);

            if(PEAR::isError($ret)) {
                trigger_error(strip_tags($ret->getMessage()), E_USER_ERROR);
            }
        }
    }
    /*
    #############################
    #150220 uchida 超納言ではメールサーバー接続は使っていない。なので紛らわしいので該当箇所廃止。
    #############################
    
        #110620 uchida 指定した容量内に収まるように古いメールを削除する。デフォルトで200MB以内
        function saveMailSpace($limited_size_byte = null){
        
            #110620 uchida 入力値の解析
            if(is_numeric($limited_size_byte)){
                #do nothing
            }elseif(preg_match('/^([,0-9]+) *(B|KB|MB|GB)$/i', $limited_size_byte, $mc)){
                $num = str_replace(',', '', $mc[1]);
                $unit = array_search(strtoupper($mc[2]), array(1=>'B', 1000=>'KB', 1000*1000=>'MB', 1000*1000*1000=>'GB'));
                $limited_size_byte = $num * $unit;
            }else{
                $limited_size_byte = self::DEFAULT_LIMITED_SIZE_BYTE;
            }
        
            $occupied_size = $this->pop3->getSize();
            $delete_size_required = $occupied_size - $limited_size_byte;
    
    
            #110620 uchida 削除する必要があれば削除
            if($delete_size_required > 0){	
            
                $deleted_size = 0;
                $mails = $this->pop3->getListing();
                
                foreach($mails as $key=>$mail) {
    
                    if($deleted_size > $delete_size_required){
                        #110620 uchida 必要なサイズ確保できれば終了。その旨返す
                        $this->pop3->disconnect();
                        return array('flag'=>true, 'deleted_num'=>$key + 1, 'deleted_byte'=>$deleted_size);
                        break;
                    }else{
                        $this->pop3->deleteMsg($mail["msg_id"]);
                    }
                    $deleted_size += $mail['size'];
                }
    
            #110620 uchida 削除の必要なければその旨返す
            }else{
                return array('flag'=>false, 'remaining_byte'=>$delete_size_required * -1);
            }
        }
    
    
        #110620 uchida メールキュー内のデータを取得する
        function getMailAll($delete_flag = true){
        
            #変数初期化
            $rets = array();
            
            $mails = $this->pop3->getListing();
    
            if($mails === false) return false;	
    
            foreach($mails as $key=>$mail) {
    
                $message = $this->pop3->getMsg($mail["msg_id"]);
    
                if($mail['size'] > self::DECODABLE_SIZE_BYTE){
                    $error_flag = 'OVER_SIZE';#110716 uchida 添付ファイルは受信しない仕様なのでフラグとしては未使用
                    $message = substr($message, 0, self::DECODABLE_SIZE_BYTE);
                }
                
                $ret = $this->myMimeDecode($message);
                
                #110620 uchida 問題なければ追加
                if(str_replace($this->ngWords['from'], '', $ret['from']) == $ret['from']
                && str_replace($this->ngWords['subject'], '', $ret['subject']) == $ret['subject']
                && $ret['from'] && $ret['to'] && $ret['body']){
                    $rets[] = $ret;
                }
    
                #110620 uchida メールの削除
                if($delete_flag) $this->pop3->deleteMsg($mail['msg_id']);
            }
        
            $this->pop3->disconnect();
        
            return $rets;
        }
    */

    const INTERNAL_ENCODE = 'UTF-8';
    const DETECT_ORDER = 'UTF-8,JIS,SJIS,EUC-JP,AUTO,CP936';

    #150220 uchida http://www.hand-in-hand.biz/c-board/c-board.cgi?cmd=ntr;tree=28;id=0002 	
    function getbody($structure, $i=null, $ary=null){

        if (strtolower($structure->ctype_primary) == "multipart") {

            //複数本文があるメール（本文を１件づつ処理する）
            foreach ((array)$structure->parts as $part) {
                //タイプ
                if (property_exists($part,'disposition') and $part->disposition == "attachment") {
                    //添付ファイル
                    $ary['files'][$i]['type'] = strtolower($part->ctype_primary)."/".strtolower($part->ctype_secondary);
                    $ary['files'][$i]['name'] = $part->ctype_parameters['name'];
                    $ary['files'][$i]['body'] = $part->body;
                    $i++;
                } else {
                    switch (strtolower($part->ctype_primary)) {
                        case "image": //HTML本文中の画像
                            $ary['files'][$i]['type'] = strtolower($part->ctype_primary)."/".strtolower($part->ctype_secondary);
                            $ary['files'][$i]['name'] = $part->ctype_parameters['name'];
                            $ary['files'][$i]['cid'] = trim($part->headers['content-id'], "<>");
                            $ary['files'][$i]['body'] = $part->body;
                            $i++;
                            break;
                        case "text": //テキスト本文の抽出
                            if ($part->ctype_secondary=="plain") {
                                $ary['body'] = self::convert_body_encoding($part->body);
                            } else { //HTML本文
                                $ary[$part->ctype_secondary] = self::convert_body_encoding($part->body);
                            }
                            break;
                        case "multipart": //マルチパートの中にマルチパートがある場合（MS-OutlookExpressからHTML形式で送信した場合）
                            $ary = self::getbody($part, $i, $ary);
                            break;
                    }
                }
            }
        } elseif (strtolower($structure->ctype_primary) == "text" && $structure->body) {
            //テキスト本文のみのメール
            $ary['body'] = self::convert_body_encoding($structure->body);
        }

        return $ary;
    }


    function convert_body_encoding($body){

        if($body){

            //docomoのエラーメールはUTF-8で戻ってくる
            $enc = mb_detect_encoding($body, self::DETECT_ORDER);

            if($enc and strtoupper($enc) != self::INTERNAL_ENCODE){

                $body = trim(mb_convert_encoding($body, self::INTERNAL_ENCODE, self::DETECT_ORDER));
            }
        }

        return $body;
    }


    function myMimeDecodeFunc($structure, $get_attach_flag = true){

        $cont = self::getbody($structure);

        #110620 uchida メール構成要素の抽出
        $ret = array(
            'id'      => $structure->headers['message-id'],
            'date'    => date('Y-m-d H:i:s', strtotime($structure->headers['date'])),
            'to'      => $this->parseEmail($structure->headers['to']),
            'from'    => $this->parseEmail($structure->headers['from']),
            'subject' => $this->J2U($structure->headers['subject']),
            'body'    => preg_replace("/[\s\r\n]+$/", '', $cont['body']),
            'headers' => $structure->headers,#110727 uchida 追加
        );

        #110620 uchida MAILER-DAEMON対策					 
        if(strpos($ret['from'], self::MAILER_DAEMON_STRING) !== false){

            $ret['to']    = self::MAILER_DAEMON_STRING . '-TO';

            //これで元のメール内容が取得できることは/mailbuffer/sample/mailer-daemon/で検証済み
            $ret['mailer-daemon'] = $structure->parts ? end($structure->parts)->parts[0] : $structure;
        }

        #110620 uchida 添付ファイルの対応
        foreach((array)array_get($cont, 'files') as $f){

            #150227 uchida falseで取得回避できる
            if($get_attach_flag){
                $tmp_name = tempnam(sys_get_temp_dir(), 'att');
                @file_put_contents($tmp_name, $f['body']);
            }

            $ret['attachment'][] = array('name'=>$f['name'], 'tmp_name'=>$tmp_name);
        }


        $ret['text'] = $ret['body'];#110706 uchida text は body のエイリアス

        return $ret;
    }

    function myMimeDecode($message, $get_attach_flag=true){

        #110620 uchida メール構造の取得
        $decoder = new \Mail_mimeDecode($message, "\n");

        $structure = $decoder->decode(array(
                'include_bodies'=>true,
                'decode_bodies' =>true,
                'decode_headers'=>true)
        );

        // print_r([__LINE__, $structure]);

        $ret = $this->myMimeDecodeFunc($structure, $get_attach_flag);

        $ret['token'] = $this->getTransToken($message);

        $ret['text'] = $this->trimBodyText($ret['text']);


        return $ret;
    }


    function trimBodyText($text){

        $text = preg_replace('/<http.+?>/', '', $text);
        $text = preg_replace('/<.+?@.+>/', '', $text);
        $text = preg_replace("/\[image:.+?\]/", "", $text);

        $text = preg_replace("/[\r\n]{2,}/", "\n", $text);

        //googleカレンダー用設定
        $text = preg_replace("/次の登録済みカレンダーから.+$/us", "", $text);



        return $text;
    }



    function getTransToken($message){

        if(preg_match(config('app.trans_token_regexp'), $message, $mc)){

            return $mc[1];
        }
        return false;
    }

    const SEND_TO_BCC = 'undisclosed-recipients:;';

    #110620 uchida ヘッダーからメールアドレス文字列を抽出する
    function parseEmail($addr) {
        if(preg_match(self::MAIL_REGEXP, $addr, $mc)){
            return $mc[0];
        }elseif(preg_match('/'.self::MAILER_DAEMON_STRING.'/', $addr, $mc)){
            return $mc[0];
        }else{
            //	trigger_error('Error in self::parseEmail() [' . U2E($addr) . ']', E_USER_WARNING);
            return null;
        }
    }


    #110620 uchida jis から utf-8 に変換する
    function J2U($str) {
        if(mb_detect_encoding($str, 'auto') == 'UTF-8'){
            return $str;
        }elseif(mb_detect_encoding($str, self::DETECT_ORDER)){
            return mb_convert_encoding($str, 'UTF-8', self::DETECT_ORDER);
        }else{
            trigger_error('Can not detect encoding in self::J2U() [' . $str . ']', E_USER_WARNING);
            return null;
        }
    }
}


