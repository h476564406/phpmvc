<?php

class CartTool
{

    private static $instance = null;
    private $items;

    private function __construct()
    {
        $this->items = array();
    }

    private function __clone()
    {
    }

    public static function getCart()
    {
        // 看session有没有开启,并开启session
        if (!session_id()) {
            session_start();
        }

        // 看session里有没有cart对象
        if (!isset($_SESSION['cart']) || !($_SESSION['cart'] instanceof self)) {
            $_SESSION['cart'] = self::getInstance();
        }

        return $_SESSION['cart'];
    }

    private static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function addItem($id, $num, $name, $price)
    {
        // 增加某商品时,要判断该商品在购物车是否存在,如果存在,则直接增加数量
        // 如果不存在,再新建该数据单元.
        if (array_key_exists($id, $this->items)) {
            $this->items[$id]['num'] += $num;
        } else {
            $this->items[$id] = array();
            $this->items[$id]['num'] = $num;
            $this->items[$id]['name'] = $name;
            $this->items[$id]['price'] = $price;
            $this->items[$id]['ji'] = $num * $price;
        }
    }

    public function delItem($id)
    {
        if (array_key_exists($id, $this->items)) {
            unset($this->items[$id]);
            echo 1; //表示删除成功！
        }
    }

    public function setItem($id, $num)
    {
        if (array_key_exists($id, $this->items)) {
            $this->items[$id]['num'] = $num;
            $this->items[$id]['ji'] = $num * $this->items[$id]['price'];
            return 1;
        } else {
            return 0;
        }

    }

    public function getItem($id)
    {
        if (array_key_exists($id, $this->items)) {
            return $this->items[$id];
        } else {
            return 0;
        }
    }

    // 计算购物车中商品的种类,
    public function getCount()
    {
        return count($this->items);
    }

    // 计算购物车中商品的总件数.
    public function getAll()
    {
        if (!$this->getCount()) {
            return 0;
        }

        $sum = 0;
        foreach ($this->items as $v) {
            $sum += $v['num'];
        }
        return $sum;
    }

    // 计算购物车中商品的总价格
    public function getPrice()
    {
        if (!$this->getCount()) {
            return 0;
        }
        //!表示取非
        $price = 0;
        foreach ($this->items as $v) {
            $price += $v['num'] * $v['price'];
        }

        return $price;
    }

    // 返回购物车中所有商品
    public function getAllItems()
    {
        return $this->items;
    }

    // 清空购物车
    public function emptyItems($emptys = '')
    {
        if (!$emptys) {
            $this->items = array();
        } else {
            foreach ($emptys as $value) {
                unset($this->items[$value]);
            }

        }
        echo 1; //表示删除成功！

    }

}
