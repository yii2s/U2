<?php
// 优衣库mini站,author:kimi
class IndexAction extends Action {
    public $uniq_user_name;
    public function index(){
		$ism = is_mobile();
		if($ism){
         $this->redirect('Mobile/index');
		}
        if(cookie('uniq_user_name') && cookie('uniq_user_id')){
            session("uniq_user_name",cookie('uniq_user_name'));
            session("uniq_user_id",cookie('uniq_user_id'));
        }
        $this->uniq_user_name =  session("uniq_user_name");
        $user = M('User');
        $is_allow_register = 0;
        $is_mobile_active = 0;
        $mobile = '';
        $nick = session("uniq_user_id");
        $result = $user->where(array('id'=>$nick))->find();
        if($result['login_type']!='normal'){
            $this->uniq_user_name = $result['user_name'];
        }
        $is_mobile_active = $result['is_active'];
        $mobile = $result['mobile'];
        $goods = M('Goods');
        $goodtag = M('Goodtag');
        $time = date('Y-m-d H:i:s');
        $love = M('Love');
        $buy = M('Buy');
        $suit_style = M('SettingsSuitStyle');
        $beubeu_suits = M('BeubeuSuits');
        $recomodel = D('Reco');
        if(empty($result)){
            $u_id = 0;
        }else{
            $u_id = $result['id'];
        }
        $_SESSION['u_id'] = $u_id;
        //kimi 优衣库二期
        //默认女士上下装自定义分类,这个不能删
        if(S('cust11')){
            $ucuslist = unserialize(S('cust11'));
        }else{
            $where = array('selected'=>1,'shortName'=>array('neq',''),'isshow'=>0);
            $ucuslist  = $recomodel->getCateList2($where);//自定义分类
            S('cust11',serialize($ucuslist),array('type'=>'file'));
        }
        $this->assign('ucuslist',$ucuslist);
        //优衣库二期
        $this->assign('nick',$nick);
        $this->assign('newstore',C('NEWSRORE'));
        $this->assign('cityn',cookie('cityn'));
        $this->assign('provi',cookie('pro'));
        $this->assign('uniurl',C('UNIQLOURL'));
        $this->assign('uniq_user_name',$this->uniq_user_name);
        $this->assign('is_allow_register',$is_allow_register);
        $this->assign('is_mobile_active',$is_mobile_active);
        $this->assign('mobile',$mobile);
        $this->assign('user',$result);
        $this->assign('dsn','http://'.$_SERVER['HTTP_HOST']);
        $this->assign('basedir',__ROOT__);
        $this->display();
        //}
    }


    public function loginout(){
        /*$user = M('user')->where(array('id'=>session("uniq_user_id")))->find();
        if($user['is_active']){
            M('Collection')->where(array('uid'=>session("uniq_user_id")))->save(array('is_delete'=>1));
        }else{
            M('Collection')->where(array('uid'=>session("uniq_user_id")))->delete();
        }*/
        $_SESSION=array();
        if(isset($_COOKIE[session_name()])){
            setCookie(session_name(), '', time()-100, '/');
        }
        cookie('uniq_user_name',null);
        cookie('uniq_user_id',null);
        session_destroy();
        if(is_mobile()){
           $this->redirect('Mobile/index');
        }else{
           $this->redirect('Index/index');
        }
    }

//删除
    public function delg(){
        $id = trim($this->_post('id'));//Collectio表的num_iid
        if($id>0){
            M('Collection')->where(array('num_iid'=>$id,'uid'=>session("uniq_user_id")))->delete();
        }
    }
//喜欢
    public function addlove(){
        $id = trim($this->_post('id'));//Collectio表的num_iid
        $flag = trim($this->_post('flag'));
        $isdel = trim($this->_post('isdel'));
        if($id>0){
            $uid = session("uniq_user_id");
            if(!empty($uid)){
                if($flag==1){
                    $love = M('Love');
                    $time = date('Y-m-d H:i:s');
                    if($isdel==1){
                        $cresult = $love->field('id')->where(array('num_iid'=>$id,'uid'=>$uid))->find();
                        if(empty($cresult)){
                            $love->add(array('num_iid'=>$id,'uid'=>$uid,'cratetime'=>$time));
                        }
                    }else if($isdel==0){
                        $love->where(array('num_iid'=>$id,'uid'=>$uid))->delete();
                    }
                }else if($flag==2){
                    $buy = M('Buy');
                    $time = date('Y-m-d H:i:s');
                    if($isdel==1){
                        $cresult = $buy->field('id')->where(array('num_iid'=>$id,'uid'=>$uid))->find();
                        if(empty($cresult)){
                            $buy->add(array('num_iid'=>$id,'uid'=>$uid,'cratetime'=>$time));
                        }
                    }else if($isdel==0){
                        $buy->where(array('num_iid'=>$id,'uid'=>$uid))->delete();
                    }
                }
            }else{
                $returnArr = array('code'=>0,'msg'=>'没有登录');
                $this->ajaxReturn($returnArr, 'JSON');
            }
        }
    }

//点击按钮取数据
    public function getgood(){
        if($this->_request('tem')){
            $tem = trim($this->_request('tem'));//平均温度
        }
        $root_dir = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
        $sid = trim($this->_request('sid'));//性别id形如1,2,3 all为0
        $lid = trim($this->_request('lid'));//收藏id
        $bid = trim($this->_request('bid'));//购买id
        $fid = trim($this->_request('fid'));//风格id
        $zid = trim($this->_request('zid'));//自定义分类
        $kid = trim($this->_request('kid'));//快速搜索标记
        $page = trim($this->_request('page'));
        $keyword = trim($this->_request('keyword'));
        $timestamp = trim($this->_request('timestamp'));
        $oid = trim($this->_request('oid'));//排序id
        if($this->_request('tem') && $this->_request('tem')<=-10){
            $tem = -10;
        }
        $lid = $lid?$lid:0;
        $sid = $sid?$sid:0;
        $bid = $bid?$bid:0;
        $fid = $fid?$fid:0;
        $zid = $zid?$zid:0;
        $kid = $kid?$kid:0;
        $page = $page?$page:1;
        $oid = $oid?$oid:2;
        $goodtag = M('Goodtag');
        $windex = D('Windex');
        $page_num = 25;
        $start = ($page-1)*$page_num;
        if(isset($tem)){
            $widvalue = $windex->getwindex($tem);
        }
        $ostr = $windex->getOrderStr($oid);
        $productSyn = D('ProductSyn');
        $uid = session("uniq_user_id");
        $love = M('Love');
        $buy = M('Buy');
        if($lid==1 && $bid==1){
            $sql = "
select bg.num_iid,li.loveid,li.buyid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url
      from u_beubeu_goods bg ,
(SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid={$uid}
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid={$uid}
) bl group by bl.num_iid) li
where bg.num_iid = li.num_iid order by ".$ostr." limit ".$start.",".$page_num;
            $result = M('BeubeuGoods')->query($sql);
            if(!empty($result)){
                foreach($result as $k1=>$v1){
                    $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                    $productsValue =  $productSyn->GetProductColorByID($v1['num_iid']);
                    $windex->Get32Pic($productsValue,$root_dir);
                    $result[$k1]['products'] = $productsValue;
                    $result[$k1]['tuijian'] = $windex->GetTuijian($v1['item_bn'],$v1['num_iid']);
                }
            }else{
                $result = array();
            }
            if($page==1){
                $parmas = array('oid'=>$oid);
                if(is_mobile()){
                    $result = $this->waterdataMobile($result,$lid,$bid,$keyword,$parmas);
                }else{
                    $result = $this->waterdata($result,$lid,$bid,$keyword,$parmas);
                  }
            }
        }else if($lid==1 && $bid!=1){
            $sql = "
select bg.num_iid,li.loveid,li.buyid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url
      from u_beubeu_goods bg ,
(SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid={$uid}
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid={$uid}
) bl group by bl.num_iid) li
where bg.num_iid = li.num_iid and li.loveid is not null order by ".$ostr." limit ".$start.",".$page_num;
            $result = M('BeubeuGoods')->query($sql);
            if(!empty($result)){
                foreach($result as $k1=>$v1){
                    $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                    $productsValue =  $productSyn->GetProductColorByID($v1['num_iid']);
                    $windex->Get32Pic($productsValue,$root_dir);
                    $result[$k1]['products'] = $productsValue;
                    $result[$k1]['tuijian'] = $windex->GetTuijian($v1['item_bn'],$v1['num_iid']);
                }
            }else{
                $result = array();
            }
            if($page==1){
                $parmas = array('oid'=>$oid);
                if(is_mobile()){
                    $result = $this->waterdataMobile($result,$lid,$bid,$keyword,$parmas);
                }else{
                    $result = $this->waterdata($result,$lid,$bid,$keyword,$parmas);
                }
            }
        }else if($bid==1 && $lid!=1){
            $sql = "
select bg.num_iid,li.loveid,li.buyid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url
      from u_beubeu_goods bg ,
(SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid={$uid}
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid={$uid}
)bl group by bl.num_iid)li
where bg.num_iid = li.num_iid and li.buyid is not null order by ".$ostr." limit ".$start.",".$page_num;
            $result = M('BeubeuGoods')->query($sql);
            if(!empty($result)){
                foreach($result as $k1=>$v1){
                    $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                    $productsValue =  $productSyn->GetProductColorByID($v1['num_iid']);
                    $windex->Get32Pic($productsValue,$root_dir);
                    $result[$k1]['products'] = $productsValue;
                    $result[$k1]['tuijian'] = $windex->GetTuijian($v1['item_bn'],$v1['num_iid']);
                }
            }else{
                $result = array();
            }
            if($page==1){
                $parmas = array('oid'=>$oid);
                if(is_mobile()){
                    $result = $this->waterdataMobile($result,$lid,$bid,$keyword,$parmas);
                }else{
                    $result = $this->waterdata($result,$lid,$bid,$keyword,$parmas);
               }
            }
        }else if($kid>0){
            //快速搜索走这里
           if($keyword){
            $isair = is_int(strpos($keyword,' '));
            if($isair){
                $keywordArr = explode(' ',$keyword);
                $newkeyword = implode('%',$keywordArr);
            }else{
                $newkeyword =  $keyword;
            }
             $liketitle = "and bg.title like '%{$newkeyword}%'";
           }else{
             $liketitle = '';
           }
                if(!empty($uid)){
                    $wherelb = " left join (select id,num_iid from `u_love` where uid={$uid}) as lo on lo.num_iid=ol.num_iid left join (select id,num_iid from `u_buy` where uid={$uid}) bu on bu.num_iid=ol.num_iid";
                    $fieldlb = ",lo.id as loveid,bu.id as buyid";
                }
                $sql = "select ol.*{$fieldlb} from (select bg.num_iid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from `u_beubeu_goods` as bg inner join `u_goodtag` as g on g.num_iid=bg.num_iid  where  1 {$liketitle} group by g.num_iid order by {$ostr} limit {$start},{$page_num}) as ol{$wherelb}";
                $result = M('BeubeuGoods')->query($sql);
            if(!empty($result)){
                foreach($result as $k1=>$v1){
                    $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                    $productsValue =  $productSyn->GetProductColorByID($v1['num_iid']);
                    $windex->Get32Pic($productsValue,$root_dir);
                    $result[$k1]['products'] = $productsValue;
                    $result[$k1]['tuijian'] = $windex->GetTuijian($v1['item_bn'],$v1['num_iid']);
                }
            }else{
                $result = array();
            }
            if($page==1){
                $parmas = array('oid'=>$oid);
                if(is_mobile()){
                    $result = $this->waterdataMobile($result,$lid,$bid,$keyword,$parmas);
                }else{
                    $result = $this->waterdata($result,$lid,$bid,$keyword,$parmas);
              }
            }
        }else{
            //普通走这里
            /*if(S('good'.$sid.$fid.$zid.$tem.$page)){
             $result = unserialize(S('good'.$sid.$fid.$zid.$tem.$page));
            }else{*/
            $where = '';
            if(isset($tem)){
                $where.="and g.wid in (".$widvalue['str'].")";
            }
            switch($sid){
                case 1 :
                case 2 :
                    $where.=" and g.gtype='".$sid."'";
                    break;
                case 3 :
                    $where.=" and g.gtype in ('3','4','7')";
                    break;
                case 4 :
                    $where.=" and g.gtype='5'";
                    break;
            }
            if(!empty($fid) && $fid!='all'){
                $where.=" and g.ftag_id='".$fid."'";
            }
            if($zid && !empty($zid)){
                $is_g = is_int(strpos($zid,'_'));
                if(!$is_g){
                    $zid = $zid.'_';
                }
                $cstr = '';
                $ccid = explode('_',$zid);
                $ccid = array_unique($ccid);
                $gzid = $ccid[0];
                foreach($ccid as $k=>$v){
                    if($v){
                        $cstr.=$v.',';
                    }
                }
                $cstr = rtrim($cstr,',');
                $catewhere = " and cg.cateID in (".$cstr.")";
                //$where.= " and g.ccateid in (".$cstr.")";
            }
            //$where.=" and bg.approve_status='onsale'";
            //$where.=" and bg.isdel=0";
            if(isset($tem)){
                $case = "case when g.wid=".$widvalue['wid']." then 0 else g.wid end wo,";
                $ordr = "order by wo asc,";
            }else{
                $case = '';
                $ordr = "order by ";
                $fi = 'g.id';
            }
            if(!empty($uid)){
                $wherelb = " left join (select id,num_iid from `u_love` where uid={$uid}) as lo on lo.num_iid=al.num_iid left join (select id,num_iid from `u_buy` where uid={$uid}) bu on bu.num_iid=al.num_iid";
                $fieldlb = ",lo.id as loveid,bu.id as buyid";
            }
            if($sid!=4){
                $goodstable = '`u_beubeu_goods`';
                if(!empty($zid)){
                    $sellsex = $windex->getSellCateSex($gzid);
                    if($sellsex['gender']==4 || $sellsex['gender']==5){
                    $goodstable = '`u_goods`';
                    }
                }
            }else{
                $goodstable = '`u_goods`';
            }
            if($oid==1){
               $mwhere = " and bg.approve_status='onsale'";
            }
                    //有自定义分类也有其他的条件
         if(isset($tem)){
                    if(!empty($uid)){
                      $sql = "select al.*{$fieldlb} from (select {$case}bg.num_iid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from `u_goodtag` as g inner join {$goodstable} as bg on bg.id=g.good_id inner join `u_catesgoods` as cg on cg.num_iid=bg.num_iid where 1 ".$where." ".$catewhere."{$mwhere} group by g.good_id ".$ordr."{$ostr} limit ".$start.",".$page_num.")  as al ".$wherelb;
                    }else{
                      $sql = "select {$case}bg.num_iid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from `u_goodtag` as g inner join {$goodstable} as bg on bg.id=g.good_id inner join `u_catesgoods` as cg on cg.num_iid=bg.num_iid where 1 ".$where." ".$catewhere."{$mwhere} group by g.good_id ".$ordr."{$ostr} limit ".$start.",".$page_num;
                    }
         }else{
                $wheret = '';
                if(!empty($sid)){
                    switch($sid){
                        case 1 :
                        case 2 :
                        $wheret.=" and g.gtype='".$sid."'";
                            break;
                        case 3 :
                            $wheret.=" and g.gtype in ('3','4')";
                            break;
                        case 4 :
                            $wheret.=" and g.gtype='5'";
                            break;
                    }
                }
                if(!empty($fid) && $fid!='all'){
                    $wheret.=" and g.ftag_id='".$fid."'";
                }
                if(!empty($uid)){
                    $sql = "select al.*{$fieldlb} from (select bg.num_iid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from {$goodstable} as bg where EXISTS(select 1 from `u_goodtag` as g where bg.id=g.good_id{$wheret}) and exists(select 1 from `u_catesgoods` as cg where cg.num_iid=bg.num_iid{$catewhere}) {$mwhere} {$ordr}{$ostr},bg.id desc limit ".$start.",".$page_num.") as al ".$wherelb;
                }else{
                   $sql = "select bg.num_iid,bg.type,bg.isud,bg.approve_status,bg.item_bn,bg.title,bg.num,bg.price,bg.pic_url,bg.detail_url from {$goodstable} as bg where EXISTS(select 1 from `u_goodtag` as g where bg.id=g.good_id{$wheret}) and exists(select 1 from `u_catesgoods` as cg where cg.num_iid=bg.num_iid{$catewhere}) {$mwhere} {$ordr}{$ostr},bg.id desc
limit ".$start.",".$page_num;
                }
          }

            $result = $goodtag->query($sql);
            if(!empty($result)){
                foreach($result as $k1=>$v1){
                    $result[$k1]['skunum'] = $productSyn->getSkuNum($v1['num_iid']);
                    $productsValue =  $productSyn->GetProductColorByID($v1['num_iid']);
                    $windex->Get32Pic($productsValue,$root_dir);
                    $result[$k1]['products'] = $productsValue;
                    $result[$k1]['tuijian'] = $windex->GetTuijian($v1['item_bn'],$v1['num_iid']);
                }
            }else{
                $result = array();
            }

            if($page==1){
                $parmas = array('oid'=>$oid);
                if(is_mobile()){
                    $result = $this->waterdataMobile($result,$lid,$bid,$keyword,$parmas);
                }else{
                    $result = $this->waterdata($result,$lid,$bid,$keyword,$parmas);
                    //$result = $this->waterdataMobile($result,$lid,$bid,$keyword,$parmas);
                }
            }
            /*S('good'.$sid.$fid.$zid.$tem.$page,$result,array('type'=>'file'));
           }*/
        }
        if(!empty($result)){
            $returnArr = array('code'=>1,'da'=>$result,'timestamp'=>$timestamp);
        }else{
            $returnArr = array('code'=>0,'msg'=>'没有数据');
        }
        $this->ajaxReturn($returnArr, 'JSON');
    }
    public function waterdata($result,$lid,$bid,$keyword,$arr){
        switch($arr['oid']){
            case 1 :
                $sel1 = 'selected="selected"';
                break;
            case 2 :
                $sel2 = 'selected="selected"';
                break;
            case 3 :
                $sel3 = 'selected="selected"';
                break;
            case 4 :
                $sel4 = 'selected="selected"';
                break;
            case 5 :
                $sel5 = 'selected="selected"';
                break;
            case 6 :
                $sel6 = 'selected="selected"';
                break;
            case 7 :
                $sel7 = 'selected="selected"';
                break;
        }
        $ad = "<div class='productinfo'><div class='wrapper_box banner_box'><a href='http://uniqlo.tmall.com/category-138188209.htm?kid=11727_51912_165828_280468' target='_blank'><img src='".C('UNIQLOURL')."Upload/ad/TB2qz8SaXXXXXa5XXXXXXXXXXXX-196993935.jpg' width='228' height='471' alt='' /></a></div></div>";
        $ad2 = '<div class="productinfo"><div class="wrapper_box banner_box"><a href="http://uniqlo.tmall.com/category-639803611.htm?kid=11727_51912_228348_280466" target="_blank"><img src="'.C('UNIQLOURL').'Upload/ad/TB2bmxuaXXXXXXcXpXXXXXXXXXX-196993935.jpg" width="228" height="228" alt="" /></a></div></div>';

        if($lid == 1 && $bid == 0){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn select" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><select name="sequence" id="gorder"><option value="2" '.$sel2.'>最新商品</option><option value="3" '.$sel3.'>热销商品</option><option value="1" '.$sel1.'>库存排序</option><option value="4" '.$sel4.'>价格由低到高</option><option value="5" '.$sel5.'>价格由高到低</option></select></div></div></div>';
        }
        else if($lid == 0 && $bid == 1){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn select" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><select name="sequence" id="gorder"><option value="2" '.$sel2.'>最新商品</option><option value="3" '.$sel3.'>热销商品</option><option value="1" '.$sel1.'>库存排序</option><option value="4" '.$sel4.'>价格由低到高</option><option value="5" '.$sel5.'>价格由高到低</option></select></div></div></div>';

        }else if($lid == 1 && $bid == 1){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn select" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn select" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><select name="sequence" id="gorder"><option value="2" '.$sel2.'>最新商品</option><option value="3" '.$sel3.'>热销商品</option><option value="1" '.$sel1.'>库存排序</option><option value="4" '.$sel4.'>价格由低到高</option><option value="5" '.$sel5.'>价格由高到低</option></select></div></div></div>';
        }else if($lid == 0 && $bid == 0 ){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><select name="sequence" id="gorder"><option value="2" '.$sel2.'>最新商品</option><option value="3" '.$sel3.'>热销商品</option><option value="1" '.$sel1.'>库存排序</option><option value="4" '.$sel4.'>价格由低到高</option><option value="5" '.$sel5.'>价格由高到低</option></select></div></div></div>';
        }
        else{
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><select name="sequence" id="gorder"><option value="2" '.$sel2.'>最新商品</option><option value="3" '.$sel3.'>热销商品</option><option value="1" '.$sel1.'>库存排序</option><option value="4" '.$sel4.'>价格由低到高</option><option value="5" '.$sel5.'>价格由高到低</option></select></div></div></div>';
        }
        $keystr = '<div class="productinfo"><div class="right_search2"><div class="wrapper_box wrapper_box_search"><input name="search" type="text" value="'.$keyword.'" placeholder="输入您想要的款式或名称" autocomplete="off" id="keywordid"><a href="javascript:;" id="keybutton"></a></div></div></div>';
        array_unshift($result,array('first'=>1,'ad'=>$ad));
        array_splice($result,1,0,array(array('first'=>2,'ke'=>$keystr)));
        array_splice($result,2,0,array(array('first'=>3,'ad'=>$ad2)));
        array_splice($result,3,0,array(array('first'=>4,'cb'=>$str)));
        /*$arr_count = count($result);
        if($arr_count>=4){
            array_splice($result,2,0,array(array('first'=>2,'ad'=>$ad2)));
        }else if($arr_count == 3){
            array_splice($result,($arr_count-1),0,array(array('first'=>2,'ad'=>$ad2)));
        }else{
            array_splice($result,($arr_count),0,array(array('first'=>2,'ad'=>$ad2)));
        }
        $arr_count = count($result);
        if($arr_count>=4){
            array_splice($result,3,0,array(array('first'=>3,'cb'=>$str)));
        }else{
            array_splice($result,($arr_count),0,array(array('first'=>3,'cb'=>$str)));
        }*/
        return $result;
    }
    //手机版广告
    public function waterdataMobile($result,$lid,$bid,$keyword,$arr){
        switch($arr['oid']){
            case 1 :
                $sel1 = 'selected="selected"';
            break;
            case 2 :
                $sel2 = 'selected="selected"';
            break;
            case 3 :
                $sel3 = 'selected="selected"';
            break;
            case 4 :
                $sel4 = 'selected="selected"';
            break;
            case 5 :
                $sel5 = 'selected="selected"';
            break;
        }
        if($lid == 1 && $bid == 0){
            $str = '<div class="productinfo"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn select" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><form action="#" method="get"><input name="search" type="text" value="'.$keyword.'" placeholder="款式或名称" id="keywordid" autocomplete="off"><a href="javascript:;" id="keybutton"></a></form></div></div>';
        }
        else if($lid == 0 && $bid == 1){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn select" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><form action="#" method="get"><input name="search" type="text" value="'.$keyword.'" placeholder="款式或名称" id="keywordid" autocomplete="off"><a href="javascript:;" id="keybutton"></a></form></div></div>';

        }else if($lid == 1 && $bid == 1){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn select" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn select" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><form action="#" method="get"><input name="search" type="text" value="'.$keyword.'" placeholder="款式或名称" id="keywordid" autocomplete="off"><a href="javascript:;" id="keybutton"></a></form></div></div>';
        }else if($lid == 0 && $bid == 0 ){
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><form action="#" method="get"><input name="search" type="text" value="'.$keyword.'" placeholder="款式或名称" id="keywordid" autocomplete="off"><a href="javascript:;" id="keybutton"></a></form></div></div>';
        }
        else{
            $str = '<div class="productinfo"><div class="right_search"><div class="wrapper_box wrapper_box_btn_group"><a href="javascript:;" class="ysc_btn select" id="cldata"><i></i>我喜欢</a><a href="javascript:;" class="ygm_btn" id="buydata"><i></i>已购买</a></div><div class="wrapper_box wrapper_box_search"><form action="#" method="get"><input name="search" type="text" value="'.$keyword.'" placeholder="款式或名称" id="keywordid" autocomplete="off"><a href="javascript:;" id="keybutton"></a></form></div></div>';
        }
        $arr_count = count($result);
        if($arr_count==0){
            array_splice($result,0,0,array(array('first'=>1,'ad'=>$str)));
        }else{
            array_splice($result,1,0,array(array('first'=>1,'ad'=>$str)));
        }

        return $result;
    }
//收入衣柜
    public function addwar(){
        if(session("uniq_user_name")){
            $id = trim($this->_post('id'));
            if($id>0){
                $uid = session("uniq_user_id");
                $user = M('user')->where("id={$uid}")->find();
                if(empty($user['mobile'])){
                    $returnArr = array('code'=>-3,'msg'=>'');
                }else{
                    //if($user['is_active']){
                    $collection = M('Collection');
                    $time = date('Y-m-d H:i:s');
                    $cresult = $collection->field('id')->where(array('num_iid'=>$id,'uid'=>session("uniq_user_id")))->find();
                    if(empty($cresult)){
                        $flag = $collection->add(array('num_iid'=>$id,'uid'=>session("uniq_user_id"),'cratetime'=>$time));
                        if($flag){
                            $returnArr = array('code'=>1,'msg'=>'');
                        }else{
                            $returnArr = array('code'=>-2,'msg'=>'收入衣柜失败');
                        }
                    }else{
                        $returnArr = array('code'=>-2,'msg'=>'您已经收入过衣柜，不要重复收入');
                    }
                    //}else{
                    //$returnArr = array('code'=>-4,'msg'=>$user['mobile']);
                    //}
                }
            }
        }else{
            $returnArr = array('code'=>-1,'msg'=>'');
        }
        $this->ajaxReturn($returnArr,'JSON');
    }
}