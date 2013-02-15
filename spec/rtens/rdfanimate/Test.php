<?php
namespace spec\rtens\rdfanimate;

use rtens\collections\Collection;
use rtens\rdfanimate\renderer\RdfaRendererFactory;

abstract class Test extends \PHPUnit_Framework_TestCase {

    private $rendered;

    /**
     * @var \rtens\rdfanimate\renderer\RdfaRenderer
     */
    private $renderer;

    protected function background() {
    }

    protected function setUp() {
        parent::setUp();

        $this->background();
    }

    protected function givenTheModel($json) {
        $model = Collection::toCollections(json_decode($json));
        $this->createRenderer($model);
    }

    protected function createRenderer($model) {
        $factory = new RdfaRendererFactory();
        $this->renderer = $factory->createRendererFor($model);
    }

    protected function whenIRender($markup) {
        $rendered = $this->renderer->render("<html><body>$markup</body></html>");
        if (strlen($rendered) <= 11) {
            $this->rendered = '';
        }
        $this->rendered = substr($rendered, 15, -15);
    }

    protected function thenTheResultShouldBe($expected) {
        $this->assertEquals($this->clean($expected), $this->clean($this->rendered));
    }

    protected function clean($string) {
        return trim(preg_replace('/\s*(\S.+\S)\s*/', '$1', $string));
    }
}
