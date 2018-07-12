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
    const DETECT_ORDER = 'JIS,SJIS,EUC-JP,CP936';
    const STANDARD_KANJI_REGEXP = '/[ぁ-ん血月件倹健兼券剣圏堅嫌建憲懸検権犬献研絹県肩見謙賢軒遣険顕験元原厳幻弦減源玄現言限個古呼固孤己庫弧戸故枯湖誇雇顧鼓五互午呉娯後御悟碁語誤護交侯候光公功効厚口向后坑好孔孝工巧幸広康恒慌抗拘控攻更校構江洪港溝甲皇硬稿紅絞綱耕考肯航荒行衡講貢購郊酵鉱鋼降項香高剛号合拷豪克刻告国穀酷黒獄腰骨込今困墾婚恨懇昆根混紺魂佐唆左差査砂詐鎖座債催再最妻宰彩才採栽歳済災砕祭斎細菜裁載際剤在材罪財坂咲崎作削搾昨策索錯桜冊刷察撮擦札殺雑皿三傘参山惨散桟産算蚕賛酸暫残仕伺使刺司史嗣四士始姉姿子市師志思指支施旨枝止死氏祉私糸紙紫肢脂至視詞詩試誌諮資賜雌飼歯事似侍児字寺慈持時次滋治璽磁示耳自辞式識軸七執失室湿漆疾質実芝舎写射捨赦斜煮社者謝車遮蛇邪借勺尺爵酌釈若寂弱主取守手朱殊狩珠種趣酒首儒受寿授樹需囚収周宗就州修愁拾秀秋終習臭舟衆襲週酬集醜住充十従柔汁渋獣縦重銃叔宿淑祝縮粛塾熟出術述俊春瞬准循旬殉準潤盾純巡遵順処初所暑庶緒署書諸助叙女序徐除傷償勝匠升召商唱奨宵将小少尚床彰承抄招掌昇昭晶松沼消渉焼焦照症省硝礁祥称章笑粧紹肖衝訟証詔詳象賞鐘障上丈乗冗剰城場壌嬢常情条浄状畳蒸譲醸錠嘱飾植殖織職色触食辱伸信侵唇娠寝審心慎振新森浸深申真神紳臣薪親診身辛進針震人仁刃尋甚尽迅陣酢図吹垂帥推水炊睡粋衰遂酔錘随髄崇数枢据杉澄寸世瀬畝是制勢姓征性成政整星晴正清牲生盛精聖声製西誠誓請逝青静斉税隻席惜斥昔析石積籍績責赤跡切拙接摂折設窃節説雪絶舌仙先千占宣専川戦扇栓泉浅洗染潜旋線繊船薦践選遷銭銑鮮前善漸然全禅繕塑措疎礎祖租粗素組訴阻僧創双倉喪壮奏層想捜掃挿操早曹巣槽燥争相窓総草荘葬藻装走送遭霜騒像増憎臓蔵贈造促側則即息束測足速俗属賊族続卒存孫尊損村他多太堕妥惰打駄体対耐帯待怠態替泰滞胎袋貸退逮隊代台大第題滝卓宅択拓沢濯託濁諾但達奪脱棚谷丹単嘆担探淡炭短端胆誕鍛団壇弾断暖段男談値知地恥池痴稚置致遅築畜竹蓄逐秩窒茶嫡着中仲宙忠抽昼柱注虫衷鋳駐著貯丁兆帳庁弔張彫徴懲挑朝潮町眺聴脹腸調超跳長頂鳥勅直朕沈珍賃鎮陳津墜追痛通塚漬坪釣亭低停偵貞呈堤定帝底庭廷弟抵提程締艇訂逓邸泥摘敵滴的笛適哲徹撤迭鉄典天展店添転点伝殿田電吐塗徒斗渡登途都努度土奴怒倒党冬凍刀唐塔島悼投搭東桃棟盗湯灯当痘等答筒糖統到討謄豆踏逃透陶頭騰闘働動同堂導洞童胴道銅峠匿得徳特督篤毒独読凸突届屯豚曇鈍内縄南軟難二尼弐肉日乳入如尿任妊忍認寧猫熱年念燃粘悩濃納能脳農把覇波派破婆馬俳廃拝排敗杯背肺輩配倍培媒梅買売賠陪伯博拍泊白舶薄迫漠爆縛麦箱肌畑八鉢発髪伐罰抜閥伴判半反帆搬板版犯班畔繁般藩販範煩頒飯晩番盤蛮卑否妃彼悲扉批披比泌疲皮碑秘罷肥被費避非飛備尾微美鼻匹必筆姫百俵標氷漂票表評描病秒苗品浜貧賓頻敏瓶不付夫婦富布府怖扶敷普浮父符腐膚譜負賦赴附侮武舞部封風伏副復幅服福腹複覆払沸仏物分噴墳憤奮粉紛雰文聞丙併兵塀幣平弊柄並閉陛米壁癖別偏変片編辺返遍便勉弁保舗捕歩補穂募墓慕暮母簿倣俸包報奉宝峰崩抱放方法泡砲縫胞芳褒訪豊邦飽乏亡傍剖坊妨帽忘忙房暴望某棒冒紡肪膨謀貿防北僕墨撲朴牧没堀奔本翻凡盆摩磨魔麻埋妹枚毎幕膜又抹末繭万慢満漫味未魅岬密脈妙民眠務夢無矛霧婿娘名命明盟迷銘鳴滅免綿面模茂妄毛猛盲網耗木黙目戻問紋門匁夜野矢厄役約薬訳躍柳愉油癒諭輸唯優勇友幽悠憂有猶由裕誘遊郵雄融夕予余与誉預幼容庸揚揺擁曜様洋溶用窯羊葉要謡踊陽養抑欲浴翌翼羅裸来頼雷絡落酪乱卵欄濫覧利吏履理痢裏里離陸律率立略流留硫粒隆竜慮旅虜了僚両寮料涼猟療糧良量陵領力緑倫厘林臨輪隣塁涙累類令例冷励礼鈴隷零霊麗齢暦歴列劣烈裂廉恋練連錬炉路露労廊朗楼浪漏老郎六録論和話賄惑枠湾腕]/u';

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

    /**
     * まともな日本語が一番多いものを、そのエンコーディングとみなす！
     *
     * @param $body
     * @return int|null|string
     */
    function detect_encoding($body){

        preg_match_all(self::STANDARD_KANJI_REGEXP, $body, $m);
        $size[self::INTERNAL_ENCODE] = sizeof($m[0]);

        $encodes = explode(',', self::DETECT_ORDER);
        foreach($encodes as $e){
            $tmp = mb_convert_encoding($body, self::INTERNAL_ENCODE, $e);
            preg_match_all(self::STANDARD_KANJI_REGEXP, $tmp, $m);
            $size[$e] = sizeof($m[0]);
        }

        arsort($size);

        return key($size);
    }

    function convert_body_encoding($body){

        if($body){

            $enc = $this->detect_encoding($body);

            if($enc and strtoupper($enc) != self::INTERNAL_ENCODE){

                $body = trim(mb_convert_encoding($body, self::INTERNAL_ENCODE, self::DETECT_ORDER));
            }
        }

        return preg_replace("/[\s\r\n]+$/", '', $body);
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
            'body'    => $cont['body'],
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


