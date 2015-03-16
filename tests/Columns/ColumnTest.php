<?php

use ResultTable\Table;
use Mockery as m;

class ColumnTest extends PHPUnit_Framework_TestCase {

    public $paginator;
    public $table;

    public function setUp()
    {
        $paginator = m::mock('Illuminate\Pagination\Paginator');
        $paginator->shouldReceive('getCurrentPage')->andReturn(0);
        $paginator->shouldReceive('links')->andReturn('');
        $paginator->shouldReceive('getTotal')->andReturn(5);
        $paginator->shouldReceive('count')->andReturn(5);
        $this->paginator = $paginator;

        $this->table  = new Table( $this->paginator );
    }

    public function testFetchValue()
    {
        $column = new \ResultTable\Columns\Column( $this->table, 'test' );
        $column->setData(['test'=>'BALLS']);
        $value = $column->getValue();

        $this->assertEquals('BALLS', $value);
    }

    public function testNameDeterminesFormat()
    {
        $column = new \ResultTable\Columns\Column( $this->table, 'test:image' );

        $this->assertEquals('test', $column->getName() );

        $this->assertInstanceOf('ResultTable\Formatter\Image', $column->getFormatter() );
    }

    public function testGetHeader()
    {
        $column = new \ResultTable\Columns\Column( $this->table, 'test' );
        $header = $column->getHeader();

        $this->assertTag([
            'tag' => 'a',
            'attributes' => [
                'class'=>'grid-view-sort-asc',
                'href'=>'/?sort=test&sort_dir=asc&page=0'
            ],
            'content'=>'Test'
        ], $header);
    }

    public function testGetSortDirections()
    {
        $column = new \ResultTable\Columns\Column( $this->table, 'test' );

        $this->table->setInput(['sort'=>'test','sort_dir'=>'asc']);

        $this->assertEquals('asc', $column->getCurrentSortDirection());
        $this->assertEquals('desc', $column->getNextSortDirection());
    }

    public function testGetHeaderName()
    {
        $column = new \ResultTable\Columns\Column( $this->table, 'test' );
        $this->assertEquals('Test', $column->getHeaderName());
    }

    public function testGetFilter()
    {
        $column = new \ResultTable\Columns\Column( $this->table, 'test' );

        $filter = $column->getFilter();

        $this->assertTag([
            'tag' => 'div',
            'attributes'=>[
                'class'=>'grid-view-filter-container'
            ],
            'child'=>[
                'tag'=>'input',
                'attributes'=>[
                    'name'=>'test',
                    'type'=>'text',
                    'class'=>'grid-view-filter input-small form-control'
                ]
            ]
        ], $filter);

        $column = new \ResultTable\Columns\Column( $this->table, [
            'name' => 'test',
            'filter'=>[0=>'no',1=>'yes']
        ] );

        $filter = $column->getFilter();

        $this->assertTag([
            'tag'=>'select',
            'attributes'=>[
                'name'=>'test',
                'class'=>'form-control'
            ],
            'descendant'=>[
                'tag'=>'option',
                'attributes'=>[
                    'value'=>'0'
                ]
            ]
        ], $filter);
    }
}