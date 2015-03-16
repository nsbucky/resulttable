<?php namespace ResultTable\Formatter;

class Email extends AbstractFormatter {

    /**
     * @return string
     */
    public function format()
    {
        $subject = e( array_get( $this->options, 'subject') );

        $value = e($this->getValue());

        return sprintf('<a href="mailto:%s?subject=%s">%s</a>', $value, $subject, $value);
    }

}