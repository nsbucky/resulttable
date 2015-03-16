<?php namespace ResultTable\Columns;

use ResultTable\Traits\ConfigTrait;
use ResultTable\Table;
use ResultTable\Traits\TokenizeTrait;

abstract class AbstractColumn {

    const SORT_ASCENDING  = 'asc';
    const SORT_DESCENDING = 'desc';

    use ConfigTrait;
    use TokenizeTrait;

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $visible = true;

    /**
     * @var bool
     */
    public $sortable = true;

    /**
     * @var string
     */
    public $sortDirection = 'ASC';

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var string
     */
    public $value;

    /**
     * @var string|array
     */
    public $filter;

    /**
     * @var string
     */
    public $css = 'grid-view-column';

    /**
     * @var string
     */
    public $sortableName;

    /**
     * escape data in table cell or not. defaults to escape data.
     * @var bool
     */
    public $raw = false;

    /**
     * @var string
     */
    public $header;

    /**
     * @var string
     */
    public $javascript;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var string|array
     */
    public $filterName;

    /**
     * @var \ResultTable\Formatter\AbstractFormatter
     */
    protected $formatter;

    /**
     * @param Table $table
     * @param $config
     */
    public function __construct( Table $table, $config )
    {
        $this->table = $table;

        if( is_scalar( $config ) ) {
            $this->setNameFromString( $config );
            $this->detectFormatting( $config );
            return;
        }

        $this->setConfigOptions( $config );

        if( ! isset( $this->sortableName ) ) {
            $this->sortableName = $this->name;
        }
    }

    /**
     * @param $name
     */
    protected function setNameFromString( $name )
    {
        $this->name = $name;

        if( strpos( $name, ':') !== false ) {
            $this->name = substr( $name, 0, strpos( $name, ':') );
        }

        if( ! isset( $this->sortableName ) ) {
            $this->sortableName = $this->name;
        }
    }

    /**
     * @param $name
     */
    protected function detectFormatting( $name )
    {
        if( strpos( $name, ':' ) === false ) {
            return ;
        }

        $formatOptions = substr( $name, strpos( $name, ':') + 1 );

        // detect if the value now required formatting
        // formatting should be like this:
        // img.jpg:image|width:45|height:34
        foreach( ['image','url','size','email'] as $formatter ) {
            if( stripos( $name, ':'.$formatter) !== false ) {
                $formatClass = "\\ResultTable\\Formatter\\".ucfirst( $formatter );
                $this->formatter = new $formatClass( $formatOptions );
                break;;
            }
        }
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData( $data )
    {
        $this->data = $data;
        $this->tokenize( $data );
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    abstract function getValue();

    /**
     * @return string
     */
    public function getHeader()
    {
        $headerName = $this->getHeaderName();

        if( $this->isSortable() ) {
            $headerName = $this->createSortableLink( $headerName );
        }

        return $headerName;
    }

    /**
     * @return string
     */
    public function getFooter()
    {
        return '';
    }

    /**
     * @param $label
     * @return string
     */
    protected function createSortableLink( $label )
    {
        $direction        = $this->getNextSortDirection();
        $currentDirection = $this->getCurrentSortDirection();
        $limit            = $this->table->getItemsPerPageIdentifier();

        $sortValues = [
            $this->table->getSortQueryStringKey()          => $this->sortableName,
            $this->table->getSortDirectionQueryStringKey() => $direction,
            $this->table->getPagingIdentifier()            => $this->table->getCurrentPage(),
            $limit                                         => array_get( $this->table->getInput(), $limit),
        ];

        $qs  = http_build_query( array_merge( $this->table->getInput(), $sortValues ) );

        $url = $this->table->getBaseUrl() . '?' . $qs;

        $icon = '';
        $asc  = '<i class="fa fa-chevron-up"></i>';
        $desc = '<i class="fa fa-chevron-down"></i>';

        if( $this->isBeingSorted() ) {
            if( $currentDirection == self::SORT_ASCENDING ) {
                $icon = $asc;
            }

            if( $currentDirection == self::SORT_DESCENDING ) {
                $icon = $desc;
            }
        }

        return sprintf('<a href="%s" class="grid-view-sort-%s">%s %s</a>', $url, $currentDirection, $icon, $label);
    }

    /**
     * @return string
     */
    public function getCurrentSortDirection()
    {
        return strtolower(
            array_get(
                $this->table->getInput(),
                $this->table->getSortDirectionQueryStringKey(),
                self::SORT_ASCENDING
            )
        );
    }

    /**
     * Get sort direction for column based on input
     * @return string
     */
    public function getNextSortDirection()
    {
        $sortDirection = $this->getCurrentSortDirection();

        if( ! $this->isBeingSorted() ) {
            return self::SORT_ASCENDING;
        }

        if( $sortDirection == self::SORT_ASCENDING ) {
            // next direction
            return self::SORT_DESCENDING;
        }

        if( $sortDirection == self::SORT_DESCENDING ) {
            // next direction
            return self::SORT_ASCENDING;
        }

        return $sortDirection;
    }

    /**
     * @return bool
     */
    public function isBeingSorted()
    {
        $currentlyBeingSorted = array_get( $this->table->getInput(), $this->table->getSortQueryStringKey() );

        return strcasecmp( $this->getSortableName(), $currentlyBeingSorted ) == 0;
    }

    /**
     * @return string
     */
    public function getHeaderName()
    {
        if( !isset($this->header) ) {
            $h = str_replace('_', ' ', $this->name);
            $this->header = ucwords($h);
        }

        return $this->header;
    }

    /**
     * @return mixed|string
     */
    public function getFilter()
    {
        $value = array_get( $this->table->getInput(), $this->name );

        // if the value coming back from array_get is an array, it means name is not set. we don't want an array.
        if( is_array( $value ) ) {
            $value = '';
        }

        if( isset( $this->filter ) && is_array( $this->filter ) ) {
            return sprintf(
                '<select name="%s" class="form-control">%s</select>',
                $this->name,
                $this->buildDropDownList( $this->filter, $value )
            );
        }

        if( !isset( $this->filter ) ) {
            return sprintf(
                '<div class="grid-view-filter-container">
                <input type="text" name="%s" style="width:100%%" class="grid-view-filter input-small form-control" value="%s">
                </div>',
                $this->name,
                e( $value )
            );
        }

        return $this->filter;
    }

    /**
     * build a drop down list (select) from an array
     * @param array $options
     * @param string $selectedValue
     * @return string
     */
    protected function buildDropDownList( array $options, $selectedValue = null )
    {
        $optionsHtml = '';
        foreach( $options as $key => $value ) {
            if( is_array( $value ) ) {
                $optionsHtml .= sprintf(
                    '<optgroup label="%s">%s</optgroup>',
                    $key,
                    $this->listOptions( $value, $selectedValue )
                );
                continue;
            }

            $optionsHtml .= $this->listOptions( [ $key => $value ], $selectedValue );
        }

        return $optionsHtml;
    }

    /**
     * create options tags from array
     * @param array $options
     * @param string $selectedValue
     * @return string
     */
    protected function listOptions( array $options, $selectedValue = null )
    {
        $optionsHtml = '';

        foreach( $options as $key => $value ) {
            $selected = null;

            if( $key === '' ) {
                $key = null;
            }

            if( strcmp( $selectedValue, $key ) == 0 ) {
                $selected = 'selected="selected"';
            }

            $optionsHtml .= sprintf(
                '<option value="%s" %s>%s</option>',
                e( $key ),
                $selected,
                e( $value )
            );
        }

        return $optionsHtml;
    }

    /**
     * @return string
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * @param string $css
     * @return $this
     */
    public function setCss( $css )
    {
        $this->css = $css;
        return $this;
    }

    /**
     * @return string
     */
    public function getJavascript()
    {
        return $this->javascript;
    }

    /**
     * @param string $javascript
     * @return $this
     */
    public function setJavascript( $javascript )
    {
        $this->javascript = $javascript;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName( $name )
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRaw()
    {
        return $this->raw;
    }

    /**
     * @param boolean $raw
     * @return $this
     */
    public function setRaw( $raw )
    {
        $this->raw = $raw;
        return $this;
    }

    /**
     * @param string $sortDirection
     * @return $this
     */
    public function setSortDirection( $sortDirection )
    {
        $this->sortDirection = $sortDirection;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortableName()
    {
        return $this->sortableName;
    }

    /**
     * @param string $sortableName
     * @return $this
     */
    public function setSortableName( $sortableName )
    {
        $this->sortableName = $sortableName;
        return $this;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param Table $table
     * @return $this
     */
    public function setTable( $table )
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param boolean $visible
     * @return $this
     */
    public function setVisible( $visible )
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSortable()
    {
        if( ! $this->name || ! $this->sortableName ) {
            $this->sortable = false;
        }

        return $this->sortable;
    }

    /**
     * @param boolean $sortable
     * @return $this
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;
        return $this;
    }

    /**
     * @throws \RuntimeException if name or value is not set
     * @return string
     */
    protected function fetchValueFromData()
    {
        if( !isset($this->name) && !isset($this->value) ) {
            throw new \RuntimeException('You must set a name or value for a column to render.');
        }

        $value = null;

        if( is_callable( $this->value ) ) {
            $func  = $this->value;
            $value = $func($this->data);

            return $this->isRaw() ? (string) $value : e( (string) $value );
        }

        if( is_array($this->data) ) {
            $value = array_get( $this->data, $this->name );
        }

        if( is_object($this->data) ) {
            $value = object_get( $this->data, $this->name );
        }

        // don't escape value if it is an array or an object.
        if( ! is_scalar( $value ) ) {
            return $value;
        }

        if( $this->formatter ) {
            $this->formatter->setValue( $value );
            return $this->formatter->format();
        }

        return $this->isRaw() ? (string) $value : e( (string) $value );
    }

    /**
     * @return array|string
     */
    public function getFilterName()
    {
        if( ! isset( $this->filterName ) ) {
            $this->filterName = $this->name;
        }

        return $this->filterName;
    }

    /**
     * @param array|string $filterName
     * @return $this
     */
    public function setFilterName( $filterName )
    {
        $this->filterName = $filterName;
        return $this;
    }

    /**
     * @return \ResultTable\Formatter\AbstractFormatter
     */
    public function getFormatter()
    {
        return $this->formatter;
    }
}