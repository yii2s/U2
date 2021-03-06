<?php
//更新商品
require_once('init.php');
require_once('TopSdk.php');
set_time_limit(0);
ini_set('memory_limit','200M');
$root_dir = realpath(dirname(dirname(__FILE__)));
$db = new DB();
$c = new TopClient;
$c->appkey = $db->appkey;
$c->secretKey = $db->secretKey;
$c->format = 'json';
$req = new ItemsOnsaleGetRequest;
$req->setFields("num_iid,title,price");
$req->setIsTaobao("true");
$req->setPageSize(1);
$resp = $c->execute($req, $db->token);
$resp = (array)$resp;
$pagenum = ceil($resp['total_results']/200);
$products = new ItemSellerGetRequest;//获取商品详细信息
$ccate = new ItemcatsGetRequest;
$ccate->setFields('cid,parent_cid,name,is_parent');

//获取商品详细信息
for($i=1;$i<=$pagenum;$i++){
//if($i==1){
$req->setFields("seller_cids,num_iid,title,cid,pic_url,num,approve_status,list_time,delist_time,price,modified");
$req->setPageNo($i);
$req->setIsTaobao("true");
$req->setPageSize(200);
$result = $c->execute($req, $db->token);

$products->setFields('detail_url,property_alias,outer_id,change_prop,props_name,desc,sku.properties_name,sku.properties,sku.quantity,sku.sku_id,sku.price,sku.modified,prop_img');
$goods = (array)$result->items->item;
$goods_arr = array();
$pstr = '';
$time = date('Y-m-d H:i:s');
foreach($goods as $k=>$v){
	$v = (array)$v;
	if($v['num']>0){
	//查询数据库里是否已经有了开始
	$gsql = "select `id`,`num_iid`,`pic_url` from `u_goods` where `num_iid`='".$v['num_iid']."'";
	$good_list = $db->mysqlfetch($gsql);

	unset($gsql);
	if(!empty($good_list[0])){
	
	$products->setNumIid($v['num_iid']);
	$product = $c->execute($products, $db->token);
	$product_arr = (array)$product->item;
    $des = GetXingXi($product_arr['desc']);
	//更新goods
    $xxt = '';
    if(!empty($des)){
        $xxt = "`detailpc`='".$des."',";//详细图
    }
    $proarr = explode(';',$product_arr['props_name']);

	$item_bn = 'UQ'.$product_arr['outer_id'];
	$v['title'] = addslashes($v['title']);
    $detail_url = 'http://detail.tmall.com/item.htm?id='.$v['num_iid'].'&kid=11727_51912_165824_211542';
	$sql = "update `u_goods` set `approve_status`='".$v['approve_status']."',`item_bn`='".$item_bn."',`outer_id`='".$product_arr['outer_id']."',`title`='".$v['title']."',`num`='".$v['num']."',`price`='".$v['price']."',`detail_url`='".$detail_url."',`props_name`='".$product_arr['props_name']."',`list_time`='".$v['list_time']."',`delist_time`='".$v['delist_time']."',`sort`='".$v['modified']."',".$xxt."`uptime`='".$time."' where `num_iid`='".$v['num_iid']."'";
    $db->mysqlquery($sql);
    unset($sql);
    //0528更新商品开始
        $v['seller_cids'] = ltrim($v['seller_cids'],',');
        $sell_cate = explode(',',$v['seller_cids']);
        $goodcatesql = "select `cateID` from `u_catesgoods` where `num_iid`=".$v['num_iid'];
        $good_result = $db->mysqlfetch($goodcatesql);
         $aellarr = array();
        foreach($good_result as $ks=>$vs){
            $aellarr[] = $vs['cateID'];
        }
        foreach($sell_cate as $ks1=>$vs1){
            if(!empty($vs1)){
            if(!in_array($vs1,$aellarr)){
              $gcinsql = "insert into `u_catesgoods` (`num_iid`,`cateID`) values ('".$v['num_iid']."','".$vs1."')";
              $db->mysqlquery($gcinsql);
            }
           }
        }
        unset($goodcatesql);
        unset($good_result);
        $goodcatesql = "select `cateID` from `u_catesgoods` where `num_iid`=".$v['num_iid'];
        $good_result = $db->mysqlfetch($goodcatesql);
        foreach($good_result as $ks2=>$vs2){
           if(!in_array($vs2['cateID'],$sell_cate)){
              $gcdelsql = "delete from `u_catesgoods` where `num_iid`=".$v['num_iid']." and `cateID`=".$vs2['cateID'];
              $db->mysqlquery($gcdelsql);
           }
        }
    //0528更新商品结束
	$product_arr_sku = (array)$product_arr['skus']->sku;
	$prop_imgs = (array)$product_arr['prop_imgs']->prop_img;
    //颜色
	$property_alias = $product_arr['property_alias'];
    $arrcolor = getcolor($property_alias);
	//颜色
	foreach($product_arr_sku as $k2=>$v2){
	$v2 = (array)$v2;
	$properties = explode(';',$v2['properties']);
    //sku库存等于0
	//if($v2['quantity']>0){
	$psql = "select `id`,`url` from `u_products` where `sku_id`='".$v2['sku_id']."'";
	$p_list = $db->mysqlfetch($psql);
	unset($psql);
	if(!empty($p_list[0])){
	//更新
    $kucolor = explode(':',$properties[0]);
	//$kucolor2 = explode(':',$properties[1]);
	$cid = 0;
	$cstr = '';
    if(!empty($product_arr['property_alias'])){
    foreach($arrcolor as $kcc=>$vcc){
    if($vcc['id']==$kucolor[1]){
    $cid = $vcc['cid'];
    $cstr = $vcc['cv'];
	break;
	}
	}
    }
    if(empty($cid) && empty($cstr)){
        $newcolor = GetColorValue($v2['properties_name']);
        $cid = $newcolor[0]['cid'];
        $cstr = $newcolor[0]['cv'];
    }

    //20140429
	$pro_img_url = '';
    if(!file_exists($root_dir.'/'.$p_list[0]['url'])){
	$url = '';
	foreach($prop_imgs as $k5=>$v5){
	if($v5->properties==$properties[0]){
	$url = $v5->url;
	break;
	}
	}
    $save_image = $db->createdir($v2['sku_id'],$root_dir.'/Upload/products/','Upload/products/',$url,2);
    @file_put_contents($save_image[0], file_get_contents($url));
	    $pic_md5 = md5_file($save_image[0]);
        if($pic_md5!='7050e9efe57300e214ad2155f37eb2cd' && $pic_md5!='8f5b0194900ce27d4aa09bdf27be2874'){
         $pro_img_url = ",url='".$save_image[1]."'";
        }else{
         unlink($save_image[0]);
        }
	}
	//20140429
	$psql = "update `u_products` set `cid`='".$cid."',`cvalue`='".$cstr."',`properties`='".$v2['properties']."',`properties_name`='".$v2['properties_name']."',`quantity`='".$v2['quantity']."' {$pro_img_url},`modified`='".$v2['modified']."',`price`='".$v2['price']."' where `sku_id`='".$v2['sku_id']."'";
	$db->mysqlquery($psql);
	unset($psql);
	/*if(file_exists($root_dir.'/'.$p_list[0]['url'])){
     unlink($root_dir.'/'.$p_list[0]['url']);
	}*/
	}else{
	//插入
	$url = '';
	foreach($prop_imgs as $k5=>$v5){
	if($v5->properties==$properties[0]){
	$url = $v5->url;
	break;
	}
	}
    $kucolor = explode(':',$properties[0]);
	$cid = 0;
	$cstr = '';
    foreach($arrcolor as $kcc=>$vcc){
    if($vcc['id']==$kucolor[1]){
    $cid = $vcc['cid'];
    $cstr = $vcc['cv'];
	break;
	}
	}
	//获取product的图片
    $save_image = $db->createdir($v2['sku_id'],$root_dir.'/Upload/products/','Upload/products/',$url,2);
    @file_put_contents($save_image[0], file_get_contents($url));
	$pic_md5 = md5_file($save_image[0]);
    if($pic_md5!='7050e9efe57300e214ad2155f37eb2cd' && $pic_md5!='8f5b0194900ce27d4aa09bdf27be2874'){
	$insql = "insert into `u_products` (`goods_id`,`num_iid`,`sku_id`,`cid`,`cvalue`,`properties`,`properties_name`,`quantity`,`url`,`modified`,`price`) values ('".$good_list[0]['id']."','".$good_list[0]['num_iid']."','".$v2['sku_id']."','".$cid."','".$cstr."','".$v2['properties']."','".$v2['properties_name']."','".$v2['quantity']."','".$save_image[1]."','".$v2['modified']."','".$v2['price']."')";
	$db->mysqlquery($insql);
	unset($insql);
    }else{
     unlink($save_image[0]);
    }
	}
    //更新sku价格历史表
    $skuHisSql = "select `sku_id` from `u_sku_price_history` where `sku_id`='".$v2['sku_id']."' and `price`='".$v2['price']."'";
    $skuResult = $db->mysqlfetch($skuHisSql);
    if(empty($skuResult[0])){
        $inskusql = "insert into `u_sku_price_history` (`sku_id`,`num_iid`,`item_bn`,`price`,`modified`) values ('".$v2['sku_id']."','".$good_list[0]['num_iid']."','".$item_bn."','".$v2['price']."','".$v2['modified']."')";
        $db->mysqlquery($inskusql);
    }
	}
	}else{
	$prosql = "insert into `u_products` (`goods_id`,`num_iid`,`sku_id`,`cid`,`cvalue`,`properties`,`properties_name`,`quantity`,`url`,`modified`,`price`) values ";
     //sku价格历史记录表
     $skusql = "insert into `u_sku_price_history` (`sku_id`,`num_iid`,`item_bn`,`price`,`modified`) values ";
     $inskusql = '';
     //插入新商品
	$ppsql = '';
	//获取分类
	$catarr = array();
	getcat($v['cid'],$catarr);
	
    //获取图片
    $save_image = $db->createdir($v['num_iid'],$root_dir.'/Upload/goods/','Upload/goods/',$v['pic_url']);
    @file_put_contents($save_image[0], file_get_contents($v['pic_url']));
	$products->setNumIid($v['num_iid']);
	$product = $c->execute($products, $db->token);
	$product_arr = (array)$product->item;
    $des = GetXingXi($product_arr['desc']);
	//插入goods
	$cname = '';
	foreach($catarr as $ck=>$cv){
    $cname.=$cv['name'].'-';
	}
	$cname = rtrim($cname,'-');
    $item_bn = 'UQ'.$product_arr['outer_id'];
	$v['title'] = addslashes($v['title']);
    $detail_url = 'http://detail.tmall.com/item.htm?id='.$v['num_iid'].'&kid=11727_51912_165824_211542';
	//$sql = "insert into `u_goods` (`num_iid`,`approve_status`,`catid`,`item_bn`,`outer_id`,`title`,`num`,`price`,`pic_url`,`detail_url`,`cname`,`dname`,`props_name`,`list_time`,`delist_time`,`createtime`,`uptime`) values ('".$v['num_iid']."','".$v['approve_status']."','".$catarr[1]['cid']."','".$item_bn."','".$product_arr['outer_id']."','".$v['title']."','".$v['num']."','".$v['price']."','".$save_image[1]."','".$product_arr['detail_url']."','".$cname."','".$catarr[1]['name']."','".$product_arr['props_name']."','".$v['list_time']."','".$v['delist_time']."','".$time."','".$time."')";
     $sql = "insert into `u_goods` (`num_iid`,`approve_status`,`catid`,`item_bn`,`outer_id`,`title`,`num`,`price`,`pic_url`,`detail_url`,`cname`,`dname`,`props_name`,`list_time`,`delist_time`,`sort`,`detailpc`,`createtime`,`uptime`) values ('".$v['num_iid']."','".$v['approve_status']."','".$catarr[1]['cid']."','".$item_bn."','".$product_arr['outer_id']."','".$v['title']."','".$v['num']."','".$v['price']."','".$save_image[1]."','".$detail_url."','".$cname."','".$catarr[1]['name']."','".$product_arr['props_name']."','".$v['list_time']."','".$v['delist_time']."','".$v['modified']."','".$des."','".$time."','".$time."')";
    $db->mysqlquery($sql);
    $goods_id = mysql_insert_id();
    //0528插入店铺自定义分类开始
    $sell_cate = explode(',',$v['seller_cids']);
    foreach($sell_cate as $ks=>$vs){
        if(!empty($vs)){
        $sellsql = "insert into `u_catesgoods` (`num_iid`,`cateID`) values ('".$v['num_iid']."','".$vs."')";
        $db->mysqlquery($sellsql);
        }
    }
    //0528插入店铺自定义分类结束
    unset($sql);
	$product_arr_sku = (array)$product_arr['skus']->sku;
	$prop_imgs = (array)$product_arr['prop_imgs']->prop_img;
    //颜色
	$property_alias = $product_arr['property_alias'];
    $arrcolor = getcolor($property_alias);
	//颜色
	if(!empty($product_arr_sku)){
	foreach($product_arr_sku as $k2=>$v2){
	$v2 = (array)$v2;
	//if($v2['quantity']>0){sku库存等于0
	$properties = explode(';',$v2['properties']);
	$url = '';
	foreach($prop_imgs as $k5=>$v5){
	if($v5->properties==$properties[0]){
	$url = $v5->url;
	break;
	}
	}

    $kucolor = explode(':',$properties[0]);
	$cid = 0;
	$cstr = '';
    if(!empty($product_arr['property_alias'])){
    foreach($arrcolor as $kcc=>$vcc){
    if($vcc['id']==$kucolor[1]){
    $cid = $vcc['cid'];
    $cstr = $vcc['cv'];
	break;
	}
	}
    }
    if(empty($cid) && empty($cstr)){
        $newcolor = GetColorValue($v2['properties_name']);
        $cid = $newcolor[0]['cid'];
        $cstr = $newcolor[0]['cv'];
    }
	//获取product的图片
    $save_image = $db->createdir($v2['sku_id'],$root_dir.'/Upload/products/','Upload/products/',$url,2);
    @file_put_contents($save_image[0], file_get_contents($url));
	$pic_md5 = md5_file($save_image[0]);
    if($pic_md5!=='7050e9efe57300e214ad2155f37eb2cd' && $pic_md5!='8f5b0194900ce27d4aa09bdf27be2874'){
	$ppsql.="('".$goods_id."','".$v['num_iid']."','".$v2['sku_id']."','".$cid."','".$cstr."','".$v2['properties']."','".$v2['properties_name']."','".$v2['quantity']."','".$save_image[1]."','".$v2['modified']."','".$v2['price']."'),";
     //sku价格历史记录
     $inskusql.="('".$v2['sku_id']."','".$v['num_iid']."','".$item_bn."','".$v2['price']."','".$v2['modified']."')";
    }else{
        unlink($save_image[0]);
    }
	}
	//插入product
	$prosql.=$ppsql;
	$psql = rtrim($prosql,',');
    $db->mysqlquery($psql);
	unset($ppsql);
	unset($prosql);
    //插入历史记录表
    $skusql.=$inskusql;
    $skusql = rtrim($skusql,',');
    $db->mysqlquery($skusql);
    unset($inskusql);unset($skusql);
	}
	}
    }
}
unset($result);
//}
sleep(1);
}
$db->mysqlquery("call pbenben()");
//获取顶级分类
 function getcat($cid,&$catarr){
	   global $c;
       global $ccate;
       global $db;
       $ccate->setCids($cid);
	   $recate = $c->execute($ccate,$db->token);
	   $recate = (array)$recate->item_cats;
	   $recate = (array)$recate['item_cat'][0];
	   if($recate['parent_cid']!=0){
	   	getcat($recate['parent_cid'],$catarr);
	   }
	   $catarr[] = $recate;
	   return $catarr;
}
//组织颜色数据
function getcolor($property_alias){
    $arr_pro = array();
    if(!empty($property_alias)){
    $arr_property = explode(';',$property_alias);
    foreach($arr_property as $ka=>$va){
    $arr_va = explode(':',$va);
    $issp = is_int(strpos($arr_va[2],' '));
    if($issp){
	    $arr_color = explode(' ',$arr_va[2]);
    }else{
        $arr_color[0] = substr($arr_va[2],0,2);
    }
	$arr_pro[] = array('id'=>$arr_va[1],'cid'=>$arr_color[0],'cv'=>$arr_va[2]);
	}
    }
	return $arr_pro;
}
//如果没有属性别名
function GetColorValue($properties_name){
     $arr = array();
     if(!empty($properties_name)){
         $arr_property = explode(';',$properties_name);
         $arr_va = explode(':',$arr_property[0]);
         $issp = is_int(strpos($arr_va[3],' '));
             if($issp){
                 $arr_color = explode(' ',$arr_va[3]);
             }else{
                 $arr_color[0] = substr($arr_va[3],0,2);
                 $arr_color[1] = substr($arr_va[3],3);
             }
     $arr[] = array('cid'=>$arr_color[0],'cv'=>$arr_color[1]);
     }
    return $arr;
}
//获取明细图
function GetXingXi($desc){
    $fg = array();$fg2 = array();
    preg_match_all('/<table width="725" border="0" cellspacing="0" cellpadding="0" style="border-left: 1.0px solid #dcdcdc;border-right: 1.0px solid #dcdcdc;border-top: 1.0px solid #dcdcdc;border-bottom: none medium;">  <tr> <td colspan="4" align="center" style="border-bottom: 1.0px solid #dcdcdc;"> <img src="https:\/\/img.alicdn.com\/imgextra\/i2\/196993935\/T22SyVXy8aXXXXXXXX-196993935\.jpg" alt="" width="722" height="59"> <\/td> <\/tr>(.*)<\/table>/iU',$desc,$fg,PREG_SET_ORDER);
    if(!empty($fg)){
    preg_match_all('/<img src="(.*)" alt="" width="360" height="360">/iU',$fg[0][1],$fg2,PREG_SET_ORDER);
    unset($fg);
        if(!empty($fg2)){
            $mingxi = array();
        foreach($fg2 as $mx=>$mv){
            $mingxi[] = $mv[1];
        }
        unset($fg2);
        return serialize($mingxi);
       }else{
            return 0;
        }
    }else{
        preg_match_all('/<tr><td align="center" colspan="4" style="border-bottom: 1.0px solid #dcdcdc;"><img alt="" height="59" src="https:\/\/img.alicdn.com\/imgextra\/i2\/196993935\/T22SyVXy8aXXXXXXXX-196993935\.jpg" width="722"><\/td><\/tr><tr>(.*)width="360"><\/td><\/tr><tr><td align="center" colspan="4"/iU',$desc,$fg,PREG_SET_ORDER);
		if(!empty($fg)){
           preg_match_all('/<img alt="" height="360" src="(.*)" /iU',$fg[0][1],$fg2,PREG_SET_ORDER);
           if(!empty($fg2)){
            $mingxi = array();
            foreach($fg2 as $mx=>$mv){
            $mingxi[] = $mv[1];
            }
             unset($fg2);
             return serialize($mingxi);
          }else{
            return 0;
          }
		}else{
           preg_match_all('/src="https:\/\/img\.alicdn\.com\/imgextra\/i2\/196993935\/T22SyVXy8aXXXXXXXX-196993935\.jpg"(.*)src="https:\/\/img\.alicdn\.com\/imgextra\/i4\/196993935\/T24JuYXvXXXXXXXXXX-196993935\.jpg"/iU',$desc,$fg,PREG_SET_ORDER);
		   if(!empty($fg)){
			preg_match_all('/src="(.*)"/iU',$fg[0][1],$fg2,PREG_SET_ORDER);
            if(!empty($fg2)){
				$mingxi = array();
				foreach($fg2 as $mx=>$mv){
				$mingxi[] = $mv[1];
				}
				 unset($fg2);
				 return serialize($mingxi);
			}else{
              return 0;
			}
		   }else{
             return 0;
		   }
		}
    }
}
exit;