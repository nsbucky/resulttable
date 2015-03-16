<?php namespace ResultTable\Formatter;

class Url extends AbstractFormatter {

    /**
     * @return string
     */
    public function format()
    {
        $value = e($this->getValue());
        return sprintf('<a href="%s">%s</a>', $value, $value);
    }

}