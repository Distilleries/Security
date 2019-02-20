<?php

class XSSTest extends SecurityTestCase
{

    public function testResponseOkDefaultValues()
    {
        $this->call('POST', 'inputs');
        $this->assertResponseOk();

    }

    public function testXssDisableAndHtmlPurifierDisable()
    {
        config()->set('security.xss_enable', false);
        config()->set('security.html_purifier', false);

        $this->json('POST', 'inputs', ['param' => 'document.cookie']);
        $this->assertResponseOk();
        $this->assertJson($this->response->getContent());
        $datas = json_decode($this->response->getContent());
        $this->assertEquals('document.cookie', $datas->param);

    }

    public function testXssEnableAndHtmlPurifierEnable()
    {
        config()->set('security.xss_enable', true);
        config()->set('security.html_purifier', true);

        $this->json('POST', 'inputs', [
            'param_html_purifier' => '<iframe src="http://google.fr"></iframe>',
            'param_xss_enable' => 'document.cookie',
        ]);
        $this->assertResponseOk();
        $this->assertJson($this->response->getContent());
        $datas = json_decode($this->response->getContent());

        $this->assertEquals('', $datas->param_html_purifier);
        $this->assertEquals('[removed]', $datas->param_xss_enable);

    }

    public function testXssEnable()
    {
        config()->set('security.xss_enable', true);
        config()->set('security.html_purifier', false);

        $this->json('POST', 'inputs', [
            'param_html_purifier' => '<iframe src="http://google.fr"></iframe>',
            'param_xss_enable' => 'document.cookie',
        ]);
        $this->assertResponseOk();
        $this->assertJson($this->response->getContent());
        $datas = json_decode($this->response->getContent());

        $this->assertEquals('<iframe src="http://google.fr"></iframe>',
            html_entity_decode($datas->param_html_purifier));
        $this->assertEquals('[removed]', $datas->param_xss_enable);
    }


    public function testHtmlPurifierEnable()
    {
        config()->set('security.html_purifier', true);
        config()->set('security.xss_enable', false);


        $this->json('POST', 'inputs', [
            'param_html_purifier' => '<iframe src="http://google.fr"></iframe>',
            'param_xss_enable' => 'document.cookie',
        ]);
        $this->assertResponseOk();
        $this->assertJson($this->response->getContent());
        $datas = json_decode($this->response->getContent());

        $this->assertEquals('', $datas->param_html_purifier);
        $this->assertEquals('document.cookie', $datas->param_xss_enable);

    }
}

