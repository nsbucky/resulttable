<?php namespace ResultTable\Columns;

class DateTime extends AbstractColumn {

    /**
     * @var string
     */
    public $format = 'Y-m-d H:i:s';

    /**
     * @var string
     */
    public $cellCss = 'grid-view-datetime-column';

    /**
     * @return null|string
     */
    public function getValue()
    {
        $value = $this->fetchValueFromData();

        if( empty($value) ) {
            return null;
        }

        if( $value instanceof \DateTime ) {
            return $value->format( $this->format );
        }

        try {
            $date = new \DateTime($value);
            return $date->format($this->format);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getJavaScript()
    {
            $js = <<<__JS__
<script type="text/javascript">

   \$('.datetimeColumn').daterangepicker({
        format: "YYYY-MM-DD",
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
            'Last 7 Days': [moment().subtract('days', 6), moment()],
            'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
            'Month To Date': [moment().startOf('month'), moment()],
            'Year To Date': [moment().startOf('year'), moment()]
         }
    });

</script>
__JS__;

        return $js;

    }

    public function getFilter()
    {
        $dateValue = array_get( $this->table->getInput(), $this->name );

        if( ! is_scalar( $dateValue) ) {
            $dateValue = '';
        }

        return sprintf(
            '<div class="grid-view-filter-container">
            <input type="text" name="%s" class="grid-view-filter input-small form-control datetimeColumn" value="%s">
            </div>',
            $this->name,
            $dateValue
        );

    }
}