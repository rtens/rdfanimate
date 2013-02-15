<?php
namespace spec\rtens\rdfanimate;

class DynamicValuesTest extends Test {

    public function testMethodCall() {
        $this->givenTheClass('class IntegerModel {
            public $value;
            public function __construct($int) {
                $this->value = $int;
            }
            public function isBig() {
                return $this->value > 3;
            }
            public function isNotBig() {
                return !$this->isBig();
            }
        }');

        $this->givenTheClass('class MethodModel {
            public $number;
            public function __construct($number) {
                $this->number = new IntegerModel($number);
            }
        }');

        $this->givenTheModelIsInstanceOfClass('MethodModel', 2);

        $this->whenIRender('
            <div rel="number">
                <span property="value">X</span> is a <span property="isBig">BIG</span><span property="isNotBig">small</span> number
            </div>
        ');

        $this->thenTheResultShouldBe('
            <div rel="number">
                <span property="value">2</span> is a <span property="isNotBig">small</span> number
            </div>
        ');
    }

    public function testClosureCall() {
        $this->givenTheClass('class StringModel {
            public $value;
            public $shorten;
            public function __construct($string) {
                $this->value = $string;
                $this->shorten = function (StringModel $model) {
                    return substr($model->value, 0, 4);
                };
            }
        }');

        $this->givenTheModelIsInstanceOfClass('StringModel', 'SomeLongString');

        $this->whenIRender('<div property="shorten">Shorten</div>');

        $this->thenTheResultShouldBe('<div property="shorten">Some</div>');
    }

    private function givenTheClass($def) {
        eval($def);
    }

    private function givenTheModelIsInstanceOfClass($classname) {
        $params = func_get_args();
        array_shift($params);

        $refl = new \ReflectionClass($classname);
        $model = $refl->newInstanceArgs($params);
        $this->givenTheModelObject($model);
    }

}