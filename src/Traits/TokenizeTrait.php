<?php namespace ResultTable\Traits;

trait TokenizeTrait {

    /**
     * @var array
     */
    protected $tokens = [];

    /**
     * create an array of tokens to be used in various column functions. if the
     * data pass is an object, it will first see if the object has a method called
     * isArray() to create an array from the object, otherwise it will cast the object
     * to an array and hope for the best
     * @param $data
     */
    protected function tokenize( $data )
    {
        if(is_object($data) && method_exists($data, 'toArray')) {
            $data = $data->toArray();
        }

        foreach((array) $data as $key => $value) {
            if( is_array($value) || is_object($value) ) continue;
            $this->tokens['{'.$key.'}'] = (string) $value;
        }
    }

    /**
     * replace any tokens found in string with tokens
     * @param string $string
     * @return string
     */
    public function replaceTokens($string)
    {
        if (strpos($string, '{') !== false) {
            $tokens = $this->getTokens();
            return str_replace(array_keys($tokens), array_values($tokens), (string) $string);
        }

        return $string;
    }

    /**
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * @param array $tokens
     * @return $this;
     */
    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
        return $this;
    }
}