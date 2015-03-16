<?php namespace ResultTable\Buttons;

class Edit extends AbstractButton {

    public $label = 'Edit';
    public $css = 'btn btn-xs btn-success';

    public function render()
    {
        $url     = $this->getUrl();
        $label   = $this->getLabel();
        $onclick = null;

        if( $this->isConfirm() ) {
            $onclick = 'onclick="return confirm(\''.$this->getConfirmMessage().'\')"';
        }

        return sprintf('<a href="%s" class="%s" %s>%s</a>', $url, $this->getCss(), $onclick, $label);
    }
}