<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yu
 * Date: 14-7-10
 * Time: 下午3:49
 * To change this template use File | Settings | File Templates.
 */
class ExportinAction extends Action{
    public function exportSuit(){
       set_time_limit(0);
      //$handle = fopen('Upload/1.csv','r');
      $handle = fopen('/data/upload/Upload/1.csv','r');
      $i = 0;
      $suit = M('BeubeuSuits');
      while($data = fgetcsv($handle)){
       if($i>0){
           /*$da = array('suitImageUrl'=>$data[3],
                       'suitImageUrlHead'=>$data[4],
                       'suitImageUrlBody'=>$data[5],
                       'suitImageUrlShose'=>$data[6],
                       'suitImageUrlMatch'=>$data[7],
                       'tag'=>$data[8]);*/
          $sql = "update `u_beubeu_suits` set `suitImageUrl`='".$data[3]."',`suitImageUrlHead`='".$data[4]."',`suitImageUrlBody`='".$data[5]."',`suitImageUrlShose`='".$data[6]."',`suitImageUrlMatch`='".$data[7]."',`tag`='".$data[8]."' where `suitID`=".$data[0];
           $suit->query($sql);
          //$suit->where(array('suitID'=>$data[0]))->save($da);
       }
          $i++;
      }
     exit;
    }
}