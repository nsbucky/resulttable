<?php namespace ResultTable\Buttons;

class Delete extends AbstractButton {

    public $label   = 'Delete';
    public $css     = 'btn btn-xs btn-danger';
    public $confirm = true;

    public function render()
    {
        $url     = $this->getUrl();
        $label   = $this->getLabel();
        $onclick = null;

        if( $this->isConfirm() ) {
            $onclick = 'onclick="return confirm(\''.$this->getConfirmMessage().'\')"';
        }

        return sprintf(
            '<form action="%s" method="post" class="form-inline">
				<input type="submit" name="grid-view-submit" value="%s" class="%s" %s>
				<input type="hidden" name="_method" value="DELETE">
				%s
				</form>',
            $url,
            $label,
            $this->getCss(),
            $onclick,
            \Form::token()
        );
    }
}