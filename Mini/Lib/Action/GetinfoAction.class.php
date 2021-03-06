<?php
class GetinfoAction extends Action{
        public function sexToStyle(){
            $sid = trim($this->_post('sid'));
            $fid = trim($this->_post('fid'));
            $tem = trim($this->_post('tem'));
            $page = trim($this->_request('page'));
            $sid = $sid?$sid:0;
            $fid = $fid?$fid:0;
            $page = $page?$page:1;
            $ism = is_mobile();
            if($ism){
            $page_num = 2;
            }else{
            $page_num = 4;
            }
            $start = ($page-1)*$page_num;
            /*if(S('sid'.$tem.$sid.$fid)){
             $arr = unserialize(S('sid'.$tem.$sid.$fid));
            }else{*/
                $recomodel = D('Reco');
                if(!empty($fid)){
                    $defaultwhere['suitStyleID'] = $fid;
                }
            switch($sid){
                case 3 :
                //取出默认数据
                $defaultwhere['suitGenderID'] = array('exp','IN(3,4)');
                $defaultwhere['approve_status'] = 0;
                $defaultResult = $recomodel->getBeubeu($defaultwhere,$page,$page_num,$start);
                break;
                case 4 :
                  //mini婴幼儿还是从以前商品取数据
                 $goodtag = M('Goodtag');
                 $windex = D('Windex');
                 $where = '';
                 if(isset($tem)){
                        $widvalue = $windex->getwindex($tem);
                        $where.="and g.wid in (".$widvalue['str'].")";
                 }
                 if(!empty($sid)){
                        $where.=" and g.gtype='".$sid."'";
                }
                $where.=" and bg.approve_status='onsale' and bg.num>=15";
                    $sql = "select distinct g.good_id,case when g.wid=".$widvalue['wid']." then 0 end wo, bg.num_iid,bg.type,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from `u_goodtag` as g inner join `u_goods` as bg on bg.id=g.good_id where 1 ".$where." order by wo asc,uptime desc";
                    $defaultResult = $goodtag->query($sql);
                    /*$defaultResult = '';
                    foreach($childResult as $k=>$v){
                        if($v){
                            //$defaultResult.='<li><img  data-original="'.__ROOT__.'/'.$v['pic_url'].'" id="'.$v['num_iid'].'" place="'.$gtag2['name'].'" url="'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'"></li>';
                            $defaultResult.='<li><img  data-original="http://uniqlo.bigodata.com.cn/'.$v['pic_url'].'" id="'.$v['num_iid'].'" place="'.$gtag2['name'].'" url=http://uniqlo.bigodata.com.cn/'.$v['detail_url'].'" rest="'.$v['num'].'" price="'.$v['price'].'" alt="'.$v['title'].'"></li>';
                        }
                    }*/
                break;
                case 1 :
                case 2 :
                $defaultwhere['suitGenderID'] = $sid;
                $defaultwhere['approve_status'] = 0;
                $defaultResult = $recomodel->getBeubeu($defaultwhere,$page,$page_num,$start);
                break;
            }
            $arr['page'] = $defaultResult['page'];
            $arr['prepage'] = $page;
            $arr['count'] = $defaultResult['count'];
            $arr['def'] = $defaultResult['result'];
          //S('sid'.$tem.$sid.$fid,serialize($arr),array('type'=>'file'));
          //}
            $this->ajaxReturn($arr, 'JSON');
        }

    public function getGS($where){
           $list = M('SettingsGenderStyle')->cache(true)->join('inner join u_settings_suit_style as sss on u_settings_gender_style.styleID=sss.ID')->distinct(true)->field('sss.ID')->where($where)->select();
           return $list;
    }

    //中间点击分类获取所对应得风格和自定义分类
    public function midsexToStyle(){
        $sid = trim($this->_request('sid'));
        $sid = $sid?$sid:0;
        if(S('midsid'.$sid)){
         $arr = unserialize(S('midsid'.$sid));
        }else{
        $recomodel = D('Reco');
        if($sid!=4 && $sid!=0){
            $where['gender'] = $sid;
            $where['selected'] = 1;
            $where['shortName'] = array('neq','');
            $where['isshow']= 0;
            $ucuslist  = $recomodel->getCusData2($where);//上装
            //$ucuslist  = $recomodel->getCusData($where);//上装
            $arr['u'] = $ucuslist;
        }else if($sid==4){
            $where['gender'] = array('exp','in(4,5)');
            $where['selected'] = 1;
            $where['shortName'] = array('neq','');
            $where['isshow']= 0;
            $babylist  = $recomodel->getCusData2($where);//上装
            $arr['u'] = $babylist;
        }else if($sid==0){
            $where = array('selected'=>1,'shortName'=>array('neq',''),'isshow'=>0);
            $ucuslist = $recomodel->getCateList2($where);
            $arr['u'] = $ucuslist;
        }
        S('midsid'.$sid,serialize($arr),array('type'=>'file'));
        }
        $this->ajaxReturn($arr, 'JSON');
    }

    //给jack返回款号和色号
    public function getCidItembn(){
        $suitid = trim($this->_post('suitid'));
        if($suitid>0){
            if(S('ciditembn'.$suitid)){
                $result = unserialize(S('ciditembn'.$suitid));
            }else{
                $suites = M('Suits');
                $sql = "SELECT concat(left(u_beubeu_goods.item_bn,8) , u_suits_goodsdetail.cid) AS barcode
 from u_suits INNER JOIN u_suits_goodsdetail on u_suits.suitID = u_suits_goodsdetail.suitID
INNER JOIN u_beubeu_goods on u_suits_goodsdetail.num_iid = u_beubeu_goods.num_iid
where u_suits.approve_status=0 and u_suits.suitID = ".$suitid;
                $result = $suites->query($sql);
                S('ciditembn'.$suitid,serialize($result),array('type'=>'file'));
            }
            if(!empty($result)){
            $returnArr = array('code'=>1,'data'=>$result);
            }else{
           $returnArr = array('code'=>0,'msg'=>'没有数据');
            }
        }else{
            $returnArr = array('code'=>0,'msg'=>'参数错误');
        }
        $this->ajaxReturn($returnArr, 'JSON');
    }

    //给jack返回num_iid
    public function getJackNumiid(){
        $item_bn = trim($this->_post('item_bn'));
        if(!empty($item_bn)){

           $goods = M('Goods');
           $sql = "select num_iid,title,approve_status,IF(num>0 and approve_status='onsale',detail_url,'') as detail_url,num from u_beubeu_goods where item_bn like '".$item_bn."%' order by approve_status DESC,num desc limit 0,1";
           $result = $goods->query($sql);
           if(!empty($result[0])){
                 $returnArr = array('code'=>1,'data'=>$result[0]);
           }else{
                 $returnArr = array('code'=>0,'msg'=>'没有数据');
           }
        }else{
            $returnArr = array('code'=>0,'msg'=>'参数错误');
        }
        $this->ajaxReturn($returnArr, 'JSON');
    }
//个人中心
public function getCollData(){
    $page = trim($this->_request('page'));
    $page = ($page>0) ? $page : 1;
    $page_num = 4;
    $start = ($page-1)*$page_num;
    $recomodel = D('Reco');
    $where['uid'] = session("uniq_user_id");
    $defaultResult = $recomodel->getBenebnColl($where,$page,$page_num,$start);
    if($page==1){
       //获取用户信息
        $userinfo = $recomodel->getUserInfo();
        $arr['uname'] = $userinfo[0];
        $arr['collflag'] = $userinfo[1];
        $arr['collcount'] = $userinfo[2];
        $arr['tname'] = $userinfo[3];
    }
    $arr['page'] = $defaultResult['page'];
    $arr['def'] = $defaultResult['result'];
    $this->ajaxReturn($arr, 'JSON');
}
public function delBeubenColl(){
   $id = trim($this->_request('id'));//百衣收藏id
    $uid = session("uniq_user_id");
   if(!empty($uid)){
       $beubeu_coll = M('BeubeuCollection');
       $res = $beubeu_coll->where(array('id'=>$id))->delete();
       if($res){
          M('BeubeuCollGoods')->where(array('bcid'=>$id))->delete();
           $arr['code'] = 1;
           $arr['msg'] = '删除成功';
       }else{
           $arr['code'] = 0;
           $arr['msg'] = '参数错误';
       }
   }else{
    $arr['code'] = 0;
    $arr['msg'] = '没有登录';
   }
    $this->ajaxReturn($arr, 'JSON');
}
public function setCollFlag(){
    $uid = session("uniq_user_id");
    if($uid){
    $user = M('User');
    $result = $user->field('id,collflag')->where(array('id'=>$uid))->find();
    if(!empty($result)){
        if($result['collflag']==0){
        $re = $user->where(array('id'=>$uid))->save(array('collflag'=>1));
        if($re){
        $arr['code'] = 1;
        $arr['msg'] = '领取成功';
        }else{
        $arr['code'] = 0;
        $arr['msg'] = '领取失败';
        }
       }else{
            $arr['code'] = 0;
            $arr['msg'] = '已经领取';
        }
    }else{
        $arr['code'] = 0;
        $arr['msg'] = '此用户不存在';
    }
    }else{
        $arr['code'] = 0;
        $arr['msg'] = '没有登录';
    }
    $this->ajaxReturn($arr, 'JSON');
}
public function addBeubenColl(){
    //添加beubeu收藏
    $uid = session("uniq_user_id");
    if($uid){
        $headpic = trim($this->_post('headpic'));
        $bodypic = trim($this->_post('bodypic'));
        $shoespic = trim($this->_post('shoespic'));
        $clothespic = trim($this->_post('pic_match'));
        $gender = trim($this->_post('gender'));
        $suitid = trim($this->_post('suitid'));
        $uq = trim($this->_post('uq'));
        $num_iid = $this->getCollNumm_iid($uq);
        $beuben = M('BeubeuCollection');
        if(!empty($suitid) && !empty($clothespic)){
        $count = $beuben->field('id')->where(array('uid'=>$uid))->count();
        if($count<50){
           $time = date('Y-m-d H:i:s');
           $data = array('uid'=>$uid,
                         'gender'=>$gender,
                         'suitID'=>$suitid,
                         'pic_head'=>$headpic,
                         'pic_body'=>$bodypic,
                         'pic_shoes'=>$shoespic,
                         'pic_clothes'=>$clothespic,
                         'createtime'=>$time);
           $res = $beuben->add($data);
           if($res>0){
                   $insql = "insert into `u_beubeu_coll_goods` (`bcid`,`num_iid`,`uq`) values ";
                   $str = '';
                   if(!empty($num_iid)){
                   foreach($num_iid as $k=>$v){
                    if($v){
                        $str.="('".$res."','".$v[0]."','".$v[1]."'),";
                    }
                   }
                   $str = rtrim($str,',');
                   $insql.=$str;
                   $beuben->query($insql);
                }
                   $arr['code'] = 1;
                   $arr['msg'] = '已收藏至您的个人衣柜';
           }else{
               $arr['code'] = 0;
               $arr['msg'] = '收藏失败';
           }
        }else{
            $arr['code'] = 0;
            $arr['msg'] = '一个用户最多只能收藏50套';
        }
    }else{
            $arr['code'] = 0;
            $arr['msg'] = '请先搭配';
        }
    }else{
        $arr['code'] = 0;
        $arr['msg'] = '登录之后即可收藏此套搭配';
    }
    $this->ajaxReturn($arr, 'JSON');
}
public function getUserInfo(){
      $uid = session("uniq_user_id");
      if($uid){
        $result = M('User')->field('user_name,mobile,taobao_name,login_type')->where(array('id'=>$uid))->find();
        if(!empty($result)){
            $arr['code'] = 1;
            $arr['result'] = $result;
        }else{
            $arr['code'] = 0;
            $arr['msg'] = '没有此用户';
        }
      }else{
           $arr['code'] = 0;
           $arr['msg'] = '没有登录';
      }
    $this->ajaxReturn($arr, 'JSON');
}
public function changeName(){
    //修改user_name
    $uid = session("uniq_user_id");
    if($uid){
        $user = M('User');
        $uname = trim($this->_post('uname'));
        $result = $user->field('mobile,taobao_name')->where(array('id'=>$uid))->find();
        $user_name = session("uniq_user_name");
        if(!empty($result)){
           $re = $user->where(array('id'=>$uid))->save(array('user_name'=>$uname));
           if($re){
               $arr['code'] = 1;
               $arr['msg'] = '编辑成功';
           }else{
               $arr['code'] = 0;
               $arr['msg'] = '编辑失败';
           }
        }else{
            $arr['code'] = 0;
            $arr['msg'] = '用户信息不匹配';
        }
    }else{
      $arr['code'] = 0;
      $arr['msg'] = '没有登录';
    }
    $this->ajaxReturn($arr, 'JSON');
}
public function changeTaoName(){
        //修改淘宝账号
        $uid = session("uniq_user_id");
        if($uid){
            $user = M('User');
            $tname = trim($this->_post('taobao_name'));
            $result = $user->field('mobile,taobao_name,login_type')->where(array('id'=>$uid))->find();
            if(!empty($result)){
                if($result['login_type']=='normal'){
                    session("uniq_user_name",$tname);
                    cookie('uniq_user_name',$tname,604800);
                   $map = array('user_name'=>$tname,'taobao_name'=>$tname);
                }else{
                   $map = array('taobao_name'=>$tname);
                }
                $re = $user->where(array('id'=>$uid))->save($map);
                if($re){
                    $arr['code'] = 1;
                    $arr['tname'] = $tname;
                    $arr['login_type'] = $result['login_type'];
                    $arr['msg'] = '编辑成功';
                }else{
                    $arr['code'] = 0;
                    $arr['msg'] = '关联淘宝登录名没有变化';
                }
            }else{
                $arr['code'] = 0;
                $arr['msg'] = '用户信息不匹配';
            }
        }else{
            $arr['code'] = 0;
            $arr['msg'] = '没有登录';
        }
    $this->ajaxReturn($arr, 'JSON');
    }

    public function getCollNumm_iid($uq){
        //通过uq获取num_iid
        if(!empty($uq)){
            $goods = M('Goods');
            $is_g = is_int(strpos($uq,'_'));
            if(!$is_g){
                $uq = $uq.'_';
            }
            $arr_uq = explode('_',$uq);$arr = array();
            foreach($arr_uq as $k=>$v){
                if($v){
                    $uv = substr($v,0,8);
                    $sql = "select `num_iid` from `u_goods` where item_bn like '".$uv."%' order by num desc";
                    $result = $goods->query($sql);$uqArr = array();
                    $uqArr[] = $result[0]['num_iid'];$uqArr[] = $v;
                    $arr[] = $uqArr;
                }
            }
            return $arr;
        }
    }
}