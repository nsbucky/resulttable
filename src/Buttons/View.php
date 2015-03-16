<?php namespace ResultTable\Buttons;

class View extends AbstractButton {

    public $label = 'View';
    public $css = 'btn btn-xs btn-info';

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