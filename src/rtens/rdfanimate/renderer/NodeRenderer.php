<?php
namespace rtens\rdfanimate\renderer;

use rtens\rdfanimate\Element;
use rtens\rdfanimate\model\LeafModel;
use rtens\collections\Liste;

class NodeRenderer extends RdfaRenderer {

    public static $CLASSNAME = __CLASS__;

    /**
     * @param Element $element
     * @return Element
     */
    public function manipulate(Element $element) {
        if ($this->isLeaf($element)) {
            $leafModel = $this->createLeafModel($element);
            $this->getFactory()->createRendererFor($leafModel)->manipulate($element);
        } else if ($this->getModel() === null || $this->getModel() === false) {
            $element->remove();
        } else if ($this->isList($this->getModel())) {
            $this->manipulateWithList($element);
        }

        $this->manipulateChildren($element);
        return $element;
    }

    protected function manipulateWithList(Element $element) {
        $this->insertCopiesOfElement($element);

        $toBeRemoved = $this->findDuplicateSiblings($element);

        $toBeRemoved->insert($element, 0);
        foreach ($toBeRemoved as $removeElement) {
            $removeElement->remove();
        }
    }

    protected function manipulateChildren(Element $element) {
        foreach ($element->getChildren() as $child) {
            if (!$child->getParent()) {
                continue;
            }

            if ($this->isLeaf($child)) {
                $model = $this->createLeafModel($child);
            } else if ($this->isNode($child)) {
                $model = $this->getModelProperty($this->getNodePropertyName($child));
            } else {
                $model = $this->getModel();
            }

            $renderer = $this->getFactory()->createRendererFor($model);
            $renderer->manipulate($child);
        }
    }

    private function insertCopiesOfElement(Element $element) {
        foreach ($this->getItemsModels() as $itemModel) {
            $copy = $element->copy();
            $element->insertSibling($copy);

            $renderer = $this->getFactory()->createRendererFor($itemModel);
            $renderer->manipulate($copy);
        }
    }

    /**
     * @param \rtens\rdfanimate\Element $element
     * @return \rtens\collections\Liste|Element[]
     */
    private function findDuplicateSiblings(Element $element) {
        $toBeRemoved = new Liste();
        $sibling = $element->getNextSibling();

        while ($sibling) {
            if ($this->hasSamePropertyName($sibling, $element)) {
                $toBeRemoved->append($sibling);
            }
            $sibling = $sibling->getNextSibling();
        }

        return $toBeRemoved;
    }

    /**
     * @return Liste
     */
    protected function getItemsModels() {
        return $this->getModel();
    }

    private function createLeafModel($child) {
        return new LeafModel($this->getModel(), $this->getModelProperty($this->getLeafPropertyName($child)));
    }

    protected function hasSamePropertyName(Element $sibling, Element $element) {
        return $this->hasNodePropertyName($sibling) && $this->hasNodePropertyName($element)
            && $this->getNodePropertyName($sibling) == $this->getNodePropertyName($element);
    }

    protected function isLeaf(Element $element) {
        return $this->hasLeafPropertyName($element)
            && $this->hasModelProperty($this->getLeafPropertyName($element));
    }

    protected function getLeafPropertyName(Element $element) {
        return $element->getAttributes()->get('property');
    }

    protected function hasLeafPropertyName(Element $element) {
        return $element->getAttributes()->has('property');
    }

    private function isNode(Element $element) {
        return $this->hasNodePropertyName($element)
            && $this->hasModelProperty($this->getNodePropertyName($element));
    }

    private function getNodePropertyName(Element $element) {
        return $element->getAttributes()->get('rel');
    }

    protected function hasNodePropertyName(Element $element) {
        return $element->getAttributes()->has('rel');
    }

    protected function isList($object) {
        return $object instanceof Liste;
    }
}
