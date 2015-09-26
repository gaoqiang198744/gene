<?php
namespace Manage\Controller;
class FoodController extends CommonController {
    public function index() {
        $this->display();
    }
    	//上传方法
     function upload2()
    {
        header("Content-Type:text/html;charset=utf-8");
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('xls', 'xlsx');// 设置附件上传类
        $upload->savePath  =      '/'; // 设置附件上传目录
        // 上传文件
        $info   =   $upload->uploadOne($_FILES['excelData']);
        $filename = './Uploads'.$info['savepath'].$info['savename'];
        $exts = $info['ext'];
        //print_r($info);exit;
        if(!$info) {// 上传错误提示错误信息
            $data1['status'] =   0;
            $data1['info']=$upload->getError();
            echo json_encode($data1);             
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
			$date['food_type']  = $v['A'];
			$date['food_name'] = $v['B'];
                        if(!$v['B']){
                            echo json_encode(array("info"=>'第二列数据为空'));
                            exit;
                        }
                        $date['food_another']  = $v['C'];
                        $date['weight']  = $v['D'];
                        $date['eat_part']  = $v['E'];
                        $date['heat']  = $v['F'];
                        $date['portein']  = $v['G'];
                        $date['fat']  = $v['H'];
                        $date['ash']  = $v['I'];
                        $date['carbohydrate']  = $v['J'];
                        $date['renieratene']  = $v['K'];
                        $date['niacin']  = $v['L'];
                        $date['fibre']  = $v['M'];
                        $date['cholestenone']  = $v['N'];
                        $date['va']  = $v['O'];
                        $date['vc']  = $v['P'];
                        $date['vd']  = $v['Q'];
                        $date['ve']  = $v['R'];
                        $date['oryzanin']  = $v['S'];
                        $date['actochrome']  = $v['T'];
                        $date['acid']  = $v['U'];
                        $date['ca']  = $v['V'];
                        $date['p']  = $v['W'];
                        $date['k']  = $v['X'];
                        $date['na']  = $v['Y'];
                        $date['mg']  = $v['Z'];
                        $date['fe']  = $v['AA'];
                        $date['zn']  = $v['AB'];
                        $date['se']  = $v['AC'];
                        $date['cu']  = $v['AD'];
                        $date['mn']  = $v['AE'];                       
                        $date['add_time']     = time();
			$result = M('food')->add($date);
                        
        }
        if($result){
            
            $data1['status'] =   1;
            $data1['url']    = '';
            $data1['info']='导入成功';
            $data1['sql']=M('food')->getlastsql();
            $data1['data']=$data;
           echo json_encode($data1);
 //           $this->success('产品导入成功', '',$date);
        }else{
            $data1['status'] =   0;
            $data1['info']='导入失败';
            echo json_encode($data1);
        }
        //print_r($info);

    }

    //导出数据方法
    function goods_export()
    {
		$goods_list = M('user')->select();
        //print_r($goods_list);exit;
        $data = array();
        foreach ($goods_list as $k=>$goods_info){
			$data[$k][id] = $goods_info['id'];
            $data[$k][name] = $goods_info['name'];
            $data[$k][sex] = $goods_info['sex'];
        }
        //print_r($goods_list);
        //print_r($data);exit;

        foreach ($data as $field=>$v){
            if($field == 'id'){
                $headArr[]='序号';
            }

            if($field == 'name'){
                $headArr[]='姓名';
            }
			
			if($field == 'sex'){
                $headArr[]='年龄';
            }
        }

        $filename="goods_list";

        $this->getExcel($filename,$headArr,$data);
    }


    function getExcel($fileName,$headArr,$data){
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.IOFactory.php");

        $date = date("Y_m_d",time());
        $fileName .= "_{$date}.xls";

        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();

        //设置表头
        $key = ord("A");
        //print_r($headArr);exit;
        foreach($headArr as $v){
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();

        //print_r($data);exit;
        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j.$column, $value);
                $span++;
            }
            $column++;
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        //$objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }
}