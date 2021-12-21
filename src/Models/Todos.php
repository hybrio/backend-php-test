<?php

namespace Models;

use Doctrine\DBAL\Connection;

class Todos
{

    protected $db;
    public $page_limit;

    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->page_limit = 20; //set pagination limit for todos to 20
    }

    public function get_todo($id)
    {
        $sql = "SELECT * FROM todos WHERE id = ?";
        $todo = $this->db->fetchAssoc($sql, array($id));
        return $todo;
    }

    public function get_page($user, $page)
    {
        //get todos from page
        $user_id = $user['id'];
        $page_offset = ($page - 1) * $this->page_limit;
        $sql = "SELECT * FROM todos WHERE user_id = $user_id LIMIT $page_offset, $this->page_limit";
        $todos = $this->db->fetchAll($sql);
        return $todos;
    }

    public function get_total_pages($user)
    {
        //get a count of all todos
        $sql = $sql = "SELECT COUNT(*) FROM todos WHERE user_id = '${user['id']}'";
        $query_result = $this->db->fetchAssoc($sql);
        //divide by number of todos per page and round up
        $total_pages = ceil($query_result['COUNT(*)'] / $this->page_limit);
        return $total_pages;
    }

    public function add_todo($user, $description)
    {
        $user_id = $user['id'];
        $sql = "INSERT INTO todos (user_id, description) VALUES ('$user_id', ?)";
        $this->db->executeUpdate($sql, array($description));
    }

    public function toggle_completed($id)
    {
        $sql = "UPDATE todos SET completed = !completed WHERE id = ?";
        $this->db->executeUpdate($sql, array($id));
    }

    public function delete_todo($id)
    {
        $sql = "DELETE FROM todos WHERE id = ?";
        $this->db->executeUpdate($sql, array($id));
    }
}
