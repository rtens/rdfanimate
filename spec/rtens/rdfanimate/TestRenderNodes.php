<?php
namespace spec\rtens\rdfanimate;

class TestRenderNodes extends Test {

    static $CLASSNAME = __CLASS__;

    public function testNodesShouldChangeContextForLeafs() {
        $this->givenTheModel('{
            "post": {
                "text": "Hello World!",
                "author": {
                    "name": "Timmy Tester",
                    "image": {
                        "alt": "Timmys Image",
                        "src": "http://example.com/image.jpg"
                    },
                    "email": {
                        "href": "mailto:timmy@tester.com"
                    }
                }
            }
        }');
        $this->whenIRender('
            <div rel="post">
                <div rel="author">
                    <img property="image" src="" alt="Nothing"/>
                    <a property="email" href="mailto:john.doe@example.com">
                        <span property="name">John Doe</span>
                    </a>
                </div>
                <span property="text">Some Text</span>
            </div>');
        $this->thenTheResultShouldBe('
            <div rel="post">
                <div rel="author">
                    <img property="image" src="http://example.com/image.jpg" alt="Timmys Image"/>
                    <a property="email" href="mailto:timmy@tester.com">
                        <span property="name">Timmy Tester</span>
                    </a>
                </div>
                <span property="text">Hello World!</span>
            </div>');
    }

    public function testIgnoreNodeWithUndefinedModel() {
        $this->givenTheModel('{"nonObject":1}');
        $this->whenIRender('
            <div rel="nonObject"><span rel="ignore"><span property="me">Nothing</span></span></div>
            <div property="nonObject">One</div>');
        $this->thenTheResultShouldBe('
            <div rel="nonObject"><span rel="ignore"><span property="me">Nothing</span></span></div>
            <div property="nonObject">1</div>');
    }

    public function testFalseOrNullNodesShouldBeRemoved() {
        $this->givenTheModel('{
            "outer": {
                "no": false,
                "empty": null,
                "yes": true
            }
        }');
        $this->whenIRender('
            <div rel="outer">
                <span rel="no"><span property="test">No</span></span>
                <span rel="empty"><span property="test">Empty</span></span>
                <span rel="yes"><span property="test">Yes</span></span>
            </div>');
        $this->thenTheResultShouldBe('
            <div rel="outer">
                <span rel="yes"><span property="test">Yes</span></span>
            </div>');
    }

    public function testNodeAndLeafInSameElement() {
        $this->givenTheModel('{
            "outer": {
                "inner": "World"
            }
        }');
        $this->whenIRender('<span rel="outer" property="inner"/>');
        $this->thenTheResultShouldBe('<span rel="outer" property="inner">World</span>');
    }

}
