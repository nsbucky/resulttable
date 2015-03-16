<?php namespace ResultTable\Columns;

class Boolean extends AbstractColumn {

    /**
     * @var string
     */
    public $trueLabel = 'Yes';

    /**
     * @var string
     */
    public $falseLabel = 'No';

    /**
     * @var array
     */
    public $trueValues = ['yes','true','1','on'];

    /**
     * @var array
     */
    public $falseValues = ['no','false','0','off','null'];

    /**
     * @var array
     */
    public $filter = [''=>'', '0'=>'No', '1'=>'Yes'];

    /**
     * @return string
     */
    public function getValue()
    {
        $value = $this->fetchValueFromData();

        // check and see if value is one of these:
        // 1, true, yes
        $labelHtml = '<span class="label %s">%s</span>';
        $labelCss  = 'label-default';

        if( in_array(strtolower( (string) $value ), $this->trueValues, true) ) {
            $value = $this->trueLabel;
            $labelCss = 'label-success';
        }

        if( in_array(strtolower( (string) $value ), $this->falseValues, true) || empty( $value ) ) {
            $value = $this->falseLabel;
            $labelCss = 'label-danger';
        }

        return sprintf($labelHtml, $labelCss, $value);
    }

}