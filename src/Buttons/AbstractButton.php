<?php namespace ResultTable\Buttons;

use ResultTable\Traits\ConfigTrait;
use ResultTable\Traits\LinkTrait;
use ResultTable\Table;
use ResultTable\Traits\TokenizeTrait;

abstract class AbstractButton {

    use ConfigTrait;
    use TokenizeTrait;
    use LinkTrait;

    /**
     * @var string
     */
    public $css = 'btn btn-xs btn-default';

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var bool
     */
    public $visible = true;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var bool
     */
    public $confirm = false;

    /**
     * @var string
     */
    public $confirmMessage = 'Are you sure?';

    /**
     * @param Table $table
     * @param array $config
     */
    public function __construct( Table $table, array $config = [] )
    {
        $this->table = $table;
        $this->setConfigOptions( $config );
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        $this->tokenize( $data );
        return $this;
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
    public function setCss($css)
    {
        $this->css = $css;
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
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    /**
     * @return boolean
     */
    public function isConfirm()
    {
        return $this->confirm;
    }

    /**
     * @param boolean $confirm
     */
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
    }

    /**
     * @return string
     */
    public function getConfirmMessage()
    {
        return $this->confirmMessage;
    }

    /**
     * @param string $confirmMessage
     * @return $this
     */
    public function setConfirmMessage($confirmMessage)
    {
        $this->confirmMessage = $confirmMessage;
        return $this;
    }

    abstract public function render();
}