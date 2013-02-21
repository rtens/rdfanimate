<?php
namespace rtens\rdfanimate\renderer;

use rtens\rdfanimate\Element;
use rtens\rdfanimate\model\LeafModel;
use rtens\collections\Liste;

class LeafRenderer extends NodeRenderer {

    public static $CLASSNAME = __CLASS__;

    /**
     * @param Element $element
     * @return Element
     */
    public function manipulate(Element $element) {
        if ($this->getModel()->isNullOrFalse()) {
            $element->remove();
        } else if ($this->isList($this->getModel()->getModel())) {
            $this->manipulateWithList($element);
        } else if ($this->getModel()->isObject()) {
            $this->manipulateAttributes($element);
        }

        if (!$this->getModel()->isTrue() && strlen($this->getModel()->getText())) {
            $element->setContent($this->getModel()->getText());
        }

        $this->setModel($this->getModel()->getNodeModel());
        $this->manipulateChildren($element);

        return $element;
    }

    /**
     * @return \rtens\collections\Liste
     */
    protected function getItemsModels() {
        $itemModels = new Liste();
        foreach ($this->getModel()->getModel() as $item) {
            $itemModels->append(new LeafModel($this->getModel()->getNodeModel(), $item));
        }
        return $itemModels;
    }

    protected function hasSamePropertyName(Element $sibling, Element $element) {
        return $this->hasLeafPropertyName($sibling) && $this->hasLeafPropertyName($element)
            && $this->getLeafPropertyName($sibling) == $this->getLeafPropertyName($element);
    }

    private function manipulateAttributes(Element $element) {
        foreach ($element->getAttributes() as $name => $value) {
            if ($this->getModel()->hasProperty($name)) {
                $property = $this->getModel()->getProperty($name);
                if ($property === null || $property === false) {
                    $element->getAttributes()->remove($name);
                } else {
                    $element->getAttributes()->set($name, $property);
                }
            }
        }
    }

    /**
     * @return LeafModel
     */
    protected function getModel() {
        return parent::getModel();
    }

}
