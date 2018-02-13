<?php
namespace app\api\controller;

use think\Db;
use think\Validate;

use app\api\common\Base;

class Demo extends Base{

	protected $userData;
	protected $uid;

	public function __construct(){
		$this->userData = parent::__construct();
		$this->uid = $this->userData['uid'];
	}


    /**
     * 添加记录
     * api post api.php/api/{controller}/{method}
     * @return integer $code 状态码
     * @return string $msg 提示信息
     */
    public function insert(){
        $rule = [];
        $validate = new Validate();
        $paramData = input('post.');
        if(!$validate->check($rule)){
            return make_return_json(500,$validate->getError());
        }

        $insertData = [];

        $id = Db::name()->insertGetId($insertData);
        if($id){
            // write_log();
            return make_return_json(200,'添加成功');
        }else{
            return make_return_json(500,'添加失败');
        }
    }

    /**
     * 查看所有列表
     * api get/param api.php/api/{controller}/{method}
     * @param integer $page 页数
     * @param integer $limit 每页个数
     * @param integer $search 可选,搜索内容
     * @param string $start_time 可选,开始日期
     * @param string $end_time 可选,结束日期
     */
    public function lists(){

        $rule = [
            'page|页数' => 'require|integer|>:0',
            'limit|每页个数' => 'require|integer|>:0',
            'search|状态' => 'in:1,2,3,4'
        ];
        $paramData = input('param.');
        $validate = new Validate($rule);
        if(!$validate->check($paramData)){
            return make_return_json(500,$validate->getError());
        }

        // 搜索+时间
        // 搜索
        // 时间
        // 全部
        if(empty($paramData['start_time'])!=true and empty($paramData['end_time'])!=true and empty($paramData['search'])!=true){
            $list = Db::name('order')->where('mix','like',"%{$paramData['search']}%")->whereTime('create_time','between',[$paramData['start_time'],$paramData['end_time']])->order('oid','desc')->select();
        }elseif(empty($paramData['search'])!=true){
            $list = Db::name('order')->where('mix','like',"%{$paramData['search']}%")->order('oid','desc')->select();
        }elseif(empty($paramData['start_time'])!=true and empty($paramData['end_time'])!=true){
            $list = Db::name('order')->whereTime('create_time','between',[$paramData['start_time'],$paramData['end_time']])->order('oid','desc')->select();
        }else{
            $list = Db::name('order')->order('oid','desc')->select();
        }

        $data = page($list,$paramData['page'],$paramData['limit']);
        for($i=0;$i<count($data);$i++){
            $data[$i]['create_time'] = date('Y-m-d H:i:s',$data[$i]['create_time']);
            $data[$i]['recv_time'] = empty($data[$i]['recv_time'])? '(尚未)' :date('Y-m-d H:i:s',$data[$i]['recv_time']);
            $data[$i]['over_time'] = empty($data[$i]['over_time'])? '(尚未)' :date('Y-m-d H:i:s',$data[$i]['over_time']);
            $data[$i]['uid_name'] = get_user_name($data[$i]['uid']);
            $data[$i]['status'] = get_order_status($data[$i]['status']);
            $buData = Db::name('order_bu')->where('oid',$data[$i]['oid'])->find();
            if(!$buData){
                $data[$i]['bu'] = '待录入';
            }else{
                $data[$i]['bu'] = get_order_bu_status($buData['status']);
            }
            $data[$i]['uid'] = get_user_name($data[$i]['uid']);
            $data[$i]['cuid'] = get_user_name($data[$i]['cuid']);
        }

        return make_layui_table($data,$list);
    }


    // ----- 看着用

    /**
     * 动态搜索订单列表
     * api get/param api.php/api/{controller}/{method}
     * @param integer $page 页数
     * @param integer $limit 每页个数
     * @param mixed $xxx.. 可选,搜索关键词,可以多个
     * @param mixed $xxx.. 可选,搜索关键词,可以多个
     */
    public function search_list(){
        $rule = [
            'page|页数' => 'require|integer|>:0',
            'limit|每页个数' => 'require|integer|>:0'
        ];
        $paramData = input('param.');
        $validate = new Validate($rule);
        if(!$validate->check($paramData)){
            return make_return_json(500,$validate->getError());
        }

        $col = [];
        if(count($paramData)>2){
            foreach($paramData as $key => $value){
                if($key!='page' and $key!='limit' and empty($value)!=true){
                    $new = [
                        'left' => $key,
                        'right' => $value
                    ];
                    $col[] = $new;
                }
            }
        }

        if(count($col)!=0){
            // 生成sql
            $stratTime = strtotime('00:00');
            $endTIme = strtotime('24:00');
            $sql = "select * from f_order_bu where uid={$this->uid} and (create_time between {$stratTime} and {$endTIme}) and (";
            for ($i=0; $i < count($col); $i++) {
                $sql .= "({$col[$i]['left']} like '%{$col[$i]['right']}%') or";
            }
            $sql = substr($sql,0,-2);
            $sql .= ") order by create_time desc;";
            $list = Db::query($sql);
        }else{
            // 默认搜索
            $list = Db::name('order_bu')->where('uid',$this->uid)->whereTime('create_time','today')->order('create_time','desc')->select();
        }


        $data = page($list,$paramData['page'],$paramData['limit']);
        for($i=0;$i<count($data);$i++){
            $data[$i]['create_time'] = date('Y-m-d H:i:s',$data[$i]['create_time']);
        }

        return make_layui_table($data,$list);
    }

    /**
     * 导出数据到excel
     * api post api.php/api/{controller}/{method}
     * @param mixed $xxx.. 可选,搜索关键词,可以多个
     * @param mixed $xxx.. 可选,搜索关键词,可以多个
     */
    public function output(){
        $paramData = input('param.');

        $col = [];
        foreach($paramData as $key => $value){
            $new = [
                'left' => $key,
                'right' => $value
            ];
            $col[] = $new;
        }

        if(empty($col)){
            return make_return_json(500,'空参数');
        }

        $fields = '';
        foreach ($col as $value) {
            $fields .= $value['left'].',';
        }
        $fields = substr($fields,0,-1);

        // 改这里
        // $data = Db::name('user')->field($fields)->limit(10)->select();
        $data = Db::name('order_bu')->field($fields)->select();

        import('Excel.PHPExcel', EXTEND_PATH);


        $objPHPExcel = new \PHPExcel();
        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("freeloop工作室")
        	->setLastModifiedBy("freeloop工作室")
        	->setTitle("数据EXCEL导出")
        	->setSubject("数据EXCEL导出")
        	->setDescription("备份数据")
        	->setKeywords("excel")
        	->setCategory("result file");

        /*表头*/

        /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
        $num = 1;

        $fuckFun = '$objPHPExcel->setActiveSheetIndex(0)';
        for($i=1;$i<=count($col);$i++){
            $fuckFun .= '->setCellValue(\''.number_to_letter($i) . $num.'\', \''.$col[$i-1]['right'].'\')';
        }
        $fuckFun .=';';


        foreach ($data as $k => $v) {
        	$num = $num + 1;
        	// $objPHPExcel->setActiveSheetIndex(0);
            $fuckFun .= '$objPHPExcel->setActiveSheetIndex(0)';
            for($i=1;$i<=count($col);$i++){
                $fuckFun .= '->setCellValue(\''.number_to_letter($i) . $num.'\', \''.$v[$col[$i-1]['left']].'\')';
            }
            $fuckFun .=';';
        }

        $fuckFun .= '$objPHPExcel->getActiveSheet()->setTitle(\'User\');';
        $fuckFun .= '$objPHPExcel->setActiveSheetIndex(0);';
        $fuckFun .= 'return $objPHPExcel;';
        $fuck = create_function('$objPHPExcel',$fuckFun);
        $objPHPExcel = $fuck($objPHPExcel);

        header('Content-Type: application/vnd.ms-excel');
        $now = date('m_d_Hi', time());
        header('Content-Disposition: attachment;filename=' . $now . '.xls');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }


}
