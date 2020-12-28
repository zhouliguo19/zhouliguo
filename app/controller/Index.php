<?php
namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\Request;
use app\index\model\ShopUserLucky_2019_618 as ShopUserLucky;
use PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
class Index extends BaseController
{
    public function index()
    {
        echo phpinfo();
    }

    public function down(){
        $shop =new ShopUserLucky();
        $map=[];
        $map['activity_type']=['like','2020.1212'];
        $data = $shop->getlist($map);
        $table_head= array('id','订单编号','姓名','单位','单位id','来源','活动类型','中奖说明','抽奖时间');
        $arr_a=array();
        foreach ($data as $row => $col){
            $arr_a[$row][] = $col['id'];
		    $arr_a[$row][] = $col['djbh'];
		    $arr_a[$row][] = $col['contact'];
		    $arr_a[$row][] = $col['enterprise'];
		    $arr_a[$row][] = $col['dwid'];
		    $arr_a[$row][] = $col['source'];
		    $arr_a[$row][] = $col['activity_type'];
		    $arr_a[$row][] = $col['record'];
		    $arr_a[$row][] = $col['create_time'];
        }
        
        downloadExcel($arr_a,$table_head,'Report','Xls');
    }
    public function setprice(){
        return View::fetch();
    }
}
