<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Email;

use App\Services\LineService;

class LineServiceTest extends TestCase
{

    public function testCheckModeRegisterEmail()
    {
        $line = new LineService(new Email);

        $ret = $line->checkMode('uchida@p-rex.net');

        $this->assertEquals($ret, 'registerEmail');
    }

    public function testCheckModeCheckToken()
    {
        $line = new LineService(new Email);

        $ret = $line->checkMode('12345');

        $this->assertEquals($ret, 'checkToken');
    }

    public function testCheckModeOther()
    {
        $line = new LineService(new Email);

        $ret = $line->checkMode('aaaaaa');

        $this->assertEquals($ret, 'other');
    }

    public function testRegisterEmail()
    {
        $line = new LineService(new Email);

        $ret = $line->registerEmail('12345', 'uchida@p-rex.net');

        $this->assertEquals($ret['type'], 'text');
    }

    public function testPush()
    {
        $line = new LineService(new Email);

        $ret = $line->push('U3a18c70aa31fcb9dd3c10bec6cc64877', ['type'=>'text', 'text' => 'よろしくね ' . date('m/d H:i:s')]);

        $this->assertEquals($ret, 'text');
    }



    public function testReceiveEmail()
    {
        $line = new LineService(new Email);

        $email = [
            'token'=> '15766',
            'to'  => 'dummy@yamada.com',
            'from'=> 'yamada@site.com',
            'subject'=> 'テスト',
            'text'=> 'testReceiveEmail によるメールテスト' . date('m/d H:i:s'),
        ];

        $ret = $line->receiveEmail($email);

        $this->assertEquals(false, 'text');
    }

}
