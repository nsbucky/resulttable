# ResultTable
Inspired by Yii's CGridView, this Laravel 4.2 dependent class strives to be a simple way to generate a table from an array, ie; a database result set. It works off of an array of arrays, or an array of objects. You decide which array columns or object properties to display. It does not rewrite queries or handle paging, that is up to you.

## Features
- Configurable headers
- Generates sort urls
- Uses Bootstrap table css by default
- Provided column types enable quicker development time

## Column Types
ResultTable offers a few different column types for calculations or displaying certain data types.

- Column: default column type, supports visiblity and custom headers and filters
- CheckBox: Generate a checkbox in a table cell
- DateTime: Use php's DateTime object to format date strings
- Link: generate anchor tags
- Total: sum columns and puts a total in the table footer

## Formatters
You can quickly format a column when you add it to the table:

    ->addColumn('name:formatter|option1:value1')
    ->addColumn('email_address:email')

### Required setup

Download the library, and just put it someplace you can autoload with composer:

    "autoload": {
        "psr-4": {
          "ResultTable\\": "/path/to/src/"
        }
      }

## Example

    $table = new ResultTable\Table( Paginator $dataSource);
    $table->addColumn('uniqid')
    ->addLinkColumn([
          'filter'=>Form::text('first_name', Input::get('first_name'), ['placeholder'=>'First Name','class'=>'form-control']).
                    '<br>'.Form::text('last_name', Input::get('last_name'), ['placeholder'=>'Last Name','class'=>'form-control']),
          'name' =>'full_name',
          'filterName'=>['first_name','last_name'],
          'label'=>function( $data ){
                  return $data->displayName();
              },
          'url'=>'/leads/{id}'
      ])
    ->addColumn('uploaded_image:image|width:50|height:50')   
    ->addDateTimeColumn('created_at')
    ->addViewButton('/url/{id}');
    
    echo $table->render();