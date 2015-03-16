<?php namespace ResultTable\Columns;

use ResultTable\Traits\LinkTrait;

class Link extends AbstractColumn {

    use LinkTrait;

    /**
     * @var string
     */
    public $linkCss = '';

    /**
     * @var string
     */
    public $css = 'grid-view-link-column';

    /**
     * @return string
     */
    function getValue()
    {
        return sprintf('<a href="%s" class="%s">%s</a>', $this->getUrl(), $this->linkCss, $this->getLabel());
    }

    /**
     * @return string
     */
    public function getLinkCss()
    {
        return $this->linkCss;
    }

    /**
     * @param string $linkCss
     */
    public function setLinkCss( $linkCss )
    {
        $this->linkCss = $linkCss;
    }

}