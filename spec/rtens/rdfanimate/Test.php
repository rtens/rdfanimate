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
        $factory = new RdfaRendererFactory();
        $model = Collection::toCollections(json_decode($json));
        $this->renderer = $factory->createRendererFor($model);
    }

    protected function whenIRender($markup) {
        $rendered = $this->renderer->render("<div>$markup</div>");
        if (strlen($rendered) <= 11) {
            $this->rendered = '';
        }
        $this->rendered = substr($rendered, 5, -6);
    }

    protected function thenTheResultShouldBe($expected) {
        $this->assertEquals($this->clean($expected), $this->clean($this->rendered));
    }

    protected function clean($string) {
        return trim(preg_replace('/\s*(\S.+\S)\s*/', '$1', $string));
    }
}
