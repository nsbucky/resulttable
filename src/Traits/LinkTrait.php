<?php namespace ResultTable\Traits;

trait LinkTrait {

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $label;

    /**
     * @return string
     */
    public function getUrl()
    {
        $url = $this->url;

        if(is_callable($this->url)) {
            $func = $this->url;
            $url = $func( $this->getData() );
        }

        return $this->replaceTokens($url);
    }

    /**
     * @param mixed $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        $label = $this->label;

        if(is_callable($this->label)) {
            $func = $this->label;
            $label = $func( $this->getData() );
        }

        return $this->replaceTokens($label);
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }
}