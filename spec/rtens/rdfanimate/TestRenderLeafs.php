<?php
namespace spec\rtens\rdfanimate;

class TestRenderLeafs extends Test {

    static $CLASSNAME = __CLASS__;

    public function testUndefinedLeafShouldStayTheSame() {
        $this->givenTheModel('{}');
        $this->whenIRender('<span property="undefined">Hello World</span>');
        $this->thenTheResultShouldBe('<span property="undefined">Hello World</span>');
    }

    public function testTrueLeafShouldStay() {
        $this->givenTheModel('{"yes":true}');
        $this->whenIRender('<span property="yes">Hello World</span>');
        $this->thenTheResultShouldBe('<span property="yes">Hello World</span>');
    }

    public function testFalseAndNullLeafShouldBeRemoved() {
        $this->givenTheModel('{"no":false, "notThere":null}');

        $this->whenIRender('Hello<span property="no"> All</span><span property="notThere"> World</span>');
        $this->thenTheResultShouldBe('Hello');
    }

    public function testLeafTextShouldBeReplaced() {
        $this->givenTheModel('{"greetings":"Hello", "name":"World"}');

        $this->whenIRender('
            <span property="greetings">Hey</span>
            <span property="name">You</span>');
        $this->thenTheResultShouldBe('
            <span property="greetings">Hello</span>
            <span property="name">World</span>');
    }

    public function testLeafCanBeNested() {
        $this->givenTheModel('{"message":"Hello World"}');
        $this->whenIRender('<div><h1><a property="message">My Message</a></h1></div>');
        $this->thenTheResultShouldBe('<div><h1><a property="message">Hello World</a></h1></div>');
    }

    public function testLeafAttributesShouldBeReplaced() {
        $this->givenTheModel('{"image": {"src":"http://example.com", "alt":"Test"}}');
        $this->whenIRender('<img property="image" src="" alt="nothing"/>');
        $this->thenTheResultShouldBe('<img property="image" src="http://example.com" alt="Test"/>');
    }

    public function testFalseAndNullLeafAttributesShouldBeRemoved() {
        $this->givenTheModel('{"name": {"title":false, "class":null}}');
        $this->whenIRender('<div property="name" title="test" class="nothing"/>');
        $this->thenTheResultShouldBe('<div property="name"/>');
    }

    public function testPropertiesShouldAccessInnerFields() {
        $this->givenTheModel('{
            "item": {
                "title": "Hello"
            }
        }');
        $this->whenIRender('<span property="item" title=""/>');
        $this->thenTheResultShouldBe('<span property="item" title="Hello"/>');
    }

    public function testValuePropertyShouldBecomeContent() {
        $this->givenTheModel('{"email":{"href":"mailto:test@example.com", "value":"test"}}');
        $this->whenIRender('<a property="email" href=""></a>');
        $this->thenTheResultShouldBe('<a property="email" href="mailto:test@example.com">test</a>');
    }

    public function testAccessInnerFieldsOfLeaf() {
        $this->givenTheModel('{
            "stock" : [
                { "quantity": { "value": "1", "isMany": false }, "color": "blue" },
                { "quantity": { "value": "2", "isMany": true }, "color": "red" }
            ]}');
        $this->whenIRender('
            <div rel="stock">
                <span property="quantity">#</span>
                <span property="color">colorful</span>
                car<span rel="quantity" property="isMany">s</span>
            </div>');
        $this->thenTheResultShouldBe('
            <div rel="stock">
                <span property="quantity">1</span>
                <span property="color">blue</span>
                car
            </div>
            <div rel="stock">
                <span property="quantity">2</span>
                <span property="color">red</span>
                car<span rel="quantity" property="isMany">s</span>
            </div>');
    }

}
