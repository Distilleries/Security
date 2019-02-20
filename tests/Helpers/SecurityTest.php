<?php

class SecurityTest extends SecurityTestCase
{


    public function testRemovedXssClean()
    {

        $params = [
            'document.cookie' => '[removed]',
            'document.write' => '[removed]',
            '.parentNode' => '[removed]',
            '.innerHTML' => '[removed]',
            'window.location' => '[removed]',
            '-moz-binding' => '[removed]',
            '<!--' => '&lt;!--',
            '-->' => '--&gt;',
            '<![CDATA[' => '&lt;![CDATA[',
            '<comment>' => '&lt;comment&gt;'
        ];

        $faker = Faker\Factory::create();


        $xss = new \Distilleries\Security\Helpers\Security();
        foreach ($params as $value => $replace) {
            $sentance = $faker->sentence(6);
            $this->assertEquals($sentance . $replace, $xss->xss_clean($sentance . $value));
        }
    }

    public function testSanitizeNaughtyScriptingElements()
    {
        $params = [
            'eval(\'some code\')' => 'eval&#40;\'some code\'&#41;',
            'alert(\'some code\')' => 'alert&#40;\'some code\'&#41;',
            'cmd(\'some code\')' => 'cmd&#40;\'some code\'&#41;',
            'passthru(\'some code\')' => 'passthru&#40;\'some code\'&#41;',
            'exec(\'some code\')' => 'exec&#40;\'some code\'&#41;',
            'system(\'some code\')' => 'system&#40;\'some code\'&#41;',
            'fopen(\'some code\')' => 'fopen&#40;\'some code\'&#41;',
            'fsockopen(\'some code\')' => 'fsockopen&#40;\'some code\'&#41;',
            'file(\'some code\')' => 'file&#40;\'some code\'&#41;',
            'file_get_contents(\'some code\')' => 'file_get_contents&#40;\'some code\'&#41;',
            'readfile(\'some code\')' => 'readfile&#40;\'some code\'&#41;',
            'unlink(\'some code\')' => 'unlink&#40;\'some code\'&#41;',
        ];

        $faker = Faker\Factory::create();

        $xss = new \Distilleries\Security\Helpers\Security();
        foreach ($params as $value => $replace) {
            $sentance = $faker->sentence(6);
            $this->assertEquals($sentance . $replace, $xss->xss_clean($sentance . $value));
        }
    }


    public function testSanitizeNaughtyHtmlElements()
    {
        $params = [
            '<blink>' => '&lt;blink&gt;',
            '<alert>' => '&lt;alert&gt;',
            '<applet>' => '&lt;applet&gt;',
            '<audio>' => '&lt;audio&gt;',
            '<basefont>' => '&lt;basefont&gt;',
            '<base>' => '&lt;base&gt;',
            '<behavior>' => '&lt;behavior&gt;',
            '<bgsound>' => '&lt;bgsound&gt;',
            '<blink>' => '&lt;blink&gt;',
            '<body>' => '&lt;body&gt;',
            '<embed>' => '&lt;embed&gt;',
            '<expression>' => '&lt;expression&gt;',
            '<form>' => '&lt;form&gt;',
            '<frameset>' => '&lt;frameset&gt;',
            '<frame>' => '&lt;frame&gt;',
            '<head>' => '&lt;head&gt;',
            '<html>' => '&lt;html&gt;',
            '<ilayer>' => '&lt;ilayer&gt;',
            '<iframe>' => '&lt;iframe&gt;',
            '<input>' => '&lt;input&gt;',
            '<isindex>' => '&lt;isindex&gt;',
            '<layer>' => '&lt;layer&gt;',
            '<link>' => '&lt;link&gt;',
            '<meta>' => '&lt;meta&gt;',
            '<object>' => '&lt;object&gt;',
            '<plaintext>' => '&lt;plaintext&gt;',
            '<style>' => '&lt;style&gt;',
            '<script>' => '[removed]',
            '<textarea>' => '&lt;textarea&gt;',
            '<title>' => '&lt;title&gt;',
            '<video>' => '&lt;video&gt;',
            '<xml>' => '&lt;xml&gt;',
            '<xss>' => '[removed]',
        ];

        $faker = Faker\Factory::create();
        $xss = new \Distilleries\Security\Helpers\Security();
        foreach ($params as $value => $replace) {
            $sentance = $faker->sentence(6);
            $this->assertEquals($sentance . $replace, $xss->xss_clean($sentance . $value));
        }
    }

    public function testDisallowJavascriptLink()
    {
        $params = [
            '<a href="javascript:alert(\'test\')">Test</a>' => '<a >Test</a>',
            '<a href="javascript:window.document">Test</a>' => '<a >Test</a>',
            '<a href="javascript:window.document.cookie">Test</a>' => '<a >Test</a>',
            '<img src="javascript:alert(\'test\')" />' => '<img  />',
            '<img src="javascript:window.document" />' => '<img  />',
            '<img src="javascript:window.document.cookie" />' => '<img  />',
        ];

        $faker = Faker\Factory::create();
        $xss = new \Distilleries\Security\Helpers\Security();
        foreach ($params as $value => $replace) {
            $sentance = $faker->sentence(6);
            $this->assertEquals($sentance . $replace, $xss->xss_clean($sentance . $value));
        }
    }

    public function testEscapeAttribute()
    {
        $params = [
            '%'=>'\\\\%',
            '_'=>'\\\\_',
            '\''=>'\'\'',
            '"'=>'\\\\"',
            '<'=>'\\\\<',
            '>'=>'\\\\>',
            '('=>'\\\\(',
            ')'=>'\\\\)',
            '{'=>'\\\\{',
            ']'=>'\\\\}',
            ':'=>'\\\\:',
            '/'=>'\\\\/',
            '\\'=>'\\\\'
        ];

        $faker = Faker\Factory::create();
        foreach ($params as $value=>$replace) {
            $sentance = $faker->sentence(6);
            $this->assertEquals($sentance . $replace, Distilleries\Security\Helpers\Security::escapeLike($sentance . $value));
        }
    }

    public function testSanitizeFilename(){
        $faker = Faker\Factory::create();
        $file = $faker->slug .'.'. $faker->fileExtension();

        $params = [
            "../".$file=>$file,
            "<!--".$file=>$file,
            "-->".$file=>$file,
            "<".$file=>$file,
            ">".$file=>$file,
            "'".$file=>$file,
            '"'.$file=>$file,
            '&'.$file=>$file,
            '$'.$file=>$file,
            '#'.$file=>$file,
            '{'.$file=>$file,
            '}'.$file=>$file,
            '['.$file=>$file,
            ']'.$file=>$file,
            '='.$file=>$file,
            ';'.$file=>$file,
            '?'.$file=>$file,
            "%20".$file=>$file,
            "%22".$file=>$file,
            "%3c".$file=>$file,
            "%253c".$file=>$file,
            "%3e".$file=>$file,
            "%0e".$file=>$file,
            "%28".$file=>$file,
            "%29".$file=>$file,
            "%2528".$file=>$file,
            "%26".$file=>$file,
            "%24".$file=>$file,
            "%3f".$file=>$file,
            "%3b".$file=>$file,
            "%3d".$file=>$file,
            "./".$file=>$file,
            "/".$file=>$file,
        ];

        $xss = new \Distilleries\Security\Helpers\Security();
        foreach ($params as $value => $replace) {
            $this->assertEquals( $replace, $xss->sanitize_filename($value));
        }
    }

    public function testSanitizeFilenameNotRelative(){
        $faker = Faker\Factory::create();
        $file = $faker->slug .'.'. $faker->fileExtension();

        $params = [
            "../".$file=>$file,
            "<!--".$file=>$file,
            "-->".$file=>$file,
            "<".$file=>$file,
            ">".$file=>$file,
            "'".$file=>$file,
            '"'.$file=>$file,
            '&'.$file=>$file,
            '$'.$file=>$file,
            '#'.$file=>$file,
            '{'.$file=>$file,
            '}'.$file=>$file,
            '['.$file=>$file,
            ']'.$file=>$file,
            '='.$file=>$file,
            ';'.$file=>$file,
            '?'.$file=>$file,
            "%20".$file=>$file,
            "%22".$file=>$file,
            "%3c".$file=>$file,
            "%253c".$file=>$file,
            "%3e".$file=>$file,
            "%0e".$file=>$file,
            "%28".$file=>$file,
            "%29".$file=>$file,
            "%2528".$file=>$file,
            "%26".$file=>$file,
            "%24".$file=>$file,
            "%3f".$file=>$file,
            "%3b".$file=>$file,
            "%3d".$file=>$file,
            "./".$file=>"./".$file,
            "/".$file=>"/".$file,
        ];

        $xss = new \Distilleries\Security\Helpers\Security();
        foreach ($params as $value => $replace) {
            $this->assertEquals( $replace, $xss->sanitize_filename($value,true));
        }
    }
}

