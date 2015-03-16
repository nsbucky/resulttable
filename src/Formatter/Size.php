<?php namespace ResultTable\Formatter;

class Size extends AbstractFormatter {

    /**
     * @return string
     */
    public function format()
    {
        return format_file_size( $this->getValue() );
    }

}