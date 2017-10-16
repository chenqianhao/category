<?php
/**
 * Created by  68527761@qq.com
 * User: chenqinahao
 * Date: 2017/10/16
 */

namespace cqh\category;

class CateGoryTree {

	/**
	 * @var array $conf 默认参数集(会被用户设置的覆盖)
	 */
	private static $conf = array(
		'data' => array(),
		//默认初始查询的父id
		'pid' => 0,
		//默认初始层级
		'lev' => 1,
		//排序
		'sort' => array(
			//排序开关
			'is_sort' => false,
			//排序字段
			'order' => 'sort',
			//排序方式
			'orderby' => 'ASC',
		),
		//字段设置
		'field' => array(
			//表中父id字段名
			'CQH_PARENT_ID' => 'parent_id',
			//表中分类id字段名
			'CQH_CAT_ID' => 'cat_id',
			//添加的层级字段
			'CQH_LEV' => 'lev',
		),
	);

	private static $list = array();
	private static $maxlev;

	/** 获取带层级分类
	 * @param  array $conf 用户传过来的参数集
	 * @return array $list 结果
	 * @author chenqianhao <68527761@qq.com>
	 */
	public static function get_cat_list($conf) {

		//合并参数
		self::$conf = array_merge(self::$conf, $conf);
		//简化参数
		$config = self::$conf;
		$data = $config['data'];
		$field = $config['field'];
		$sort = $config['sort'];

		foreach ($data as $k => $u) {
			if ($u[$field['CQH_PARENT_ID']] == $config['pid']) {
				//存入第一级符合查询条件的分类并删除
				$u[$field['CQH_LEV']] = $config['lev'];
				self::$list[] = $u;
				unset($data[$k]);
				//递归查询被删的第一级查询的分类的所有子分类并存入
				self::get_select_list($data, $u[$field['CQH_CAT_ID']], $u[$field['CQH_LEV']]);
			}
		}
		//开启了排序就将结果按排序处理
		if ($sort['is_sort']) {
			return self::array_sort(self::$list);
		}
		return self::$list;
	}

	private static function array_order($pid) {
		$config = self::$conf;
		$field = $config['field'];
		$data = self::$list;
		$maxkey = count($data) - 1; //本身的key
		if ($data[$maxkey - 1][$field['CQH_PARENT_ID']] != $pid) {
			return;
		}
		for ($i = $maxkey; $i >= 0; $i--) {
			if ($data[$i][$field['CQH_PARENT_ID']] != $pid) {
				unset(self::$list[$i]);
				$array_px = array_slice($data, $i + 1, count($data) - 1 - $i);
				$res = self::array_sort($array_px);
				foreach ($res as $t) {
					self::$list[] = $t;
				}

				break;
			}
		}

	}

	/** 递归查询并存入所有子分类
	 * @param array $data  本次的处理数据
	 * @param int   $pid   本次查询的父id
	 * @param int   $lev   上级层级数
	 * @author chenqianhao <68527761@qq.com>
	 */
	private static function get_select_list($data, $pid, $lev) {
		//简化参数
		$config = self::$conf;
		$field = $config['field'];

		foreach ($data as $key => $value) {
			if ($value[$field['CQH_PARENT_ID']] == $pid) {
				//同上
				$value[$field['CQH_LEV']] = $lev + 1;
				self::$list[] = $value;
				unset($data[$key]);
				self::get_select_list($data, $value[$field['CQH_CAT_ID']], $value[$field['CQH_LEV']]);
			}
		}
	}

	/** 排序
	 * @param $data
	 * @return mixed
	 */
	private static function array_sort($data) {
		//简化参数
		$config = self::$conf;
		$newdata = array();
		$orders = array();
		$order = $config['sort']['order'];
		$orderby = $config['sort']['orderby'] == 'ASC' ? SORT_ASC : SORT_DESC;

		foreach ($data as $k => $v) {
			$orders[] = $v[$order];
		}
		//dd($data);exit;
		$type = SORT_NUMERIC;
		array_multisort($orders, $orderby, $type);
		foreach ($data as $k => $v) {
			foreach ($orders as $k1 => $v1) {
				if ($v[$order] == $v1) {
					$data[$k1] = $v;
				}

			}

		}
		return $data;
	}

}
