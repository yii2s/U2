<?php
class FittingModel extends Model{
    public function CkeckApp($uname,$upass){
        if($uname==C('FITTU') && $upass==C('FITTP')){
            $flag = 1;
        }else{
            $flag = 0;
        }
        return $flag;
    }
    public function GetSellercats(){
        $sell = M('Sellercats');
        $result = $sell->field('*')->where(array())->select();
        if(!empty($result) && count($result)>0){
          $arr = array('code'=>1,'data'=>$result);
          unset($result);
        }else{
          $arr = array('code'=>0,'data'=>'','msg'=>'没有数据');
        }
        return $arr;
    }
    public function GetCateGoodData($page){
        $limit = 1000;
        $offset = ($page-1)*$limit;
        $CateModel = M('Catesgoods');
        $count = $CateModel->count();
        $num_page = ceil($count/$limit);
        if($page>$num_page){
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据','page'=>0);
            return $arr;
        }
        $list = $CateModel->field('*')->where(array())->limit($offset.','.$limit)->select();
        if(!empty($list) && count($list)>0){
            $arr = array('code'=>1,'data'=>$list,'page'=>$page+1);
            unset($list);
        }else{
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据');
        }
        return $arr;
    }
    public function GetGoodsData($where=array()){
        $page = $where['page'];
        $limit = 500;
        $wh = array();
        if(!empty($where['modified'])){
            $wh['sort'] = array('EGT',$where['modified']);
        }
        $offset = ($page-1)*$limit;
        $good = M('Goods');
        $count = $good->where($wh)->count();
        $num_page = ceil($count/$limit);
        if($page>$num_page){
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据','page'=>0);
            return $arr;
        }
        $list = $good->field('id,num_iid,type,gender,isud,approve_status,item_bn,outer_id,title,num,price,pic_url,detail_url,list_time,delist_time,istag,isdoubt,sort,detailpc,isdisplay')->where($wh)->limit($offset.','.$limit)->select();
        if(!empty($list) && count($list)>0){
            foreach($list as $k=>$v){
                if($v['detailpc']!='0'){
                    $list[$k]['detailpc'] = unserialize($v['detailpc']);
                }
                $list[$k]['pic_url'] = C('UNIQLOURL').$v['pic_url'];
            }
            $arr = array('code'=>1,'data'=>$list,'page'=>$page+1);
            unset($list);
        }else{
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据');
        }
        return $arr;
    }
    public function GetSkuData($page){
        $limit = 500;
        $offset = ($page-1)*$limit;
        $products = M('Products');
        $count = $products->count();
        $num_page = ceil($count/$limit);
        if($page>$num_page){
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据','page'=>0);
            return $arr;
        }
        $list = $products->field('id,goods_id,num_iid,sku_id,cid,cvalue,properties_name,quantity,url,modified')->where(array())->limit($offset.','.$limit)->select();
        if(!empty($list) && count($list)>0){
            foreach($list as $k=>$v){
                $list[$k]['url'] = C('UNIQLOURL').$v['url'];
            }
            $arr = array('code'=>1,'data'=>$list,'page'=>$page+1);
            unset($list);
        }else{
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据');
        }
        return $arr;
    }
    public function GetRedisValue($type='d3good'){
        import("@.ORG.Reds");
        $redis = new Reds();
        $limit = 400;
        $len = $redis->llen($type);
        $list = $redis->lrange($type,0,$limit-1);
        $redis->ltrim($type,$limit,$len);
        if(!empty($list)){
        foreach($list as $k=>$v){
            $list[$k] = unserialize($v);
        }
        }
        if(!empty($list)){
            $arr = array('code'=>1,'data'=>$list);
            unset($list);
        }else{
            $arr = array('code'=>0,'data'=>'','msg'=>'没有数据');
        }
        return $arr;
    }
    public function Add3dlog($data){
        $flag = 0;
        if(is_array($data)){
            $time = date('Y-m-d H:i:s');
            $sql = "insert into `u_fitting3d_log` (`uid`,`taobao_name`,`ip`,`visittime`,`intime`,`isdown`,`fitting_time`,`gender`,`height`,`weight`,`shoulder`,`upper_arm`,`chest`,`cup`,`waist`,`hip`,`leg`,`leg_long`,`num_iid`,`goodsize`,`color`,`isbuy`,`isweibo`,`isweixin`,`createtime`) values ";
            foreach($data as $k=>$v){
                $sql.="('".$v['tid']."','".$v['taobao_name']."','".$v['ip']."','".$v['visittime']."','".$v['intime']."','".$v['isdown']."','".$v['fitting_time']."','".$v['gender']."','".$v['height']."','".$v['weight']."','".$v['shoulder']."','".$v['upper_arm']."','".$v['chest']."','".$v['cup']."','".$v['waist']."','".$v['hip']."','".$v['leg']."','".$v['leg_long']."','".$v['num_iid']."','".$v['goodsize']."','".$v['color']."','".$v['isbuy']."','".$v['isweibo']."','".$v['isweixin']."','".$time."'),";
            }
            $sql = rtrim($sql,',');
            M('Fitting3dLog')->query($sql);
        }else{
            $flag = 1;
        }
        return $flag;
    }
    public function AddTotalLog($data){
        $flag = 0;
        if(is_array($data)){
              $time = time();
              $nowtime = date('Y-m-d H:i:s',$time);
              $arr = array('fitting_num'=>$data['fitting_num'],
                           'fitting_avg_num'=>$data['fitting_avg_num'],
                           'modify_num'=>$data['modify_num'],
                           'click_buy_num'=>$data['click_buy_num'],
                           'sku_num'=>$data['sku_num'],
                           'download_num'=>$data['download_num'],
                           'log_day'=>$data['log_day'],
                           'createtime'=>$nowtime);
              M('DayLog')->add($arr);
        }else{
            $flag = 1;
        }
        return $flag;
    }
}