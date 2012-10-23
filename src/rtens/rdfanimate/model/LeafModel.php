<?php
namespace rtens\rdfanimate\model;

class LeafModel {

    public static $CLASSNAME = __CLASS__;

    private $model;

    private $nodeModel;

    function __construct($nodeModel, $model) {
        $this->nodeModel = $nodeModel;
        $this->model = $model;
    }

    public function getModel() {
        return $this->model;
    }

    public function getNodeModel() {
        return $this->nodeModel;
    }

    public function getText() {
        if ($this->isObject()) {
            $valuePropertyName = 'value';
            return property_exists($this->model, $valuePropertyName) ? (string)$this->model->$valuePropertyName : null;
        } else if ($this->isCallable()) {
            $callable = $this->model;
            return $callable($this->nodeModel);
        } else {
            return (string)$this->model;
        }
    }

    public function hasProperty($name) {
        return $this->isObject() && property_exists($this->model, $name);
    }

    public function isObject() {
        return is_object($this->model) && !$this->isCallable();
    }

    private function isCallable() {
        return is_callable($this->model);
    }

    public function getProperty($name) {
        return $this->model->$name;
    }

    public function isNullOrFalse() {
        return $this->model === null || $this->model == false;
    }

    public function isTrue() {
        return $this->model === true;
    }

}
