<?php namespace ResultTable;

use Illuminate\Pagination\Paginator;
use ResultTable\Buttons\Delete;
use ResultTable\Buttons\Edit;
use ResultTable\Buttons\Submit;
use ResultTable\Buttons\View;
use ResultTable\Columns\AbstractColumn;
use ResultTable\Columns\Boolean;
use ResultTable\Columns\CheckBox;
use ResultTable\Columns\Column;
use ResultTable\Columns\DateTime;
use ResultTable\Columns\Link;
use ResultTable\Buttons\AbstractButton;
use ResultTable\Columns\Total;
use ResultTable\Traits\ConfigTrait;

class Table {

    use ConfigTrait;

    /**
     * @var string
     */
    public $id = 'ResultTable';

    /**
     * css classes for <table>
     * @var string
     */
    public $tableCss = 'table table-bordered table-striped';

    /**
     * @var string
     */
    public $rowCss = '';

    /**
     * @var string
     */
    public $noResultsText = '<div class="well well-sm" style="margin:1em;"><p><span class="text-info">No results.</span></p></div>';

    /**
     * @var array
     */
    public $itemsPerPage = [ 15, 30, 45, 60, 75, 90 ];

    /**
     * @var string
     */
    public $itemsPerPageIdentifier = 'limit';

    /**
     * @var AbstractButton[]
     */
    protected $buttons = [];

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var array
     */
    protected $input = [];

    /**
     * @var AbstractColumn[]
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $javascript = [];

    /**
     * @var string
     */
    public $tableHeaderText = 'Listing All Items';

    /**
     * @var string
     */
    public $tableWrapper = '<div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-1" data-widget-editbutton="false">
            <header><span class="widget-icon"> <i class="fa fa-table"></i> </span><h2>{tableHeaderText}</h2></header>
            <div>
                <div class="jarviswidget-editbox"></div>
                    <div class="widget-body no-padding">
                        <div class="widget-body-toolbar">{itemsPerPage} {filter}</div>
                        {table}
                    </div>
                <div class="dt-row dt-bottom-row">{pagerLinks}</div>
            </div>
            </div>';

    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @var bool
     */
    public $sorting = true;

    /**
     * What variable name to send back to server for field to sort on
     * @var string
     */
    public $sortQueryStringKey = 'sort';

    /**
     * variable name to send back to server to indicate sorting direction
     * @var string
     */
    public $sortDirectionQueryStringKey = 'sort_dir';

    /**
     * @var string
     */
    public $pagingIdentifier = 'page';

    /**
     * @var bool
     */
    public $showFilterArea = true;

    /**
     * @var null
     */
    public $pagerLinksView = null;

    /**
     * @param Paginator $paginator
     * @param array $config
     * @param array $excludeQueryString
     */
    public function __construct( Paginator $paginator, array $config = [], array $excludeQueryString = ['page'] )
    {
        $this->paginator  = $paginator;

        $this->input = \Input::except( $excludeQueryString );

        $this->setConfigOptions( $config );

        if( ! isset( $this->baseUrl ) ) {
            $this->baseUrl = \Request::getPathInfo();
        }
    }

    /**
     * @return string
     */
    public function render()
    {
        if( count( $this->paginator ) < 1 ) {
            $replace = [
                '{table}'           => $this->getNoResultsText(),
                '{tableHeaderText}' => $this->getTableHeaderText(),
                '{filter}'          => '',
                '{pagerLinks}'      => '',
                '{itemsPerPage}'    => ''
            ];

            return str_replace( array_keys( $replace ), array_values( $replace), $this->tableWrapper );
        }

        $columns = $this->getColumns();

        $headerFilter  = '';
        $perPageFilter = '';

        if( $this->showFilterArea ) {
            $headerFilter  = $this->renderModalFilter();
            $perPageFilter = $this->renderItemsPerPage();
        }

        $replace = [
            '{tableHeaderText}' => $this->getTableHeaderText(),
            '{filter}'          => $headerFilter,
            '{pagerLinks}'      => $this->getPageLinks( $this->pagerLinksView ),
            '{itemsPerPage}'    => $perPageFilter
        ];
        ob_start();
        ?>
        <table class="<?php echo $this->tableCss; ?>" id="<?php echo $this->id;?>">
            <?php
            ob_start();
            ?>
            <thead>
                <tr>
            <?php foreach( $columns as $column ): ?>
                <?php if( ! $this->isSorting() ) $column->setSortable( false ); ?>
                <?php if( ! $column->isVisible() ) continue;?>
                    <th><?php echo $column->getHeader();?></th>
            <?php endforeach;?>
            <?php if( count( $this->buttons ) > 0 ) :?>
                <th>Actions</th>
            <?php endif; ?>
                </tr>
            </thead>
            <?php $tHead = ob_get_clean(); ?>
            <?php ob_start();?>
            <tbody>
            <?php foreach( $this->paginator as $data ): ?>
                <tr>
                <?php foreach( $columns as $column ): ?>
                <?php if( ! $column->isVisible() ) continue; ?>
                <?php $column->setData( $data );?>
                    <td class="<?php echo $column->getCss();?>"><?php echo $column->getValue();?></td>
                <?php endforeach;?>
                <?php if( count( $this->buttons ) > 0 ) :?>
                <td class="grid-view-button-column">
                    <?php foreach( $this->buttons as $button ): ?>
                        <?php $button->setData( $data );?>
                        <?php if( ! $button->isVisible() ) continue; ?>
                        <span class="grid-view-button"><?php echo $button->render();?></span>
                    <?php endforeach; ?>
                </td>
                <?php endif; ?>
                </tr>
            <?php endforeach;?>
            </tbody>
            <?php $tBody = ob_get_clean();?>
            <?php ob_start();?>
            <tfoot>
            <tr>
                <?php foreach( $columns as $column ): ?>
                    <?php if( ! $column->isVisible() ) continue;?>
                    <th><?php echo $column->getFooter();?></th>
                <?php endforeach;?>
                <?php if( count( $this->buttons ) > 0 ) :?>
                    <th></th>
                <?php endif; ?>
            </tr>
            </tfoot>
            <?php $tFoot = ob_get_clean();?>
            <?php echo $tHead, $tFoot, $tBody;?>
        </table>
        <?php echo implode(PHP_EOL, $this->getJavascript() );?>
        <?php

        $replace['{table}'] = ob_get_clean();

        return str_replace( array_keys( $replace ), array_values( $replace), $this->tableWrapper );
    }

    /**
     * @return \Illuminate\View\View
     */
    public function getPageLinks( $view = null )
    {
        return $this->paginator->links( $view );
    }

    /**
     * @return string
     */
    public function renderModalFilter()
    {
        $filters = [];
        $exclude = [];

        foreach($this->columns as $column) {
            $filterName = (array) $column->getFilterName();

            foreach( $filterName as $fn ) {
                $exclude[$fn] = $fn;
            }

            if( ! $column->isVisible() ) {
                continue;
            }

            $filter = $column->getFilter();

            if( empty($filter) ) continue;

            $filters[] = '<div class="form-group"><label>'.$column->getHeaderName().'</label>'.$filter.'</div>';
        }

        $filters = implode(PHP_EOL, $filters);

        $hidden = '';

        $hiddens = array_diff_key( $this->input, $exclude);

        foreach( $hiddens as $key => $value ) {

            $key = e( $key );

            if( is_array( $value ) ) {
                foreach( $value as $_k => $_v ) {
                    $_v = e( $_v );

                    if( is_scalar($_k) ) {
                        $hidden .= "<input type='hidden' value='$_v' name='{$key}[$_k]'>".PHP_EOL;
                    } else {
                        $hidden .= "<input type='hidden' value='$_v' name='{$key}[]'>".PHP_EOL;
                    }
                }
                continue;
            }

            $hidden .= "<input type='hidden' value='".e($value)."' name='$key'>".PHP_EOL;

        }

        $baseUrl = $this->getBaseUrl();

        $html = <<<MODAL

<button type="button" data-toggle="modal" href="#modal-grid-filters" class="btn btn-default btn-sm"><i class="fa fa-search-plus"></i> Filter Table</button>

<!-- Modal -->
<div class="modal fade" id="modal-grid-filters" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Filter Table</h4>
            </div>
            <div class="modal-body">
                <form action="$baseUrl" method="get" id="grid-filter-form">
                    $hidden
                    $filters
                    <input type="reset" name="reset" value="Reset Form" class="btn btn-danger modal-reset">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary modal-save">Filter</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    $('#modal-grid-filters .modal-save').click(function(){
        $('#grid-filter-form').submit();
    });

    $('.modal-reset').click(function(){
        $(':input','#grid-filter-form')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected')
            .removeAttr('value');
    });

</script>
MODAL;

        return $html;

    }

    /**
     * create the string of html for the drop down list using the $itemsPerPage
     * variable. It also creates some boilerplate javascript that depends on jQuery
     * to build a url to send to server.
     * @return string
     */
    public function renderItemsPerPage()
    {
        $url = $this->getBaseUrl() . '?' . http_build_query( array_except($this->getInput(), 'limit') );
        ob_start(); ?>
        <script>
            jQuery(function(){
                $("#grid-view-<?php echo $this->getItemsPerPageIdentifier()?>").change(function(){
                    window.location = "<?php echo $url . '&' . $this->getItemsPerPageIdentifier()?>="+$(this).val();
                });
            });
        </script>
        <?php
        $this->addJavascript( ob_get_clean(), 'per-page-filter');
        ob_start();
        ?>
            <div class="pull-right">
                Showing <?php echo count($this->paginator);?> of <?php echo $this->getTotal();?> items.
                <select name="<?php echo $this->getItemsPerPageIdentifier()?>"
                        id="grid-view-<?php echo $this->getItemsPerPageIdentifier()?>">
                    <?php
                    $limitSelected = array_get( $this->input, $this->getItemsPerPageIdentifier() );
                    foreach( (array) $this->itemsPerPage as $limit) {
                        $selected = null;
                        if( strcmp( $limitSelected, $limit ) == 0 ) {
                            $selected = 'selected="selected"';
                        }
                        printf('<option value="%d" %s>%d</option>'.PHP_EOL, $limit, $selected, $limit);
                    }
                    ?>
                </select>
            </div>
        <?php
        return ob_get_clean();
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->paginator->getTotal();
    }

    /**
     * @param $url
     * @param array $config
     * @return $this
     */
    public function addViewButton( $url, array $config = []  )
    {
        $config['url']   = $url;
        $button          = new View( $this, $config );
        $this->buttons[] = $button;

        return $this;
    }

    /**
     * @param $url
     * @param array $config
     * @return $this
     */
    public function addEditButton( $url, array $config = [] )
    {
        $config['url']   = $url;
        $button          = new Edit( $this, $config );
        $this->buttons[] = $button;

        return $this;
    }

    /**
     * @param $url
     * @param array $config
     * @return $this
     */
    public function addDeleteButton( $url, array $config = [] )
    {
        $config['url']   = $url;
        $button          = new Delete( $this, $config );
        $this->buttons[] = $button;

        return $this;
    }

    /**
     * @param $url
     * @param array $config
     * @return $this
     */
    public function addSubmitButton( $url, array $config = [])
    {
        $config['url']   = $url;
        $button          = new Submit( $this, $config );
        $this->buttons[] = $button;

        return $this;
    }

    /**
     * @param $columnName
     * @return $this
     */
    public function addColumn( $columnName )
    {
        if( is_scalar( $columnName ) ) {
            $column = new Column( $this, $columnName );
        }

        if( is_array( $columnName ) ) {
            $column = new Column( $this, $columnName );
        }

        $this->columns[] = $column;
        $this->addJavascript( $column->getJavascript(), $column->getName() );

        return $this;
    }

    /**
     * @param $columnName
     * @return $this
     */
    public function addBooleanColumn( $columnName )
    {
        if( is_scalar( $columnName ) ) {
            $column = new Boolean( $this, $columnName );
        }

        if( is_array( $columnName ) ) {
            $column = new Boolean( $this, $columnName );
        }

        $this->columns[] = $column;
        $this->addJavascript( $column->getJavascript(), $column->getName() );

        return $this;
    }

    /**
     * @param $columnName
     * @return $this
     */
    public function addCheckBoxColumn( $columnName )
    {
        if( is_scalar( $columnName ) ) {
            $column = new CheckBox( $this, $columnName );
        }

        if( is_array( $columnName ) ) {
            $column = new CheckBox( $this, $columnName );
        }

        $this->columns[] = $column;
        $this->addJavascript( $column->getJavascript(), $column->getName() );

        return $this;
    }

    /**
     * @param $columnName
     * @return $this
     */
    public function addDateTimeColumn( $columnName )
    {
        if( is_scalar( $columnName ) ) {
            $column = new DateTime( $this, $columnName );
        }

        if( is_array( $columnName ) ) {
            $column = new DateTime( $this, $columnName );
        }

        $this->columns[] = $column;
        $this->addJavascript( $column->getJavascript(), $column->getName() );

        return $this;
    }

    /**
     * @param $columnName
     * @return $this
     */
    public function addLinkColumn( $columnName )
    {
        if( is_scalar( $columnName ) ) {
            $column = new Link( $this, $columnName );
        }

        if( is_array( $columnName ) ) {
            $column = new Link( $this, $columnName );
        }

        $this->columns[] = $column;
        $this->addJavascript( $column->getJavascript(), $column->getName() );

        return $this;
    }

    public function addTotalColumn( $columnName )
    {
        if (is_scalar( $columnName )) {
            $column = new Total( $this, $columnName );
        }

        if (is_array( $columnName )) {
            $column = new Total( $this, $columnName );
        }

        $this->columns[ ] = $column;
        $this->addJavascript( $column->getJavascript(), $column->getName() );

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $cssId
     * @return $this
     */
    public function setId( $cssId )
    {
        $this->id = $cssId;
        return $this;
    }

    /**
     * @return array
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @param array $itemsPerPage
     * @return $this
     */
    public function setItemsPerPage( array $itemsPerPage )
    {
        $this->itemsPerPage = $itemsPerPage;
        return $this;
    }

    /**
     * @return string
     */
    public function getItemsPerPageIdentifier()
    {
        return $this->itemsPerPageIdentifier;
    }

    /**
     * @param string $itemsPerPageIdentifier
     * @return $this
     */
    public function setItemsPerPageIdentifier( $itemsPerPageIdentifier )
    {
        $this->itemsPerPageIdentifier = $itemsPerPageIdentifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getNoResultsText()
    {
        return $this->noResultsText;
    }

    /**
     * @param string $noResultsText
     * @return $this
     */
    public function setNoResultsText( $noResultsText )
    {
        $this->noResultsText = $noResultsText;
        return $this;
    }

    /**
     * @return string
     */
    public function getRowCss()
    {
        return $this->rowCss;
    }

    /**
     * @param string $rowCss
     * @return $this
     */
    public function setRowCss( $rowCss )
    {
        $this->rowCss = $rowCss;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableCss()
    {
        return $this->tableCss;
    }

    /**
     * @param string $tableCss
     * @return $this
     */
    public function setTableCss( $tableCss )
    {
        $this->tableCss = $tableCss;
        return $this;
    }

    /**
     * @return array
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function setInput( $input )
    {
        $this->input = $input;
        return $this;
    }

    /**
     * @return Buttons\AbstractButton[]
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * @return Columns\AbstractColumn[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getJavascript()
    {
        return $this->javascript;
    }

    /**
     * @return Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @param string $tableHeaderText
     * @return $this
     */
    public function setTableHeaderText( $tableHeaderText )
    {
        $this->tableHeaderText = $tableHeaderText;
        return $this;
    }

    /**
     * @param string $tableWrapper
     * @return $this
     */
    public function setTableWrapper( $tableWrapper )
    {
        $this->tableWrapper = $tableWrapper;
        return $this;
    }

    /**
     * @param array $javascript
     * @return $this
     */
    public function setJavascript( $javascript )
    {
        $this->javascript = $javascript;
        return $this;
    }

    /**
     * append javascript to internal array, or replace/add by key
     * @param $javascript
     * @param null $key
     * @return $this
     */
    public function addJavascript( $javascript, $key = null )
    {
        if( $key === null ) {
            $this->javascript[] = $javascript;
            return $this;
        }

        $this->javascript[$key] = $javascript;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableHeaderText()
    {
        return $this->tableHeaderText;
    }

    /**
     * @return string
     */
    public function getTableWrapper()
    {
        return $this->tableWrapper;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortQueryStringKey()
    {
        return $this->sortQueryStringKey;
    }

    /**
     * @param $sortQueryStringKey
     * @return $this
     */
    public function setSortQueryStringKey($sortQueryStringKey)
    {
        $this->sortQueryStringKey = $sortQueryStringKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortDirectionQueryStringKey()
    {
        return $this->sortDirectionQueryStringKey;
    }

    /**
     * @param $sortDirectionQueryStringKey
     * @return $this
     */
    public function setSortDirectionQueryStringKey($sortDirectionQueryStringKey)
    {
        $this->sortDirectionQueryStringKey = $sortDirectionQueryStringKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getPagingIdentifier()
    {
        return $this->pagingIdentifier;
    }

    /**
     * @param string $pagingIdentifier
     */
    public function setPagingIdentifier( $pagingIdentifier )
    {
        $this->pagingIdentifier = $pagingIdentifier;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->paginator->getCurrentPage();
    }

    /**
     * @return boolean
     */
    public function isShowFilterArea()
    {
        return $this->showFilterArea;
    }

    /**
     * @param boolean $showFilterArea
     * @return $this;
     */
    public function setShowFilterArea( $showFilterArea )
    {
        $this->showFilterArea = $showFilterArea;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSorting()
    {
        return $this->sorting;
    }

    /**
     * @param boolean $sorting
     * @return $this;
     */
    public function setSorting( $sorting )
    {
        $this->sorting = $sorting;
        return $this;
    }



}