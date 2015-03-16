<?php namespace ResultTable\Columns;

class Column extends AbstractColumn {

    /**
     * get the value to be used in the table cell. if the value set is callable
     * pass data to the callable function and get the value from it
     * @return string
     * @throws \RuntimeException if name or value are not set for column
     */
    public function getValue()
    {
        return $this->fetchValueFromData();
    }
}