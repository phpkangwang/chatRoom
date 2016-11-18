<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Class M_account
 * 用于进行账号相关的数据库操作
 */
class User_Model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }
    
    function login($username,$password)
    {
        $this->db->where('username =', $username);
        $this->db->where('password =', $password);
        return $this->db->from('user')->get()->row_array();
    }

    /**
     * 进行注册操作，会插入数据库
     * @param $postData
     * @return mixed    错误码
     */
    function regist($post)
    {
        $table = "user";
        $post['created_at'] = time();
        if($this->db->insert($table, $post)){
             $id = $this->db->insert_id();
             $result = $this->db->from($table)->where('id =', $id)->get()->row_array();
             return $result;
        }else{
            return ;
        }
    }

    
}