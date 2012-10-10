<?php
namespace rtens\rdfanimate\renderer;

use rtens\rdfanimate\RendererFactory;
use rtens\rdfanimate\model\LeafModel;

class RdfaRendererFactory implements RendererFactory {

    public static $CLASSNAME = __CLASS__;

    /**
     * @param $model
     * @return \rtens\rdfanimate\Renderer|RdfaRenderer
     */
    public function createRendererFor($model) {
        if ($model instanceof LeafModel) {
            return new LeafRenderer($this, $model);
        } else {
            return new NodeRenderer($this, $model);
        }
    }
}
