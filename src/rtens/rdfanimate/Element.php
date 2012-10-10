<?php
namespace rtens\rdfanimate;

use rtens\rdfanimate\exception\ParsingException;
use rtens\collections\events\MapSetEvent;
use rtens\collections\events\MapRemoveEvent;
use rtens\collections\Map;
use rtens\collections\Liste;

class Element {

    private $element;

    /**
     * @var null|Map
     */
    private $attributes;

    public static function fromString($document) {
        $doc = new \DOMDocument();
        if (!$doc->loadXML($document)) {
            throw new ParsingException('Error while parsing mark-up.');
        }

        return new Element($doc->documentElement);
    }

    private function __construct(\DOMElement $element) {
        $this->element = $element;
    }

    /**
     * @return \rtens\collections\Map
     */
    public function getAttributes() {
        if (!$this->attributes) {
            $this->attributes = new Map();

            foreach ($this->element->attributes as $name => $attr) {
                $this->attributes->set($name, $attr->value);
            }

            $this->setAttributesListeners($this->attributes, $this->element);
        }
        return $this->attributes;
    }

    private function setAttributesListeners(Map $attributes, \DOMElement $node) {
        $attributes->on(MapSetEvent::$CLASSNAME, function (MapSetEvent $event) use ($node) {
            /** @var $node \DOMElement */
            $node->setAttribute($event->getKey(), $event->getValue());
        });

        $attributes->on(MapRemoveEvent::$CLASSNAME, function (MapRemoveEvent $event) use ($node) {
            /** @var $node \DOMElement */
            $node->removeAttribute($event->getKey());
        });
    }

    public function insertSibling(Element $child) {
        $this->element->parentNode->insertBefore($child->element, $this->element);
    }

    public function remove() {
        $this->element->parentNode->removeChild($this->element);
    }

    public function getNextSibling() {
        $sibling = $this->element->nextSibling;
        while ($sibling) {
            if ($sibling instanceof \DOMElement) {
                return new Element($sibling);
            }
            $sibling = $sibling->nextSibling;
        }
        return null;
    }

    /**
     * @return \rtens\collections\Liste|Element[]
     */
    public function getChildren() {
        $children = new Liste();
        foreach ($this->element->childNodes as $child) {
            if ($child instanceof \DOMElement) {
                $children->append(new Element($child));
            }
        }
        return $children;
    }

    public function setContent($content) {
        foreach ($this->element->childNodes as $child) {
            $this->element->removeChild($child);
        }
        $this->element->appendChild(new \DOMText($content));
    }

    public function __toString() {
        $doc = $this->element->ownerDocument;
        $doc->formatOutput = true;
        return $doc->saveXML($this->element);
    }

    public function copy() {
        /** @var $clone \DOMElement */
        $clone = $this->element->cloneNode(true);
        return new Element($clone);
    }

    public function getParent() {
        if ($this->element->parentNode) {
            return new Element($this->element->parentNode);
        } else {
            return null;
        }
    }
}
