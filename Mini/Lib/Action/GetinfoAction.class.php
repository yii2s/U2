<?php
class GetinfoAction extends Action{
        public function sexToStyle(){
            $sid = trim($this->_request('sid'));
            $sid = $sid?$sid:0;
            if(S('sid'.$sid)){
             $arr = unserialize(S('sid'.$sid));
            }else{
                $recomodel = D('Reco');
            switch($sid){
                case 3 :
                $where['u_settings_gender_style.genderID'] = array('exp','IN(3,4)');
                $result = $this->getGS($where);
                //取出默认数据
                $defaultwhere['suitStyleID'] = $result[0]['ID'];
                $defaultwhere['suitGenderID'] = array('exp','IN(3,4)');
                $defaultResult = $recomodel->getBeubeu($defaultwhere);
                break;
                case 4 :
                  //mini婴幼儿还是从以前商品取数据
                  $result = M('BeubeuGoods')->cache(true)->field('pic_url')->where(array('type'=>$sid,'approve_status'=>'onsale','num'=>array('egt',15)))->order('uptime desc')->select();
                  $arr['def'] = $result;
                break;
                default :
                $where['u_settings_gender_style.genderID'] = $sid;
                $result = $this->getGS($where);
                $defaultwhere['suitStyleID'] = $result[0]['ID'];
                $defaultwhere['suitGenderID'] = $sid;
                $defaultResult = $recomodel->getBeubeu($defaultwhere);
                break;
            }
            if($sid!=4){
            $recomodel = D('Reco');
            foreach($result as $k=>$v){
            $result[$k]['pid'] = $recomodel->pageToDataStyle($v['ID']);
            }
            $arr['sty'] = $result;
            $arr['def'] = $defaultResult;
            }
            S('sid'.$sid,serialize($arr),array('type'=>'file'));
          }
            $this->ajaxReturn($arr, 'JSON');
        }
    public function getGS($where){
           $list = M('SettingsGenderStyle')->cache(true)->join('inner join u_settings_suit_style as sss on u_settings_gender_style.styleID=sss.ID')->distinct(true)->field('sss.ID')->where($where)->select();
           return $list;
    }
    //mini左侧点击风格获取数据
    public function styleToData(){
            $sid = trim($this->_post('sid'));
            $fid = trim($this->_post('fid'));
            if(S('sf'.$sid.$fid)){
               $result = unserialize(S('sf'.$sid.$fid));
            }else{
               $recomodel = D('Reco');
            switch($sid){
                case 3 :
                    $where['suitStyleID'] = $fid;
                    $where['suitGenderID'] = array('exp','IN(3,4)');
                    $result = $recomodel->getBeubeu($where);
                    break;
                case 4 :

                    break;
                default :
                    $where['suitStyleID'] = $fid;
                    $where['suitGenderID'] = $sid;
                    $result = $recomodel->getBeubeu($where);
                    break;
            }
          S('sf'.$sid.$fid,serialize($result),array('type'=>'file'));
          }
          $this->ajaxReturn($result, 'JSON');
    }
}