<?php namespace ResultTable;

class TableList extends Table {

    /**
     * @var mixed
     */
    protected $dataSource;

    /**
     * css classes used on the main table when rendered
     *
     * @var string
     */
    public $tableCss = 'table table-striped table-bordered';

    /**
     * the string to use for the header column
     *
     * @var string
     */
    public $labelColumnHeader = 'Label';

    /**
     * the string to use for the value column
     *
     * @var string
     */
    public $valueColumnHeader = 'Value';

    /**
     * @param $dataSource
     * @param array $config
     */
    public function __construct( $dataSource, array $config = [] )
    {
        $this->dataSource = $dataSource;

        $this->setConfigOptions( $config );

        if( ! isset( $this->baseUrl ) ) {
            $this->baseUrl = \Request::getPathInfo();
        }
    }

    /**
     * @return mixed
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @param mixed $dataSource
     */
    public function setDataSource( $dataSource )
    {
        $this->dataSource = $dataSource;
    }

    /**
     * @return string
     */
    public function getLabelColumnHeader()
    {
        return $this->labelColumnHeader;
    }

    /**
     * @param string $labelColumnHeader
     */
    public function setLabelColumnHeader( $labelColumnHeader )
    {
        $this->labelColumnHeader = $labelColumnHeader;
    }

    /**
     * @return string
     */
    public function getValueColumnHeader()
    {
        return $this->valueColumnHeader;
    }

    /**
     * @param string $valueColumnHeader
     */
    public function setValueColumnHeader( $valueColumnHeader )
    {
        $this->valueColumnHeader = $valueColumnHeader;
    }

    /**
     * @return string
     */
    public function render()
    {
        if( count( $this->dataSource ) < 1 ) {
            return $this->getNoResultsText();
        }

        $columns = $this->getColumns();

        ob_start();
        ?>
        <table class="<?php echo $this->tableCss; ?>" id="<?php echo $this->id;?>">
            <thead>
            <tr>
                <th><?php echo $this->getLabelColumnHeader();?></th>
                <th><?php echo $this->getValueColumnHeader();?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach( $columns as $column ): ?>
                <?php if( ! $column->isVisible() ) continue; ?>
                <tr>
                    <th><?php echo $column->setSortable(false)->getHeader();?></th>
                    <?php $column->setData( $this->dataSource );?>
                    <td class="<?php echo $column->getCss();?>"><?php echo $column->getValue();?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        <?php echo implode(PHP_EOL, $this->getJavascript() );?>
        <?php

        return ob_get_clean();
    }
}