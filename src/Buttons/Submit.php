<?php namespace ResultTable\Buttons;

class Submit extends AbstractButton {

    public $label = 'Submit';
    public $css = 'btn btn-xs btn-primary';
    public $confirm = false;
    public $method = 'post';

    /**
     * name => value
     * @var array
     */
    public $hiddenFields = [];

    public function render()
    {
        $url     = $this->getUrl();
        $label   = $this->getLabel();
        $onclick = null;

        if( strtolower($this->method) == 'put' ) {
            $this->hiddenFields['_method'] = 'PUT';
        }

        if( $this->isConfirm() ) {
            $onclick = 'onclick="return confirm(\''.$this->getConfirmMessage().'\')"';
        }

        return sprintf(
            '<form action="%s" method="%s" class="form-inline">
				<input type="submit" name="grid-view-submit" value="%s" class="%s" %s>
				%s
				%s
				</form>',
            $url,
            $this->method,
            $label,
            $this->getCss(),
            $onclick,
            $this->buildHiddenFields(),
            \Form::token()
        );
    }

    protected function buildHiddenFields()
    {
        if( count( $this->hiddenFields ) < 1 ) {
            return '';
        }

        $hidden = [
            ''
        ];
        foreach( $this->hiddenFields as $fieldName => $fieldValue ) {
            $hidden[] = \Form::hidden($fieldName, $this->replaceTokens($fieldValue));
        }

        return implode(PHP_EOL, $hidden);
    }
}