<?php
namespace app\index\model;


use think\Model;

class Reward extends Model
{
    //
  
    protected $name = 'reward_return';
 
    public function getLists(&$map,$field,$page,$pageSize){
        $res = self::where($map)
            ->field($field)
            ->paginate(['list_rows' => $pageSize, 'page' => $page])
            ->toArray();
        return $res;
    }
    public function getAlls(&$map,$field){
        $res = self::where($map)
            ->field($field)->select();
        return $res;
    }
}