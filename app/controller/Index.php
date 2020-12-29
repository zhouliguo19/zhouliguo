<?php
namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\Request;
use app\index\model\ShopUserLucky_2019_618 as ShopUserLucky;
use PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use app\index\model\Reward;
use think\facade\Db;
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
        
       // downloadExcel($arr_a,$table_head,'Report','Xls');
    }
    public function downMonth(){
        $reward =new Reward();
        $map['month'] = $this->request->param("month",date('Y-m'));
        if($map['month']==date('Y-m')){
            $data =file_get_contents("http://new.rjyiyao.com/SyncAction1/getMonthReward");
            $data = json_decode($data,true);
        }else{
            $field = 'id,month,enterprise_id,enterprise_type,enterprise_name,enterprise_contact,enterprise_phone,order_money,content_money,return_coupon';
            $data = $reward->getAlls($map,$field);
        }
        
        $table_head= array('月份','企业ID','企业类型','单位名称','单位联系人','单位电话','订单金额','满足阶梯金额','返券','下阶梯金额','下阶梯返券','下阶梯需要金额');
        $arr_a=array();
        foreach ($data as $row => $col){
		    $arr_a[$row][] = $col['month'];
		    $arr_a[$row][] = $col['enterprise_id'];
		    $arr_a[$row][] = $col['enterprise_type'];
		    $arr_a[$row][] = $col['enterprise_name'];
		    $arr_a[$row][] = $col['enterprise_contact'];
		    $arr_a[$row][] = $col['enterprise_phone'];
		    $arr_a[$row][] = $col['order_money'];
		    $arr_a[$row][] = $col['content_money'];
		    $arr_a[$row][] = $col['return_coupon'];
		    $data = $this->getNext($col['enterprise_type'],$col['order_money'],$map['month']);
		    $arr_a[$row][] = $data['next_content_money'];
		    $arr_a[$row][] = $data['next_return_coupon'];
		    $arr_a[$row][] = $data['next_need_money'];
        }
        
        downloadExcel($arr_a,$table_head,$map['month'].'月度详情','Xls');
    }
    public function setprice(){
        if($this->request->isPost()){
            $post = $this->request->post();
            $post =array_filter($post);
            if(empty($post)){
                $data['code']=401;
                $data['msg']='至少填写一个参数！';
                $data['data']=json_encode($post);
                return json($data);
            }else{
                $data['code']=200;
                $data['msg']='价格生成成功！';
                $str='';
                foreach ($post as $k => $v){
                    $str =$str.substr($k,5).'|'.$v.';';
                }
                $data['data']= substr($str,0,strlen($str)-1);;
                return json($data);
            }
        }else{
            return View::fetch();
        }
    }
    public function getNext($enterprise_type,$order_money,$month){
        $field = "reward".$enterprise_type;
        $res = Db::table("shop_reward")->where(["month"=>$month])->field($field)->find();
        $res = json_decode($res[$field],true);
        $arr=array();
        if($order_money<$res[0]['money']){
            $arr['next_content_money']=$res[0]['money'];
            $arr['next_return_coupon']=$res[0]['coupon'];
            $arr['next_need_money']=$res[0]['money']-$order_money;
            return $arr;
        }elseif($order_money>=$res[count($res)-1]['money']){
            $arr['next_content_money']=$res[count($res)-1]['money'];
            $arr['next_return_coupon']=$res[count($res)-1]['coupon'];
            $arr['next_need_money']=0;
            return $arr;
        }else{
            foreach ($res as $k=>$v){
                if($order_money<$v['money']){
                    $arr['next_content_money']=$v['money'];
                    $arr['next_return_coupon']=$v['coupon'];
                    $arr['next_need_money']=$v['money']-$order_money;
                    return $arr;
                }
                
            }
        }
       return $arr;
    }
    public function test(){
        $data =file_get_contents("http://new.rjyiyao.com/SyncAction1/getMonthReward");
        $data = json_decode($data,true);
        $table_head= array('月份','企业ID','企业类型','单位名称','单位联系人','单位电话','订单金额','满足阶梯金额','返券','下阶梯金额','下阶梯返券','下阶梯需要金额');
        $arr_a=array();
       
        foreach ($data as $row => $col){
            //$arr_a[$row][] = $col['id'];
		    $arr_a[$row][] = $col['month'];
		    $arr_a[$row][] = $col['enterprise_id'];
		    $arr_a[$row][] = $col['enterprise_type'];
		    $arr_a[$row][] = $col['enterprise_name'];
		    $arr_a[$row][] = $col['enterprise_contact'];
		    $arr_a[$row][] = $col['enterprise_phone'];
		    $arr_a[$row][] = $col['order_money'];
		    $arr_a[$row][] = $col['content_money'];
		    $arr_a[$row][] = $col['return_coupon'];
		    $data = $this->getNext($col['enterprise_type'],$col['order_money'],date('Y-m'));
		    $arr_a[$row][] = $data['next_content_money'];
		    $arr_a[$row][] = $data['next_return_coupon'];
		    $arr_a[$row][] = $data['next_need_money'];
        }
         downloadExcel($arr_a,$table_head,date('Y-m').'月度详情','Xls');
    }
}
