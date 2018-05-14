<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Email;

use App\Services\MailDecoderService;

class MailDecoderServiceTest extends TestCase
{

    public function testMyMimeDecode()
    {

        $stdin = file_get_contents(storage_path().'/framework/testing/a.eml');

        $mime = new MailDecoderService;
        $email = $mime->myMimeDecode($stdin);

        print_r([$email]);

        $this->assertEquals($email, 'text');
    }

    public function testMyMimeDecode2()
    {

        $stdin = file_get_contents(storage_path().'/framework/testing/b.eml');

        $mime = new MailDecoderService;
        $email = $mime->myMimeDecode($stdin);

        print_r([$email]);

        $this->assertEquals($email, 'text');
    }


    public function testTrimBodyText()
    {

        $stdin = file_get_contents(storage_path().'/framework/testing/google_calendar_email.txt');

        $mime = new MailDecoderService;
        $email = $mime->myMimeDecode($stdin);

        $text = $mime->trimBodyText($email['text']);

        print_r($text);

        $this->assertEquals($email, 'text');
    }

}
