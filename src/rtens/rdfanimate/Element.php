<?php
namespace rtens\rdfanimate;

use rtens\rdfanimate\exception\ParsingException;
use rtens\collections\events\MapSetEvent;
use rtens\collections\events\MapRemoveEvent;
use rtens\collections\Map;
use rtens\collections\Liste;

class Element {

    private static $tagPairs = array(
        '<html><body>' => array(),
        '<body>' => array('<html>', '</html>'),
        '<html>' => array('<body>', '</body>'),
        '' => array('<html><body>', '</body></html>'),
    );

    /**
     * @var Liste|null
     */
    public $children;

    /**
     * @var string Original input string (without white spaces between tags)
     */
    private $input;

    private $element;

    /**
     * @var null|Map
     */
    private $attributes;

    /**
     * @var Element|null
     */
    private $parent;

    /**
     * @var Element|null
     */
    private $nextSibling;

    public static function fromString($document) {
        $doc = new \DOMDocument();
        $document = mb_convert_encoding($document, 'HTML-ENTITIES', 'UTF-8');

        if (!$doc->loadHTML($document)) {
            throw new ParsingException('Error while parsing mark-up.');
        }

        $element = new Element($doc->documentElement);
        $element->input = trim(preg_replace('/>\s+?</', '><', $document));
        return $element;
    }

    private function __construct(\DOMNode $element, Element $parent = null) {
        $this->element = $element;
        $this->parent = $parent;
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
        $this->parent->insertBefore($child, $this);
    }

    private function insertBefore(Element $insert, Element $child) {
        if ($this->children) {
            $childIndex = $this->getChildren()->indexOf($child);
            if ($childIndex > 0) {
                $this->getChildren()->get($childIndex - 1)->nextSibling = $insert;
            }
            $insert->nextSibling = $child;
            $this->getChildren()->insert($insert, $childIndex);
        }
        $this->element->insertBefore($insert->element, $child->element);
    }

    public function remove() {
        $this->parent->removeChild($this);
    }

    private function removeChild(Element $child) {
        if ($this->children) {
            $childIndex = $this->getChildren()->indexOf($child);
            if ($childIndex > 0) {
                if ($childIndex == $this->getChildren()->count() - 1) {
                    $this->getChildren()->get($childIndex - 1)->nextSibling = null;
                } else {
                    $this->getChildren()->get($childIndex - 1)->nextSibling = $this->getChildren()->get($childIndex + 1);
                }
            }
            $this->getChildren()->remove($childIndex);
        }
        $child->parent = null;
        $this->element->removeChild($child->element);
    }

    public function getNextSibling() {
        return $this->nextSibling;
    }

    /**
     * @return \rtens\collections\Liste|Element[]
     */
    public function getChildren() {
        if (!$this->children) {
            $this->children = new Liste();
            /** @var $lastChild Element|null */
            $lastChild = null;
            foreach ($this->element->childNodes as $child) {
                if ($child instanceof \DOMElement) {
                    $nextChild = new Element($child, $this);
                    $this->children->append($nextChild);

                    if ($lastChild) {
                        $lastChild->nextSibling = $nextChild;
                    }
                    $lastChild = $nextChild;
                }
            }
        }
        return $this->children;
    }

    public function setContent($content) {
        foreach ($this->getChildren() as $child) {
            $this->removeChild($child);
        }

        foreach ($this->element->childNodes as $child) {
            $this->element->removeChild($child);
        }

        $this->element->appendChild(new \DOMText($content));
    }

    public function __toString() {
        $doc = $this->element->ownerDocument;
        $doc->formatOutput = true;
        $content = $doc->saveHTML($this->element);

        foreach (self::$tagPairs as $match => $replace) {
            if (substr($this->input, 0, strlen($match)) == $match) {
                $content = str_replace($replace, '', $content);
                break;
            }
        }

        return $content;
    }

    public function copy() {
        /** @var $clone \DOMElement */
        $clone = $this->element->cloneNode(true);
        return new Element($clone, $this->parent);
    }

    public function getParent() {
        return $this->parent;
    }
}
