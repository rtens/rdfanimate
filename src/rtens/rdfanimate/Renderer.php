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
        return $this->hasStaticModelProperty($name) || $this->hasDynamicModelProperty($name);
    }

    protected function getModelProperty($name) {
        if ($this->hasStaticModelProperty($name)) {
            return $this->getStaticModelProperty($name);
        } else if ($this->hasDynamicModelProperty($name)) {
            return $this->getDynamicModelProperty($name);
        } else {
            return null;
        }
    }

    private function hasStaticModelProperty($name) {
        return is_object($this->model) && property_exists($this->model, $name);
    }

    private function getStaticModelProperty($name) {
        return $this->model->$name;
    }

    private function hasDynamicModelProperty($name) {
        return is_object($this->model) && method_exists($this->model, $name);
    }

    private function getDynamicModelProperty($name) {
        return $this->model->{$name}();
    }
}
