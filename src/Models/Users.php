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

    public function get_user($username, $plain_text_password)
    {
        $sql = "SELECT * FROM users WHERE username = ? and password = SHA2(?, 256)";
        $user = $this->db->fetchAssoc($sql, array($username, $plain_text_password));
        return $user;
    }
}
