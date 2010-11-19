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
			'fields' => false,
			'from' => DBSIMPLE_SKIP,
			'join' => DBSIMPLE_SKIP,
			'where' => DBSIMPLE_SKIP,
			'group' => DBSIMPLE_SKIP,
			'having' => DBSIMPLE_SKIP,
			'order' => DBSIMPLE_SKIP,
			'count' => DBSIMPLE_SKIP,
			'offset' => DBSIMPLE_SKIP,
		);
	}

    /**
     * Написать вызовы построенного запроса - что-то типа
     * ->get()->(select|select_row|select_col|select_cell|...)
     *
     * Возможно без этого get() тогда во что переименовать select
     * или же select_all
     */

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

	public function fields($fields)
	{
		$sq = $this->getSQ(func_get_args());
		if (empty($this->data['fields']))
			$this->data['fields'] = $sq;
		else
			$this->data['fields'] = $this->db->subquery('?s, ?s', $this->data['fields'], $sq);
		return $this;
	}

	public function from($table)
	{
		$table = $this->getSQ(func_get_args());
		if ($this->data['from'] === DBSIMPLE_SKIP)
			$this->data['from'] = $table;
		else
			$this->data['from'] = $this->db->subquery('?s, ?s', $this->data['from'], $table);
		return $this;
	}

	public function join($join)
	{
		$join = $this->getSQ(func_get_args());
		if ($this->data['join'] === DBSIMPLE_SKIP)
			$this->data['join'] = $join;
		else
			$this->data['join'] = $this->db->subquery('?s'."\n".'?s', $this->data['join'], $join);
		return $this;
	}

	public function where($where)
	{
		$where = $this->getSQ(func_get_args());
		if ($this->data['where'] === DBSIMPLE_SKIP)
			$this->data['where'] = $where;
		else
			$this->data['where'] = $this->db->subquery('?s'."\n".'?s', $this->data['where'], $where);
		return $this;
	}

	public function limit($offset, $count = false)
	{
		if ($count)
		{
			$this->data['offset'] = $offset;
			$this->data['count'] = $count;
		}
		else
		{
			$this->data['offset'] = 0;
			$this->data['count'] = $offset;
		}
		return $this;
	}

	/**
	 * Возвращает сформированный запрос
	 *
	 * @return DbSimple_SubQuery Запрос
	 */
	public function get()
	{
		if ($this->data['from'] !== DBSIMPLE_SKIP &&
			$this->data['fields'] === false)
			$this->data['fields'] = $this->db->subquery('*');
		
		$q = $this->db->subquery(
'SELECT ?s
{FROM ?s}
{?s}
{WHERE ?s}
{GROUP BY ?s}
{HAVING ?s}
{ORDER BY ?s}
{LIMIT ?d, ?d}',
		$this->data['fields'],
		$this->data['from'],
		$this->data['join'],
		$this->data['where'],
		$this->data['group'],
		$this->data['having'],
		$this->data['order'],
		$this->data['offset'], $this->data['count']);
		return $q;
	}

    
}
?>
