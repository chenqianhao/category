本扩展使用与分类相似是数据进行层级输出排序的数据

## 使用
* composer require chenqianhao/category

~~~
<?php

require 'vendor/autoload.php';

$conf=array(
	    //此数据自己查询
        'data' => $data,   
        //默认初始查询的父id
        'pid' => 0,
        //默认初始层级
        'lev' => 1,
        //排序
        'sort'=> array(
            //排序开关
            'is_sort' => false,
            //排序字段
            'order'=> 'sort',
            //排序方式
            'orderby' => 'ASC',
        ),
        //字段设置
        'field'=> array(
            //表中父id字段名
            'CQH_PARENT_ID' => 'parent_id',
            //表中分类id字段名
            'CQH_CAT_ID'   => 'cat_id',
            //添加的层级字段
            'CQH_LEV'   => 'lev',
        )
);    

var_dump(\cqh\category\CateGoryTree::get_cat_list($config));

?>
~~~