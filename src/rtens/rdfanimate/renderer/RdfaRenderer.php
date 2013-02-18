<?php
namespace rtens\rdfanimate\renderer;

use rtens\rdfanimate\Renderer;
use rtens\rdfanimate\Element;

abstract class RdfaRenderer extends Renderer {

    /**
     * @param string $template
     * @return string
     */
    public function render($template) {
        return $this->manipulate(Element::fromString($template))->__toString();
    }

    /**
     * @param Element $element
     * @return Element
     */
    abstract protected function manipulate(Element $element);

    /**
     * @return RdfaRendererFactory
     */
    protected function getFactory() {
        return parent::getFactory();
    }
}
