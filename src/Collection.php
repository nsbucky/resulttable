<?php namespace ResultTable;

use Illuminate\Support\Collection as SupportCollection;

class Collection extends Table {

    /**
     * @var bool
     */
    public $showFilterArea = false;

    /**
     * @var string
     */
    public $tableWrapper = '{table}';

    /**
     * @var bool
     */
    public $sorting = false;

    /**
     * @param $collection
     * @param array $config
     * @param array $excludeQueryString
     */
    public function __construct( $collection, array $config = [], array $excludeQueryString = ['page'] )
    {
        if( is_array( $collection) ) {
            $collection = new SupportCollection( $collection);
        }

        $this->paginator = $collection;

        $this->input = \Input::except( $excludeQueryString );

        $this->setConfigOptions( $config );

        if( ! isset( $this->baseUrl ) ) {
            $this->baseUrl = \Request::getPathInfo();
        }
    }

    /**
     * @param null $view
     * @return null
     */
    public function getPageLinks( $view = null)
    {
        return null;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return count($this->paginator);
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return 0;
    }
}