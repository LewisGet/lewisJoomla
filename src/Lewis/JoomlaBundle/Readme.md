#Getting started

## Basic code for demo

```php
<?php

require_once JPATH_LIBRARIES . '/lewisJoomla/init.php';

$config = array(
    // table name
    'item' => array(
        // column
        'id' => array(
            // column can be read
            'read' => 'id',
            // column can be write
            'write' => 'id',
            // column can create form
            'form' => 'id',
            // delete by this column
            'delete' => true
        ),

        'name' => array(
            'read' => 'string',
            'write' => 'string',
            'form' => 'string'
        ),

        'cat' => array(
            'read' => array(
                // load this column by mapping
                'left' => array(
                    'cat.id',
                    // sql group
                    'group' => false
                    // 'group' => 'item.id'
                )
            ),
            'write' => "int",
            'form' => array(
                // edit page used cat name be list, and used id be key
                'cat.name.id',
                'multiple' => false
            )
        )
    )
);

$app = new \Lewis\JoomlaBundle\App(__DIR__, "Hello", $config, true);

echo $app->execute();
```