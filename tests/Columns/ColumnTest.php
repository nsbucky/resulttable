<?php

use ResultTable\Table;
use ResultTable\Collection;
use Mockery as m;
use ResultTable\Columns\Column;
use ResultTable\Columns\Total;
use ResultTable\Columns\Boolean;
use ResultTable\Columns\CheckBox;
use ResultTable\Columns\DateTime;
use ResultTable\Columns\Link;

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
        $column = new Column( $this->table, 'test' );
        $column->setData(['test'=>'BALLS']);
        $value = $column->getValue();

        $this->assertEquals('BALLS', $value);
    }

    public function testNameDeterminesFormat()
    {
        $column = new Column( $this->table, 'test:image' );

        $this->assertEquals('test', $column->getName() );

        $this->assertInstanceOf('ResultTable\Formatter\Image', $column->getFormatter() );
    }

    public function testGetHeader()
    {
        $column = new Column( $this->table, 'test' );
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
        $column = new Column( $this->table, 'test' );

        $this->table->setInput(['sort'=>'test','sort_dir'=>'asc']);

        $this->assertEquals('asc', $column->getCurrentSortDirection());
        $this->assertEquals('desc', $column->getNextSortDirection());
    }

    public function testGetHeaderName()
    {
        $column = new Column( $this->table, 'test' );
        $this->assertEquals('Test', $column->getHeaderName());
    }

    public function testGetFilter()
    {
        $column = new Column( $this->table, 'test' );

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

        $column = new Column( $this->table, [
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

    public function testTotalColumn()
    {
        $column = new Total( $this->table, 'test');
        $column->setData([
            'test' => 5,
        ]);

        $this->assertEquals(5, $column->getValue() );
        $this->assertEquals("5", $column->getFooter());
    }

    public function testBooleanColumn()
    {
        $column = new Boolean( $this->table, 'test' );
        $column->setData([
            'test' => 0
        ]);

        $output = $column->getValue();

        $this->assertTag([
            'tag'=>'span',
            'attributes'=>[
                'class'=>'label label-danger'
            ],
            'content' => 'No'
        ], $output);
    }

    public function testCheckBox()
    {
        $column = new CheckBox( $this->table, 'test');
        $column->setData(['test'=>1]);

        $output = $column->getValue();

        $this->assertTag([
            'tag' =>'label',
            'child'=>[
                'tag'=>'input',
                'attributes'=>[
                    'type' => 'checkbox',
                    'class'=>'grid-view-checkbox',
                    'value'=>'1',
                    'name'=>'test[]'
                ]
            ]
        ], $output);
    }

    public function testLink()
    {
        $column = new Link( $this->table, [
            'name' => 'test',
            'url' => "{test}",
            'label'=>'balls'
        ]);
        $column->setData(['test'=>'balls.com']);

        $output = $column->getValue();

        $this->assertTag([
            'tag' => 'a',
            'attributes' => [
                'href' => 'balls.com',
            ],
            'content'=>'balls'
        ], $output);
    }

    public function testDateTime()
    {
        $column = new DateTime( $this->table, 'test');
        $date = date('Y-m-d H:i:s');
        $column->setData([
            'test'=>$date,
        ]);

        $output = $column->getValue();
        $this->assertEquals( $date, $output);
    }
}