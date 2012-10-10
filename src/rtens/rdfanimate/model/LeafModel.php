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
        if (is_object($this->model)) {
            $valuePropertyName = 'value';
            return property_exists($this->model, $valuePropertyName) ? (string)$this->model->$valuePropertyName : null;
        } else {
            return (string)$this->model;
        }
    }

    public function hasProperty($name) {
        return $this->isObject() && property_exists($this->model, $name);
    }

    public function isObject() {
        return is_object($this->model);
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
