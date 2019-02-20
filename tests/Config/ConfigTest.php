<?php

class ConfigTest extends SecurityTestCase
{
    public function testDefaultConfig()
    {
        $this->assertEquals([
            'xss_enable' => true,
            'html_purifier' => true
        ], config('security'));
    }

    public function testSetXssEnable()
    {
        config()->set('security.xss_enable', false);
        $this->assertEquals(false, config('security.xss_enable'));
    }


    public function testSetHtmlPurifierEnable()
    {
        config()->set('security.html_purifier', false);
        $this->assertEquals(false, config('security.html_purifier'));
    }
}

