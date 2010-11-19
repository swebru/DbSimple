--TEST--
QB: simple query
--FILE--
<?php
require_once dirname(__FILE__) . '/../init.php';

function main(&$DB)
{
	$row = array(
		'id'  => 1,
		'str' => 'test'
	);
	
	@$DB->query("DROP TABLE test");
	$DB->query("CREATE TABLE test(id INTEGER, str VARCHAR(10))");
	$DB->query("INSERT INTO test(?#) VALUES(?a)", array_keys($row), array_values($row));
	
    $q = $DB->b()->from('test')->get();
    printr($DB->select('?s', $q));
}

?>
--EXPECT--
Query: 'DROP TABLE test'
Query: 'CREATE TABLE test(id INTEGER, str VARCHAR(10))'
Query: 'INSERT INTO test(`id`, `str`) VALUES(\'1\', \'test\')'
Query: 'SELECT *
 FROM test 





'
array (
  0 => 
  array (
    'id' => '1',
    'str' => 'test',
  ),
)
