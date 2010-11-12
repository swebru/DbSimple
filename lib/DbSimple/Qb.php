<?php
/**
 * Построитель запросов
 *
 * @author ivan
 */
class DbSimple_Qb
{
	/** @var DbSimple_Database ссылка на базу данных */
	protected $db;
	/** @var array части запроса */
	protected $data;

	public function  __construct($db)
	{
		$this->db = &$db;
		$this->data = array(
			'select' => false,
			'from' => DBSIMPLE_SKIP,
			'join' => DBSIMPLE_SKIP,
			'where' => DBSIMPLE_SKIP,
			'group' => DBSIMPLE_SKIP,
			'having' => DBSIMPLE_SKIP,
			'order' => DBSIMPLE_SKIP,
			'limit' => DBSIMPLE_SKIP,
			'offset' => DBSIMPLE_SKIP,
		);
	}

	/**
	 * Врапер для формирование DbSimple_SubQuery из параметров функции
	 *
	 * @param array $args Аргументы функции
	 * @return DbSimple_SubQuery Собранный подзапрос
	 */
	private function getSQ($args)
	{
		return call_user_func_array(array($this->db, 'subquery'), $args);
	}

	public function select($fields)
	{
		$sq = $this->getSQ(func_get_args());
		if (empty($this->data['select']))
			$this->data['select'] = $sq;
		else
			$this->data['select'] = $this->db->subquery('?s, ?s', $this->data['select'], $sq);
		return $this;
	}

	public function from($table)
	{
		$table = call_user_func_array(array($this->db, 'subquery'), func_get_args());
		if (empty($this->data['from']))
			$this->data['from'] = $table;
		else
			$this->data['from'] = $this->db->subquery('?s, ?s', $this->data['from'], $table);
		return $this;
	}

	/**
	 * Возвращает сформированный запрос
	 *
	 * @return DbSimple_SubQuery Запрос
	 */
	public function get()
	{
		if ($this->data['from'] != DBSIMPLE_SKIP &&
			$this->data['select'] == false)
			$this->data['select'] = '*';
		
		$q = $this->db->subquery(
'SELECT ?s
{FROM ?s}
{?s}
{WHERE ?s}
{GROUP BY ?s}
{HAVING ?s}
{ORDER BY ?s}
{LIMIT ?d, ?d}',
		$this->data['select'],
		$this->data['from'],
		$this->data['join'],
		$this->data['where'],
		$this->data['group'],
		$this->data['having'],
		$this->data['order'],
		$this->data['limit'], $this->data['offset']);
		return $q;
	}

    
}
?>
