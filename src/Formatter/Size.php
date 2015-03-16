<?php namespace ResultTable\Formatter;

class Size extends AbstractFormatter {

    /**
     * @return string
     */
    public function format()
    {
        return $this->format_file_size( $this->getValue() );
    }

    /**
     * for given $bytes, return a human readable representation
     * @param $bytes
     * @return string
     */
    public function format_file_size( $bytes )
    {
        if ($bytes >= 1073741824)
            return number_format($bytes / 1073741824, 2) . ' GB';

        if ($bytes >= 1048576)
            return number_format($bytes / 1048576, 2) . ' MB';

        if ($bytes >= 1024)
            return number_format($bytes / 1024, 2) . ' KB';

        if ($bytes > 1)
            return  $bytes . ' bytes';

        if ($bytes == 1)
            return $bytes . ' byte';

        return '0 bytes';
    }

}