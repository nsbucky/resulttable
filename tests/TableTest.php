<?php

use Mockery as m;
use ResultTable\Table;

class TableTest extends PHPUnit_Framework_TestCase {

    public $paginator;

    public function __construct()
    {
        $paginator = m::mock('Illuminate\Pagination\Paginator');
        $paginator->shouldReceive('getCurrentPage')->andReturn(0);
        $paginator->shouldReceive('links')->andReturn('');
        $paginator->shouldReceive('getTotal')->andReturn(5);
        $paginator->shouldReceive('count')->andReturn(5);
        $this->paginator = $paginator;
    }

    public function testGetTotal()
    {
        $table = new Table( $this->paginator );
        $this->assertEquals(5, $table->getTotal() );
    }

    public function testRenderItemsPerPage()
    {
        $table  = new Table( $this->paginator );
        $output = $table->renderItemsPerPage();

        $javascript = $table->getJavascript();
        $this->assertTrue( array_key_exists( 'per-page-filter', $javascript));
        $this->assertNotNull( $javascript['per-page-filter'] );

        $this->assertTag([
            'tag'=>'div',
            'attributes'=>[
                'class'=>'pull-right',
            ],
            'child'=>[
                'tag'=>'select'
            ]
        ], $output);
    }

    public function testRenderModalFitler()
    {
        $table  = new Table( $this->paginator );
        $table->addColumn('test');

        $output = $table->renderModalFilter();

        $this->assertTag([
            'tag'=>'button',
            'attributes'=>[
                'href'=>'#modal-grid-filters',
            ],
        ], $output);

        $this->assertTag([
            'tag'=>'div',
            'attributes'=>[
                'class'=>'modal',
            ]
        ], $output);

        $this->assertTag([
            'tag'=>'div',
            'attributes'=>[
                'class'=>'modal-body',
            ],
            'child'=>[
                'tag'=>'form',
                'descendant'=>[
                    'tag'=>'input',
                    'attributes'=>[
                        'name' => 'test'
                    ]
                ]
            ]
        ], $output);
    }

    public function testRenderTable()
    {
        $table  = new Table( new \Illuminate\Pagination\Paginator( [ ['test'=>"mytest"] ] ) );
        $table->setTableWrapper('{table}');
        $table->addColumn('test');
        $output = $table->render();

        $this->assertTag(
            array(
                'tag'=>'table',
                'id'=>'ResultTable',
                'attributes'=>array(
                    'class'=>'table table-bordered table-striped',
                ),
                'child'=>array(
                    'tag'=>'thead',
                    'descendant'=>array(
                        'tag'=>'tr',
                        'descendant'=>array(
                            'tag'=>'th',
                            'child'=>array(
                                'tag'=>'a',
                                'attributes'=>array(
                                    'href'=>'/?sort=test&sort_dir=asc&page=0',
                                    'class'=>'grid-view-sort-asc'
                                ),
                                'content'=>'Test'
                            )
                        )
                    )
                ),
            ),
            $output
        );

        $this->assertTag(
            array(
                'tag'=>'table',
                'id'=>'ResultTable',
                'attributes'=>array(
                    'class'=>'table table-bordered table-striped',
                ),
                'child'=>array(
                    'tag'=>'tbody',
                    'descendant'=>array(
                        'tag'=>'tr',
                        'descendant'=>array(
                            'tag'=>'td',
                            'content'=>'mytest',
                        )
                    )
                ),
            ),
            $output
        );
    }

}