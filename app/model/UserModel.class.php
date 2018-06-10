<?php

class UserModel extends Model
{

    protected $table_name = 'Users';

    public function check_bylogin($user_name, $password)
    {
        $row = $this->field('accountname, password, id, nickname, email')
            ->where("accountname='$user_name'")
            ->find();

        if ($row['password'] == $password) {
            return $row;
        }
    }

    public function updateUserById($data, $where)
    {
        $feedback = $this->where('id=' . $where)->update($data);

        return $feedback;
    }

    public function updateAccessTypeById($data, $where)
    {
        $feedback = $this->where('id=' . $where)->update($data);

        return $feedback;
    }

    public function getUserRecords()
    {
        $userRecords = $this->field(
            'id,
            accountname,
            nickname,
            password,
            email'
        )->select();

        $userRecordsWithId = [];
        foreach ($userRecords as $key => $val) {
            $userRecordsWithId[$val['id']] = $val;
        }

        return $userRecordsWithId;
    }
}
