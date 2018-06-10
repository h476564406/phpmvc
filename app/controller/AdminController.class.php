<?php

class AdminController extends BackPlatformController
{

    private $usermodel;

    public function __construct()
    {
        // Call parent construct, every time running code,
        // should start session and check login status.
        parent::__construct();
        $this->usermodel = new UserModel;
    }

    public function signupAction()
    {
        if (empty($_POST)) {
            require VIEW_DIR . 'signup.html';
        } else {
            $data['accountname'] = $_POST['accountname'];
            $data['password'] = $_POST['password'];

            if ($this->usermodel->autoInsert($data)) {
                $_SESSION['user_login'] = 'yes';

                $this->jump('index.php?c=Admin&a=manage', 'Sign up successfully!');
            } else {
                $this->jump('index.php?c=Admin&a=signup', 'Sign up failed!');
            }
        }
    }

    public function loginAction()
    {
        if (empty($_POST)) {
            require VIEW_DIR . 'login.html';
        } else {
            if ($user_info = $this->usermodel->check_bylogin($_POST['accountname'], $_POST['password'])) {
                $_SESSION['user_login'] = 'yes';

                $res = ['status' => 'success', 'url' => '?c=Admin&a=manage'];
            } else {

                $res = ['status' => 'fail', 'info' => 'Sign in failed! Accountname or password wrong!'];
            }
            echo json_encode($res);
        }
    }

    public function logoutAction()
    {
        $_SESSION = [];
        // 如果要清理的更彻底，那么同时删除会话 cookie
        // 注意：这样不但销毁了会话中的数据，还同时销毁了会话本身
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            // session_name PHPSESSID
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        // session_destroy() 销毁当前会话中的全部数据， 但是不会重置当前会话所关联的全局变量， 也不会重置会话 cookie。 如果需要再次使用会话变量， 必须重新调用 session_start() 函数。
        session_destroy();
        $this->jump('index.php?c=Index&a=index', 'You Logged out!');
    }

    public function insertAction()
    {
        $data['accountname'] = $_POST['accountname'];
        $data['nickname'] = $_POST['nickname'];
        $data['password'] = $_POST['password'];
        $data['email'] = $_POST['email'];

        if ($this->usermodel->autoInsert($data)) {
            $this->jump('index.php?c=Admin&a=manage', 'Insert successfully!');
        } else {
            $this->jump('index.php?c=Admin&a=manage', 'Insert failed!');
        }
    }

    public function updateAction()
    {
        $where = $_POST['id'];
        $data['accountname'] = $_POST['accountname'];
        $data['nickname'] = $_POST['nickname'];
        $data['password'] = $_POST['password'];
        $data['email'] = $_POST['email'];

        if ($this->usermodel->updateUserById($data, $where)) {
            $this->jump('index.php?c=Admin&a=manage', 'Update successfully!');
        } else {
            $this->jump('index.php?c=Admin&a=manage', 'Update failed!');
        }
    }

    public function deleteAction()
    {
        if ($this->usermodel->autoDelete($_POST['id'])) {
            $this->jump('index.php?c=Admin&a=manage', 'Delete successfully!');
        } else {
            $this->jump('index.php?c=Admin&a=manage', 'No record affected.');
        }
    }

    public function manageAction()
    {
        var_dump($_SESSION);
        $userRecords = $this->usermodel->getUserRecords();

        $userRecordsStr = json_encode($userRecords);

        require VIEW_DIR . 'manage.html';
    }
}
