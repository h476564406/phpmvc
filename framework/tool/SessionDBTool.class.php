<?php
//我们重写的只是存储机制而不是整个session机制，不用管sessionid是如何确定的。执行read的时候肯定是已经确定了sessionid的，session机制会帮我们做好。
//在logout后重新登录，session_id一样（因为浏览器没有关闭），只是captcha会发生变化。

class SessionDBTool
{
    private $db; //MySQLDB类的对象
    public function __construct()
    {
        // session_set_save_handler应该在session_start()之前执行
        //这里6个方法并没有执行。什么时候执行，哪些不执行，执行先后顺序，取决于有没有需要
        ini_set('session.save_handler', 'user'); //不加也可以，严谨的话可以加上
        session_set_save_handler(
            //这6个方法顺序不能变，比如session想要读的时候一定会调用第三个方法
            // 'sess_open'直接这样写就变函数了，要用类/对象的方法得用数组形式！写一个数组就表示它是一个方法，直接写字符串就表示是函数
            //如果是类调用，例子spl_autoload_register(array('Framework','my_autoload'));
            //是类调用还是对象调用要看这个方法是静态还是非静态方法
            array($this, 'sess_open'),
            array($this, 'sess_close'),
            array($this, 'sess_read'),
            array($this, 'sess_write'),
            array($this, 'sess_destroy'),
            array($this, 'sess_gc')
        );
        session_start();

    }

    // 在session开启时执行，负责完成session存储所需要资源的初始化工作！

    public function sess_open()
    {
        // echo'open<br/>';
        // 要求在open时，初始化一个db对象！直接用类调用公共静态方法，公共方法可在本类外调用！
        $this->db = MYSQLDB::getInstance($GLOBALS['config']['database']);
    }

    //在整体session存储机制结束时，负责释放占用的资源！
    public function sess_close()
    {
        //php脚本结束php会自己销毁全部资源，close的时候基本上已经脚本结束了，因此形式上写写就可以了
        return true;
    }

    // 在session_start()开启sessions时执行
    //php的session机制负责传参，谁调用谁传参，我们只负责定义！
    public function sess_read($sess_id)
    {
        //改成fetchColum试试
        $row = $this->db->query("select sess_data from session where sess_id='$sess_id'");
        return (string) $row[0]['sess_data'];
    }
    // 脚本结束的时候开始写  sess_id不存在则插入，存在则更新 on duplicate key
    public function sess_write($sess_id, $sess_data)
    {
        $expire = time();
        /*$arr=['sess_id'=>$sess_id],'sess_data'=>$sess_data];
        $this->db->add($arr);*///不适合于复杂操作
        $sql = "insert into session values ('$sess_id', '$sess_data', $expire) on duplicate key update sess_data='$sess_data', expire=$expire";
        return $this->db->exe($sql);
    }

    // 在调用了 session_destroy()系统函数时，被自动调用
    // 负责的功能时，利用当前id，删除当前的session记录，删除一条session记录！

    public function sess_destroy($sess_id)
    {
        $cart = CartTool::getCart();
//            $arr=$cart->getAllItems();
        //            var_dump($arr);die;
        $cartinfo = serialize($cart->getAllItems());
        if ($this->db->exe("insert into it_usersession (`user_id`,`sess_id`,`sess_cart`)  values
({$_SESSION['user_id']},'$sess_id','$cartinfo') on duplicate key update sess_id='$sess_id', sess_cart='$cartinfo';")) {
            return $this->db->exe("delete from  session where sess_id='{$sess_id}'");
        }

        //else
        //    die('destroy 失败！');
    }

    // 垃圾回收是一次性删除全部符合条件的记录而session_destory是删除一条记录

    public function sess_gc($ttl)
    {
        $last_time = time() - $ttl; //按现在的时间算一下应该清理session的时间点
        //如果上一次存入或者改动的时间<应该清理session的时间点，那么执行清理。
        return $this->db->exe("delete from  session where expire < {$last_time}");

    }

}
