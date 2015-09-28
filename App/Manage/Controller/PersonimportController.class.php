<?php
namespace Manage\Controller;
class PersonimportController extends CommonController {
    public function index() {
        $this->assign('type', '个人检测结果及营养运动');
        $this->display();
    }
    public function checksn() {
        $sn=trim($_POST['sn']);
        $where="sn="."'".$sn."'";
        $sndata = M('sn')->where($where)->find();
        if($sndata){
           if($sndata['member_id']==0){
                $data1['status'] =  0;
                $data1['info']='用户未绑定';
           }else{
                $data1['status'] =   1;
                $data1['data']=$sndata;
                $user_name=M('person')->where("sid="."'".$sndata['id']."'")->getField("member_name");
                $data1['info']='用户姓名:'.$user_name;
           }
           
        }else{
           $data1['status'] =  0;
           $data1['info']='检测信息编号有误';
        }
         echo json_encode($data1);
    }
    	//上传方法
     public function upload2()
    {
        header("Content-Type:text/html;charset=utf-8");
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('xls', 'xlsx');// 设置附件上传类
        $upload->savePath  =      '/'; // 设置附件上传目录
        // 上传文件
        $fn=intval(I('get.fn'))-1;
        $f=array('excelData1','excelData2','excelData3');
        $info   =   $upload->uploadOne($_FILES[$f[$fn]]);
        $filename = './Uploads'.$info['savepath'].$info['savename'];
        $exts = $info['ext'];
        //print_r($info);exit;
        if(!$info) {// 上传错误提示错误信息
            $data1['status'] =   0;
            $data1['info']=$upload->getError(); 
            echo json_encode($data1);
          }else{// 上传成功
            
              $data=$this->goods_import($filename, $exts);
              //print_r($data);exit;
              $this->save_import($data,I('get.fn'));
              
        }
    }
    //导入数据方法
    function goods_import($filename, $exts='xls')
    {
       
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        import("Org.Util.PHPExcel");
        //创建PHPExcel对象，注意，不能少了\
        $PHPExcel=new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
        if($exts == 'xls'){
            import("Org.Util.PHPExcel.Reader.Excel5");
            $PHPReader=new \PHPExcel_Reader_Excel5();
        }else if($exts == 'xlsx'){
            import("Org.Util.PHPExcel.Reader.Excel2007");
            $PHPReader=new \PHPExcel_Reader_Excel2007();
        }


        //载入文件
        $PHPExcel=$PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet=$PHPExcel->getSheet(0);
        //获取总列数
        $allColumn=$currentSheet->getHighestColumn();
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for($currentRow=2;$currentRow<=$allRow;$currentRow++){
            //从哪列开始，A表示第一列
            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                //数据坐标
                $address=$currentColumn.$currentRow;
                //读取到的数据，保存到数组$arr中
                $data[$currentRow][$currentColumn]=$currentSheet->getCell($address)->getValue();
            }

        }
        return $data;
        //$this->save_import($data);
    }

    //保存导入数据
    function save_import($data,$type=1)
    {
        $data1['info']="";
        //print_r($data);exit;
        $i=0;$e=0;
        foreach ($data as $k=>$v){			
            //通过检测编码，取出sid
            $where="sn="."'".$v['A']."'";
            $sn = M('sn')->where($where)->find();
            if($sn){
                if($sn['member_id']==0){
                    $data1['status'] = 0;                              
                    $data1['info'].='检测编号为'.$v['A'].'未绑定;';
                    $e++;
                    continue;
                    //echo json_encode($data1);exit; 
                }
            }else{
                    $data1['status'] = 0;                              
                    $data1['info'].='检测编号为'.$v['A'].'不存在;';
                    $e++;
                    continue;
                    //echo json_encode($data1);exit; 
            }
            $date['sid'] = $sn['id'];
            $date['add_time'] = time();
            if($type==1){
                $date['disease_cat']   =$v['B'];
                $date['disease_name']  =$v['C'];
                $date['disease_level'] =$v['D'];
		$result = M('result')->add($date);
                $time['check_time']=time();
                $time['status']     =1;
                $where="sid="."'".$sn['id']."'";
                M('check')->where($where)->save($time);
            }elseif ($type==2) {               
                $date['daye']       = $v['B'];
                $date['gs']         = $v['C'];
                $date['gs_example'] = $v['D'];
                $date['vegetables'] = $v['E'];
                $date['mem']        = $v['F'];
                $date['oil']        = $v['G'];
                $date['breakfast']  = $v['H'];
                $date['breakfaste'] = $v['I'];              
                $date['lunch']      = $v['J'];               
                $date['lunche']     = $v['K'];
                $date['dinner']     = $v['L'];
                $date['dinnere']    = $v['M'];               
		$result = M('nutrition')->add($date);
            }else{
                $date['aerobic']         = $v['B'];
                $date['power_frequency'] = $v['C'];
                $date['week1']           = $v['D'];
                $date['week2']           = $v['E'];
                $date['week3']           = $v['F'];
                $date['week4']           = $v['G'];
                $date['week5']           = $v['H'];
                $date['week6']           = $v['I'];              
                $date['week7']           = $v['J'];               
                $date['max_heat']        = $v['K'];
                $date['reserve_heat']    = $v['L'];
                $date['fast_lower']      = 70+$v['L']*0.4;
                $date['fast_upper']      = 70+$v['L']*0.7;
                $date['slow_lower']      = 70+$v['L']*0.6;
                $date['slow_upper']      = 70+$v['L']*0.85;            
		$result = M('sport')->add($date);
                $rtime['report_time']=time();
                $rtime['status']     =3;
                $where="sid="."'".$sn['id']."'";
                M('check')->where($where)->save($rtime);
            }           
            if($result){
                $i++;
            }else{
                $e++;
            }          
        }
        if($i>0){           
            $data1['status'] =   1;          
            $data1['info'].='成功导入'.$i.'条;';
        }
        if($e>0){
            $data1['status'] =   0;
            $data1['info'].='导入失败'.$e.'条;';   
        }
        echo json_encode($data1);
        //print_r($info);

    }
}