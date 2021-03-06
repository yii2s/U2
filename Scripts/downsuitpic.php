<?php
/**
 * 下载白衣搭配图片
 */
require_once('init.php');
set_time_limit(0);
ini_set('memory_limit','200M');
$root_dir = realpath(dirname(dirname(__FILE__)));
$db = new DB();
$offset = 0;
$limit = 200;
$ke = 1;
while($ke>0){
    $sql = "select `suitID`,`suitImageUrlHead`,`suitImageUrlBody`,`suitImageUrlShose`,`suitImageUrlMatch` from `u_beubeu_suits` where suitImageUrl='' and `approve_status`=0 limit $offset,$limit";
	//$sql = "select `suitID`,`suitImageUrlHead`,`suitImageUrlBody`,`suitImageUrlShose`,`suitImageUrlMatch` from `u_beubeu_suits` where `approve_status`=0 limit $offset,$limit";
    $result = $db->mysqlfetch($sql);
    unset($sql);
    if(empty($result)){
        $ke = 0;
    }else{
        $time = date('Y-m-d H:i:s');
        foreach($result as $k2=>$v2){
            $image_head = $db->createdir($v2['suitID'].'_Head',$root_dir.'/Upload/partsuits/Head/','Upload/partsuits/Head/',$v2['suitImageUrlHead'],2,$offset+1);
			if(!filesize($image_head[0])){
            @file_put_contents($image_head[0], file_get_contents($v2['suitImageUrlHead'].'.600x800.png'));//头
			}
            $image_body = $db->createdir($v2['suitID'].'_Body',$root_dir.'/Upload/partsuits/Body/','Upload/partsuits/Body/',$v2['suitImageUrlBody'],2,$offset+1);
			if(!filesize($image_body[0])){
            @file_put_contents($image_body[0], file_get_contents($v2['suitImageUrlBody'].'.600x800.png'));//身体
            }
			$shoseexten = pathinfo($v2['suitImageUrlShose'],PATHINFO_EXTENSION);
			if($shoseexten=='png'){
            $image_shose = $db->createdir($v2['suitID'].'_Shose',$root_dir.'/Upload/partsuits/Shose/','Upload/partsuits/Shose/',$v2['suitImageUrlShose'],2,$offset+1);
			if(!filesize($image_shose[0])){
            @file_put_contents($image_shose[0], file_get_contents($v2['suitImageUrlShose'].'.600x800.png'));//鞋子
            }
		    }else{
              $image_shose[1] = 1;
			}
            $image_match = $db->createdir($v2['suitID'].'_Match',$root_dir.'/Upload/partsuits/Match/','Upload/partsuits/Match/',$v2['suitImageUrlMatch'],2,$offset+1);
			if(!filesize($image_match[0])){
            @file_put_contents($image_match[0], file_get_contents($v2['suitImageUrlMatch'].'.600x800.png'));//衣服
			}
            $resql = "select `suitid` from `u_suit_pic` where `suitid`=".$v2['suitID'];
            $re = $db->mysqlfetch($resql);
            if(empty($re)){
            $insql = "insert into `u_suit_pic` (`suitid`,`pic_head`,`pic_body`,`pic_shose`,`pic_match`,`createtime`) values ('".$v2['suitID']."','".$image_head[1]."','".$image_body[1]."','".$image_shose[1]."','".$image_match[1]."','".$time."')";
            $db->mysqlquery($insql);
          }else{
            $insql = "update `u_suit_pic` set `pic_head`='".$image_head[1]."',`pic_body`='".$image_body[1]."',`pic_shose`='".$image_shose[1]."',`pic_match`='".$image_match[1]."',`isdown`=0 where `suitid`=".$v2['suitID'];
            $db->mysqlquery($insql);
		  }
        }
    }
    $offset+=$limit;
    sleep(1);
    unset($result);
}
exit;