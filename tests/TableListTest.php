<?php

use Mockery as m;
use ResultTable\TableList;

class TableListTest extends PHPUnit_Framework_TestCase {

    public function testRenderTable()
    {
        $table  = new TableList( ['test'=>"mytest"] );
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
                            'content'=>'Label',
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
                    'tag'=>'thead',
                    'descendant'=>array(
                        'tag'=>'tr',
                        'descendant'=>array(
                            'tag'=>'th',
                            'content'=>'Value',
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
                            'tag'=>'th',
                            'content'=>'Test',
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