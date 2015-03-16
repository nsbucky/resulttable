<?php

use ResultTable\Collection;

class CollectionTest extends PHPUnit_Framework_TestCase {

    public function testRenderTable()
    {
        $table  = new Collection( [ ['test'=>"mytest"] ] );
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
                            'content'=>'Test'
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