<?php namespace ResultTable\Formatter;

class Image extends AbstractFormatter {

    /**
     * @return string
     */
    public function format()
    {
        $width   = array_get( $this->options, 'width');
        $height  =  array_get( $this->options, 'height');

        return sprintf('<img src="%s" width="%s" height="%s">', $this->getValue(), $width, $height);
    }

}