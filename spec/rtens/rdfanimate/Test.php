<?php
namespace spec\rtens\rdfanimate;

use rtens\collections\Collection;
use rtens\rdfanimate\renderer\RdfaRenderer;
use rtens\rdfanimate\renderer\RdfaRendererFactory;

abstract class Test extends \PHPUnit_Framework_TestCase {

    /**
     * @var string
     */
    private $renderedFromMaps;

    /**
     * @var string
     */
    private $rendered;

    /**
     * @var \rtens\rdfanimate\renderer\RdfaRenderer
     */
    protected $renderer;

    /**
     * @var \rtens\rdfanimate\renderer\RdfaRenderer
     */
    public $rendererMaps;

    protected function background() {
    }

    protected function setUp() {
        parent::setUp();

        $this->background();
    }

    protected function givenTheModel($json) {
        $model = Collection::toCollections(json_decode($json));
        $this->renderer = $this->createRenderer($model);

        $decoded = json_decode($json, true);
        $modelMaps = Collection::toCollections($decoded);
        $this->rendererMaps = $this->createRenderer($modelMaps);
    }

    protected function createRenderer($model) {
        $factory = new RdfaRendererFactory();
        return $factory->createRendererFor($model);
    }

    protected function whenIRender($markup) {
        $this->rendered = $this->render($markup, $this->renderer);

        if ($this->rendererMaps) {
            $this->renderedFromMaps = $this->render($markup, $this->rendererMaps);
        }
    }

    private function render($markup, RdfaRenderer $renderer) {
        $rendered = $renderer->render("<html><body>$markup</body></html>");
        return substr($rendered, 15, -15);
    }

    protected function thenTheResultShouldBe($expected) {
        $this->assertEquals($this->clean($expected), $this->clean($this->rendered));

        if ($this->rendererMaps) {
            $this->assertEquals($this->clean($expected), $this->clean($this->renderedFromMaps));
        }
    }

    protected function clean($string) {
        return trim(preg_replace('/\s*(\S.+\S)\s*/', '$1', $string));
    }
}
