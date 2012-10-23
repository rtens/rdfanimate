<?php
namespace spec\rtens\rdfanimate;

class RepeatingElementsTest extends Test {

    static $CLASSNAME = __CLASS__;

    public function testListLeafsShouldBeRepeated() {
        $this->givenTheModel('{
            "item": [
                "One",
                {   "value": "Two",
                    "title": "Dos"
                }
            ]
        }');
        $this->whenIRender('<span title="" property="item">an item</span>');
        $this->thenTheResultShouldBe('<span title="" property="item">One</span><span title="Dos" property="item">Two</span>');
    }

    public function testListNodesShouldBeRepeated() {
        $this->givenTheModel('{
            "item": [
                {   "name": "Uno",
                    "title": "One"
                },
                {   "name": "Dos",
                    "title": "Two"
                }
            ]
        }');
        $this->whenIRender('
            <div rel="item">
                <span property="title">my title</span>
                <span property="name">my name</span>
            </div>');
        $this->thenTheResultShouldBe('
            <div rel="item">
                <span property="title">One</span>
                <span property="name">Uno</span>
            </div>
            <div rel="item">
                <span property="title">Two</span>
                <span property="name">Dos</span>
            </div>');
    }

    public function testClonesOfListLeafsShouldBeRemoved() {
        $this->givenTheModel('{
            "item": [
                "One",
                "Two"
            ]
        }');
        $this->whenIRender('
            <span property="item">an item</span>
            <span property="item">delete me</span>
            <span property="item">delete me too</span>');
        $this->thenTheResultShouldBe('
            <span property="item">One</span>
            <span property="item">Two</span>');
    }

    public function testClonesOfListNodesShouldBeRemoved() {
        $this->givenTheModel('{
            "item": [
                {   "name": "Uno"
                },
                {   "name": "Dos"
                }
            ]
        }');
        $this->whenIRender('
            <div rel="item">
                <span property="name">my name</span>
            </div>
            <div rel="item">
                <span property="name">delete me</span>
            </div>
            <div rel="item">
                <span property="name">me too</span>
            </div>');
        $this->thenTheResultShouldBe('
            <div rel="item">
                <span property="name">Uno</span>
            </div>
            <div rel="item">
                <span property="name">Dos</span>
            </div>');
    }

    public function testRemoveInnerElementFromItem() {
        $this->givenTheModel('{
            "item": [
                {   "name": "One",
                    "call": true
                },
                {
                    "name": "Two",
                    "call": false
                }
            ]
        }');

        $this->whenIRender('
            <div rel="item">
                <span property="name">Name</span>
                <span property="call">!!</span>
            </div>
        ');
        $this->thenTheResultShouldBe('
            <div rel="item">
                <span property="name">One</span>
                <span property="call">!!</span>
            </div>
            <div rel="item">
                <span property="name">Two</span>
            </div>
        ');
    }
}
