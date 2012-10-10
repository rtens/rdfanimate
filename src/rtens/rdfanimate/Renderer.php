<?php
namespace rtens\rdfanimate;

abstract class Renderer {

    private $model;

    /**
     * @var RendererFactory
     */
    private $factory;

    /**
     * @param string $template
     * @return string
     */
    abstract public function render($template);

    function __construct(RendererFactory $factory, $model) {
        $this->factory = $factory;
        $this->model = $model;
    }

    protected function getModel() {
        return $this->model;
    }

    protected function setModel($model) {
        $this->model = $model;
    }

    /**
     * @return \rtens\rdfanimate\RendererFactory
     */
    protected function getFactory() {
        return $this->factory;
    }

    protected function hasModelProperty($name) {
        return is_object($this->model) && property_exists($this->model, $name);
    }

    protected function getModelProperty($name) {
        return $this->hasModelProperty($name) ? $this->model->$name : null;
    }
}
