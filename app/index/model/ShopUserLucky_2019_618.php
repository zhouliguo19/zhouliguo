<?php
declare (strict_types = 1);

namespace app\index\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class ShopUserLucky_2019_618 extends Model
{
    protected $name = 'shop_user_lucky_2019_618';
    protected $pk = 'id';
    
    public function getlist($map){
        
        
        return $this->where($map)->select();
    }
}
