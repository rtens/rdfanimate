<?php
namespace rtens\rdfanimate;

interface RendererFactory {

    const CLASSNAME = __CLASS__;

    /**
     * @abstract
     * @param $model
     * @return Renderer
     */
    public function createRendererFor($model);

}
