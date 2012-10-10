<?php
namespace rtens\rdfanimate;

class RdfanimateTestSuite extends \PHPUnit_Framework_TestSuite {

    public static function suite() {
        $suite = new RdfanimateTestSuite();
        $suite->addTestSuite(TestRenderLeafs::$CLASSNAME);
        $suite->addTestSuite(TestRenderNodes::$CLASSNAME);
        $suite->addTestSuite(TestRenderLists::$CLASSNAME);
        return $suite;
    }

}
