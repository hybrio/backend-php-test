<?php

namespace Models;

use Doctrine\DBAL\Connection;

class Users
{

    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function get_user($username, $password)
    {
        $sql = "SELECT * FROM users WHERE username = '$username' and password = '$password'";
        $user = $this->db->fetchAssoc($sql);
        return $user;
    }
}
