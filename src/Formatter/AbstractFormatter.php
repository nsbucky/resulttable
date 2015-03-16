<?php namespace ResultTable\Formatter;

abstract class AbstractFormatter {

    protected $value;

    public function __construct( $options )
    {
        $this->options = $this->parseOptions( $options );
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param null $value
     */
    public function setValue( $value )
    {
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions( $options )
    {
        if( is_array($options)) {
            $this->options = $options;
        }

        $this->options = $this->parseOptions( $options );
    }

    /**
     * @return string
     */
    abstract public function format( );

    /**
     * @param $value
     * @return array
     */
    public function parseOptions( $value )
    {
        if( strpos( $value, '|') === false ) {
            return [];
        }

        $options = explode('|', $value);

        // remove the name:image bits
        array_shift( $options );

        $output = [];
        foreach( $options as $option ) {
            list($key, $value) = explode(':', $option);
            $output[ $key ] = $value;
        }

        return $output;
    }
}