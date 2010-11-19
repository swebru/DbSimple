--TEST--
QB: simple select
--FILE--
<?php
require_once dirname(__FILE__) . '/../init.php';

function main(&$DB)
{
    $q = $DB->b()->fields('1')->get();
    printr($DB->select('?s', $q));
}

?>
--EXPECT--
Query: 'SELECT 1






'
array (
  0 => 
  array (
    1 => '1',
  ),
)