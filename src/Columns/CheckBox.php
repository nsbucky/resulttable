<?php namespace ResultTable\Columns;

class CheckBox extends AbstractColumn
{

    /**
     * @var bool
     */
    public $checked = false;

    /**
     * @var string
     */
    public $css = 'grid-view-checkbox-column';

    /**
     * create a checkbox instead of an input[text] field for the header
     *
     * @return string
     */
    public function getFilter()
    {
        return '<input type="checkbox" name="grid-view-select-all" class="grid-view-select-all">';
    }

    /**
     * Render a checkbox while checking if the checkbox should be checked by default
     * @return string
     */
    public function getValue()
    {
        $value   = $this->fetchValueFromData();
        $checked = $this->isChecked( $this->data ) ? 'checked="checked"' : null;

        return sprintf( '<label><input type="checkbox" name="%s[]" class="grid-view-checkbox" value="%s" %s> %s</label>',
            $this->name,
            $value,
            $checked,
            $value );
    }

    /**
     * use the passed data to determine if the checkbox from getValue() should be
     * checked or not
     *
     * @param mixed $data
     * @return boolean
     */
    public function isChecked( $data )
    {
        if( is_callable( $this->checked ) ) {
            $func = $this->checked;

            return (bool) $func( $data );
        }

        return (bool) $this->checked;
    }

    /**
     * if $useJqueryJavascripts is true in the parent table, then create some
     * javascript to handle the select all checkbox in the header
     *
     * @return string
     */
    public function getJavascript()
    {
        ob_start()
        ?>
        <script>
            jQuery(function () {
                $('.grid-view-select-all').click(function () {
                    var checks = $('.grid-view-checkbox');
                    checks.prop("checked", $(this).is(':checked'));
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }
}