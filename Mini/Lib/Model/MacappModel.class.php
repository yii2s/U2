<?php
class MacappModel extends Model{
    public function CkeckApp($uname,$upass){
        if($uname==C('IOSNMAE') && $upass==C('IOSPASS')){
            $flag = 1;
        }else{
            $flag = 0;
        }
        return $flag;
    }
    public function getCollNumm_iid($uq){
        if(!empty($uq)){
            $goods = M('Goods');
            $is_g = is_int(strpos($uq,'_'));
            if(!$is_g){
                $uq = $uq.'_';
            }
            $arr_uq = explode('_',$uq);
            foreach($arr_uq as $k=>$v){
                if($v){
                    $sql = "select `num_iid` from `u_goods` where left(item_bn,8)='".$v."' order by num desc";
                    $result = $goods->query($sql);
                    $arr[] = $result[0]['num_iid'];
                }
            }
            return $arr;
        }
    }
public function getsstyle($sid){
    $sql = "select s.ID,s.description,gs.goodnum from u_settings_suit_style as s inner join u_settings_gender_style as gs on gs.styleID=s.ID where gs.genderID=".$sid;
    $list = M('')->query($sql);
    return $list;
}
public function getUqNum($item_bn){
    if(!empty($item_bn)){
        $goods = M('Goods');
        $sql = "select num_iid,title,approve_status,IF(num>0 and approve_status='onsale',detail_url,'') as detail_url,num from u_beubeu_goods where item_bn like '".$item_bn."%' order by num desc";
        $result = $goods->query($sql);
        if(!empty($result[0])){
            $returnArr = array('code'=>1,'data'=>$result[0]);
        }else{
            $returnArr = array('code'=>0,'msg'=>'没有数据');
        }
    }else{
            $returnArr = array('code'=>0,'msg'=>'参数错误');
    }
    return $returnArr;
}
    public function getCusData2($where){
        $sell = M('Sellercats');
        $result = $sell->field('ID as id,shortName as name')->where($where)->group('shortName')->order('sort_order asc')->select();
        foreach($result as $k=>$v){
            if(!empty($where['gender'])){
                $arr2['gender'] = $where['gender'];
            }
            $arr2['selected'] = 1;
            $arr2['shortName'] = $v['name'];
            $arr2['isshow'] = 0;
            $idlist = $sell->field('ID as id,goodnum')->where($arr2)->select();
            $idstr = '';$sum = 0;
            foreach($idlist as $k1=>$v1){
                if($v1){
                    $idstr.=$v1['id'].'_';
                    $sum+=$v1['goodnum'];
                }
            }
            $idstr = rtrim($idstr,'_');
            $ucuslist[] = array('id'=>$idstr,'name'=>$v['name'],'sum'=>$sum);
        }
        unset($result);
        return $ucuslist;
    }
public function SqlCount(&$sql,$goodtag,$page_num){
    $gcount = $goodtag->query($sql);
    $count = ceil($gcount[0]['co']/$page_num);
    return $count;
}
    public function getBeubeu($where,$page,$page_num,$start,$unihost){
        $where['suitImageUrl'] = array('neq','');
        //$beubeu_suits = M('BeubeuSuits');
        $beubeu_suits = M('SuitOrder');
        $count = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where($where)->count();
        $num = ceil($count/$page_num);
        if($page>$num){
            $page = 1;
            $start = 0;
        }
        print_r($where);
        if(isset($where['suitStyleID']) && !empty($where['suitStyleID'])){
            $beubeu_suits_list = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where($where)->order('id desc')->limit($start.','.$page_num)->select();
        }else{
            $beubeu_suits_list = $beubeu_suits->field('suitID,suitGenderID,suitImageUrl')->where($where)->order('all_order asc')->limit($start.','.$page_num)->select();
        }
        foreach($beubeu_suits_list as $k=>$v){
            $beubeu_suits_list[$k]['suitImageUrl'] = $unihost.$v['suitImageUrl'];
            switch($v['suitGenderID']){
                case 1 :
                    $sex = 15474;
                    break;
                case 2 :
                    $sex = 15478;
                    break;
                case 3 :
                    $sex = 15583;
                    break;
                case 4 :
                    $sex = 15581;
                    break;
            }
            $beubeu_suits_list[$k]['sex'] = $sex;
        }
        $arr['page'] = $page+1;
        $arr['result'] = $beubeu_suits_list;
        $arr['count'] = $num;
        return $arr;
    }
    public function getBenebnColl($where,$unihost){
        $beubeu_coll = M('BeubeuCollection');
        $result = $beubeu_coll->field('id,gender,suitID,pic_head,pic_body,pic_shoes,pic_clothes')->where($where)->order('id desc')->select();
        $str = '';
        foreach($result as $k=>$v){
            $str.=$v['id'].',';
        }
        $str = rtrim($str,',');
        $sql = "select t1.bcid,bg.`num_iid`,bg.`approve_status`,bg.`title`,bg.`num`,bg.`pic_url`,IF(bg.num>0 and bg.approve_status='onsale',bg.detail_url,'') as detail_url,li.loveid,li.buyid from (select `bcid`,`num_iid` from `u_beubeu_coll_goods` as bc where bc.bcid in ({$str})) as t1 inner join `u_beubeu_goods` as bg on bg.num_iid=t1.num_iid left join (SELECT bl.num_iid,MAX(buyid) buyid,MAX(loveid) loveid from(
	select lo.num_iid,NULL buyid, lo.id as loveid from u_love lo where lo.uid=".$where['uid']."
	union all
	select bu.num_iid,bu.id,NULL from u_buy as bu where bu.uid=".$where['uid']."
) bl group by bl.num_iid) as li on li.num_iid=bg.num_iid";
        $detail = $beubeu_coll->query($sql);
        foreach($result as $k1=>$v1){
            $detailArr = array();
            $karr = array();$karr2 = array();
            foreach($detail as $k2=>$v2){
                $v2['pic_url'] = $unihost.$v2['pic_url'];
                if($v1['id']==$v2['bcid']){
                    if($v2['num']<=0 || $v2['approve_status']=='instock'){
                        $v2['title'] = '【已售罄】'.$v2['title'];
                    }
                    $orid = $this->collGoodsOrder($v2['title']);
                    if($orid!=-1){
                        $karr2[$orid] = $v2;
                        $karr[] = $orid;
                    }else{
                        array_push($detailArr,$v2);
                    }
                    //$detailArr[] = $v2;
                }
            }
            arsort($karr);
            foreach($karr as $k3=>$v3){
                if(empty($detailArr)){
                    $detailArr[] = $karr2[$v3];
                }else{
                    array_unshift($detailArr,$karr2[$v3]);
                }
            }
            $result[$k1]['detail'] = $detailArr;
        }
        return $result;
    }
    public function collGoodsOrder($title){
        $arr = array('羽绒服','大衣','外套','卫衣','马甲','毛衣','针织衫','衬衫','衫','薄衫','茄克','家居服','套装（连身装）','T恤','背心','内衣','裙子','裤','帽子','坎肩围巾','包','袜子','鞋子','配饰','首饰','其他');
        $orid = -1;
        foreach($arr as $k=>$v){
            if(is_int(strpos($title,$v))){
                $orid = $k;
                break;
            }
        }
        return $orid;
    }
    public function AppShare($bbody,$bshose,$bclose,$bhead,$root_dir){
        $md5b = md5($bbody);$md5s = md5($bshose);$md5c = md5($bclose);$md5h = md5($bhead);$md5share = md5($bbody.$bshose.$bclose.$bhead);
        $share = M('Share');
        $result = $share->field('pic_url')->where(array('id'=>$md5share))->find();
        if(!empty($result)){
            $url = 'http://'.$_SERVER['HTTP_HOST'].$result['pic_url'];
        }else{
            $down = M('ShareDown');
            $shoseexten = pathinfo($bshose,PATHINFO_EXTENSION);
            $downb = $down->field('pic_url')->where(array('id'=>$md5b))->find();
            if(!empty($downb)){
                $bimage[0] = $downb['pic_url'];
            }else{
                $bimage = $this->createdir($md5b,$root_dir.'/Upload/sharedownpic/Body/','Upload/sharedownpic/Body/',$bbody,2);//身体
                @file_put_contents($bimage[0],file_get_contents($bbody));
                $down->add(array('id'=>$md5b,'pic_url'=>$bimage[0]));
            }
            $downs = $down->field('pic_url')->where(array('id'=>$md5s))->find();
            if(!empty($downs)){
                $simage[0] = $downs['pic_url'];
            }else{
                if($shoseexten=='png'){
                    $simage = $this->createdir($md5s,$root_dir.'/Upload/sharedownpic/Shose/','Upload/sharedownpic/Shose/',$bshose,2);//鞋子
                    @file_put_contents($simage[0],$bshose);
                    $down->add(array('id'=>$md5s,'pic_url'=>$simage[0]));
                }
            }
            $downc = $down->field('pic_url')->where(array('id'=>$md5c))->find();
            if(!empty($downc)){
                $cimage[0] = $downc['pic_url'];
            }else{
                $cimage = $this->createdir($md5c,$root_dir.'/Upload/sharedownpic/Match/','Upload/sharedownpic/Match/',$bclose,2);//衣服
                @file_put_contents($cimage[0],file_get_contents($bclose));
                $down->add(array('id'=>$md5c,'pic_url'=>$cimage[0]));
            }
            $downh = $down->field('pic_url')->where(array('id'=>$md5h))->find();
            if(!empty($downh)){
                $himage[0] = $downh['pic_url'];
            }else{
                $himage = $this->createdir($md5h,$root_dir.'/Upload/sharedownpic/Head/','Upload/sharedownpic/Head/',$bhead,2);//头
                @file_put_contents($himage[0],file_get_contents($bhead));
                $down->add(array('id'=>$md5h,'pic_url'=>$himage[0]));
            }
            $white=new Imagick($bimage[0]);//身体
            if($shoseexten=='png'){
                $im4=new Imagick($simage[0]);//鞋子
            }
            $im2=new Imagick($cimage[0]);//衣服
            $im3=new Imagick($himage[0]);//头
            if($shoseexten=='png'){
                $white->compositeimage($im4, Imagick::COMPOSITE_OVER, 0, 0);
            }
            $white->compositeimage($im2, Imagick::COMPOSITE_OVER, 0, 0);
            $white->compositeimage($im3, Imagick::COMPOSITE_OVER, 0, 0);
            $white->thumbnailImage( 400, 533);
            $white->setImageFormat('png');
            $image = $this->createdir($md5share,$root_dir.'/Upload/sharepic/','/Upload/sharepic/',$bclose,2);
            $white->writeImage($image[0]);
            $white->clear();
            $white->destroy();
            $share->add(array('id'=>$md5share,'pic_url'=>$image[1]));
            $url = 'http://'.$_SERVER['HTTP_HOST'].$image[1];
        }
        return $url;
    }
    public function createdir($filename,$dir='',$path='',$img='',$flag = 1){
        $timenow = time();
        $year = date('Y',$timenow);
        $date = date('Y-m',$timenow);
        $time = date('Y-m-d',$timenow);
        $hou = date('Y-m-d-H',$timenow);
        $dir1 = $dir.$year;
        $path.=$year;
        if(!file_exists($dir1)&&!is_dir($dir1)){
            mkdir($dir1,0777);
        }
        $dir1 = $dir1.'/'.$date;
        $path.='/'.$date;
        if(!file_exists($dir1)&&!is_dir($dir1)){
            mkdir($dir1,0777);
        }
        $dir1 = $dir1.'/'.$time;
        $path.='/'.$time;
        if(!file_exists($dir1)&&!is_dir($dir1)){
            mkdir($dir1,0777);
        }

        /*$dir1 = $dir1.'/'.$hou;
        $path.='/'.$hou;
        if(!file_exists($dir1)&&!is_dir($dir1)){
            mkdir($dir1,0777);
        }*/
        $extension = pathinfo($img, PATHINFO_EXTENSION);
        $save_image = $dir1.'/'.$filename.'.'.$extension;
        if($flag==1){
            $path.='/'.$filename.'.png';
        }else if($flag==2){
            $path.='/'.$filename.'.'.$extension;
        }
        $arr[] = $save_image;
        $arr[] = $path;
        return $arr;
    }
public function IsLogin($uid,$user_name){
    $where_str = " id='{$uid}' and ( taobao_name = '{$user_name}' OR mobile = '{$user_name}' )";
    $user = M('User')->where($where_str)->find();
    if(!empty($user)){
      $flag = 1;
    }else{
      $flag = 0;
    }
    return $flag;
}
public function GetProductColorByID($id,$unihost)
    {
        $goods = M('Goods');

        //由于客户需要展示图片，所以将所有的颜色改成图片地址。
        return $goods
            ->join('INNER JOIN u_products_beubeu on left(u_goods.item_bn,8) = u_products_beubeu.uq')
            ->join('INNER JOIN u_settings on u_settings.key = u_goods.gender')
            ->join('INNER JOIN u_color on u_color.id = u_products_beubeu.color')
            ->join('INNER JOIN u_products on u_products.num_iid=u_goods.num_iid and u_products.cid=u_products_beubeu.color')
            ->field("
                    distinct u_products_beubeu.color as colorid,
                    u_products.num_iid as num_iid,
                    concat('{$unihost}',u_products.url)  as colorcode,
                    u_color.color_name as colorname,
                    left(u_goods.item_bn,8) as uq ,
                    u_settings.value as gender
                    ")
            ->where(array('u_goods.num_iid'=>$id,'u_products_beubeu.status'=>'1'))
            ->group('uq,colorid')
            ->order('u_products_beubeu.id')
            ->select();
    }
}