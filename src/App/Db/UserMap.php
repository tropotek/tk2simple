<?php
namespace App\Db;

/**
 * Class UserMapper
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class UserMap extends \Tk\Db\Mapper
{


    public function unmap($obj)
    {
        $arr = array(
            'id' => $obj->id,
            'username' => $obj->username,
            'password' => $obj->password,
            'name' => $obj->name,
            'group' => $obj->group,
            'active' => (int)$obj->active,
            'modified' => $obj->modified->format('Y-m-d H:i:s'),
            'created' => $obj->created->format('Y-m-d H:i:s')
        );
        return $arr;
    }


    public function map($row)
    {
        $obj = new User();
        $obj->id = $row['id'];
        $obj->username = $row['username'];
        $obj->password = $row['password'];
        $obj->name = $row['name'];
        $obj->group = $row['group'];
        $obj->active = ($row['active'] == 1);
        if ($row['modified'])
            $obj->modified = new \DateTime($row['modified']);
        if ($row['created'])
            $obj->created = new \DateTime($row['created']);
        return $obj;
    }



    public function findByUsername($username)
    {
        return current($this->select(array('username' => $username)));
    }


}