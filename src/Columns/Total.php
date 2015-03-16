<?php namespace ResultTable\Columns;

class Total extends AbstractColumn {

    /**
     * @var int
     */
    protected $total = 0;

    /**
     * sprintf compatible format. defaults to %d for digits.
     * @var string
     */
    public $format = '%d';

    /**
     * get the value to be used in the table cell. if the value set is callable
     * pass data to the callable function and get the value from it
     * @return string
     * @throws \RuntimeException if name or value are not set for column
     */
    public function getValue()
    {
        $value = $this->fetchValueFromData();

        if( is_numeric( $value) ) {
            $this->total += $value;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getFooter()
    {
        return sprintf( $this->format, $this->total );
    }
}