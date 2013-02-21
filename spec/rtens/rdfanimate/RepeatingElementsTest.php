<?php
namespace spec\rtens\rdfanimate;

use rtens\collections\Liste;
use rtens\collections\Map;

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

    public function testEmptyListShouldBeCleared() {
        $this->givenTheModelObject(new Map(array('item' => new Liste())));

        $this->whenIRender('
            <ul>
                <li rel="item">
                    <span property="name">Name</span>
                    <span property="call">!!</span>
                </li>
            </ul>
        ');
        $this->thenTheResultShouldBe('
            <ul></ul>
        ');
    }

    public function testListOfLists() {
        $this->givenTheModel('{
            "week": [
                {
                    "count" : 1,
                    "day": [
                        { "number": 1 },
                        { "number": 2 },
                        { "number": 3 }
                    ]
                },
                {
                    "count" : 2,
                    "day": [
                        { "number": 4 },
                        { "number": 5 },
                        { "number": 6 }
                    ]
                }
            ]
        }');

        $this->whenIRender('
            <div rel="week">
                <div property="count">count</div>
                <div rel="day">
                    <span property="number">#</span>
                </div>
            </div>
            <div rel="week">
                <div rel="day">
                    <span property="number">should be deleted</span>
                </div>
            </div>
        ');
        $this->thenTheResultShouldBe('
            <div rel="week">
                <div property="count">1</div>
                <div rel="day">
                    <span property="number">1</span>
                </div>
                <div rel="day">
                    <span property="number">2</span>
                </div>
                <div rel="day">
                    <span property="number">3</span>
                </div>
            </div>
            <div rel="week">
                <div property="count">2</div>
                <div rel="day">
                    <span property="number">4</span>
                </div>
                <div rel="day">
                    <span property="number">5</span>
                </div>
                <div rel="day">
                    <span property="number">6</span>
                </div>
            </div>
        ');
    }
}
