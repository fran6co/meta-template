<?php

namespace MetaTemplate\Test\Template;

use MetaTemplate\Template\MustacheTemplate;

class MustacheTemplateTest extends \PHPUnit_Framework_TestCase
{
    function test()
    {
        $templ = new MustacheTemplate(function() {
            return 'Hello {{name}}!';
        });

        $context = (object) array(
            'name' => 'Jim'
        );

        $this->assertEquals('Hello Jim!', $templ->render($context));
    }
}
