<?php namespace ResultTable\Traits;

trait ConfigTrait {

    public function setConfigOptions( array $options = [] )
    {
        $publicProperties = call_user_func('get_object_vars', $this);

        foreach( $options as $key => $value ) {
            if( array_key_exists( $key, $publicProperties ) ) {
                $this->$key = $value;
            }
        }
    }

}