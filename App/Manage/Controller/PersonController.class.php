<?php
namespace Manage\Controller;
class PersonController extends CommonController {
    public function index() {
        $this->display();
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
        $info   =   $upload->uploadOne($_FILES['excelData']);
        $filename = './uploads'.$info['savepath'].$info['savename'];
        $exts = $info['ext'];
        //print_r($info);exit;
        if(!$info) {// 上传错误提示错误信息
              $this->error($upload->getError());
          }else{// 上传成功
            
                  $this->goods_import($filename, $exts);
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
        $this->save_import($data);
    }

    //保存导入数据
    function save_import($data)
    {

        //print_r($data);exit;
        foreach ($data as $k=>$v){
			//通过检测编码，取出sid
                        $where="sn="."'".$v['A']."'";
                        $sn = M('sn')->where($where)->find();
                        if($sn){
                             if($sn['member_id']==0){
                                $data1['status'] = 0;                              
                                $data1['info']='检测编号为'.$v['A'].'未绑定，导入失败';
                                echo json_encode($data1);exit; 
                             }
                        }else{
                                $data1['status'] = 0;                              
                                $data1['info']='检测编号为'.$v['A'].'不存在，导入失败';
                                echo json_encode($data1);exit; 
                        }
                        
                        $date['sid'] = $sn['id'];
			$date['member_name'] = $v['B'];                       
                        $date['sex'] = $v['C'];
                        $date['birthday'] = $v['D'];
                        $date['age'] = $v['E'];
                        $date['height'] = $v['F'];
                        $date['weight'] = $v['G'];
                        $date['waistline'] = $v['H'];
                        $date['hip'] = $v['I'];
                        $date['heart'] = $v['J'];
                        $date['job'] = $v['K'];
                        $date['sport'] = $v['L'];                      
                        $date['add_time'] = time();
			$result = M('person')->add($date);
                        $date['sn'] = $v['A'];
        }
        if($result){           
            $data1['status'] =   1;
            $data1['url']    = '';
            $data1['info']='导入成功';
            $data1['data']=$data;
           echo json_encode($data1);
 //           $this->success('产品导入成功', '',$date);
        }else{
            $this->error('产品导入失败');
        }
        //print_r($info);

    }
}