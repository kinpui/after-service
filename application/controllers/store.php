<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store extends CI_controller {

  public $menu = 'store';
  public function __construct()
  {
    parent::__construct();

    $this->load->helper(array('url','login','header'));
    $this->load->library('session');

    check_login();

    /* 加载模型器 */
    $this->load->model('Stores');
  }

  public function index()
  {
    $data = array();
    $data = $this->get_user_info($data);

    $this->load->view('header',page_header(
      '门店售后员',
      '门店售后员主页',
      $this->menu
    ));
    $this->load->view('store/index',$data);
  }

  /**
   *
   * 添加送修表
   *
   **/
  public function add()
  {
    $this->load->helper('form');
    $data = array();
    $data = $this->get_user_info($data);

    /* 视图 */
    $this->load->view('header',page_header(
      '添加送修表',
      '送修表填写',
      $this->menu
    ));

    $this->load->view('store/add',$data);
    $this->load->view('footer');
  
  }

  /**
   *
   * 显示送修列表
   *
   **/
  public function table()
  {
    $this->load->helper(array('page','form','search'));
    $data = array();
    $data = $this->get_user_info($data);

    $header = page_header(
      '送修列表',
      '我的送修记录',
      $this->menu
    );
    $header['css'] = get_search_css();

    $this->load->view('header',$header);

    /* 分页 */
    $total_rows   = $this->Stores->get_table_num()[0]['COUNT(id)'];
    $url          = site_url('store/table');
    $page_config  = set_page($url,$total_rows);
    $data['page'] = $this->pagination->create_links();

    $data['table']= $this->Stores->get_store_table($page_config['nowindex'],$page_config['per_page']);
    
    /* 视图 */
    $search       = get_search_data();
    $search['action']='store/search'; 
    $this->load->view('publics/search',$search);
    $this->load->view('store/table.php',$data);
    $this->load->view('footer',get_search_js());
  }

  /**
   *
   * 显示待处理事项
   *
   **/
  public function wait()
  {
    $data = array();
    $data = $this->get_user_info($data);

    /* 查询待接收 */
    $data['table'] = $this->Stores->get_wait_table();
  
    
    if(empty($data['table']))
    {
      echo '无待接收';
      //return false;
    }else{
      echo '有待接收';
    }

    /* 视图 */
    $this->load->view('header',page_header(
      '待处理列表',
      '待处理记录',
      $this->menu
    ));
    
    $this->load->view('store/wait.php',$data);
    $this->load->view('footer');
  }


  /**
   * 显示客户可取回的设备
   **/
  public function take()
  {
    $data = array();
    $data = $this->get_user_info($data);

    /* 查询待接收 */
    $data['table'] = $this->Stores->get_take_table();

    /* 视图 */
    $this->load->view('header',page_header(
      '待处理列表',
      '待处理记录',
      $this->menu
    ));
    
    $this->load->view('store/take.php',$data);
    $this->load->view('footer');
  }

  /**
   *  确认接收仓库返回的手机
   **/
  public function receive(){

    $id = empty($this->uri->segment(3))?'':$this->uri->segment(3);
    if($id)
    {
      $result = $this->Stores->receive($id);
      if(!$result){
        echo '接机失败,请重试';
      }else{
        echo '接收成功';
      }
    }else{
      echo '非法操作';
    }
  }

  /**
   *  确认接收仓库返回的手机
   **/
  public function take_h(){

   $id = empty($this->uri->segment(3))?'':$this->uri->segment(3);
   if($id)
   {
     $result = $this->Stores->take_h($id);
     if($result){
       echo '接收成功';
     }else{
       echo '取机失败,请重试';
     }
   }else{
     echo '非法操作';
   }
  }


  /**
   *
   * 用户session 信息
   *
   **/
  protected function get_user_info($data)
  {
    $data['nickname'] = $this->session->userdata('nickname');
    $data['username'] = $this->session->userdata('username');
    $data['userid']   = $this->session->userdata('userid');
    $data['usertype'] = $this->session->userdata('usertype');
    $data['sector'] = $this->session->userdata('sector');

    return $data;
  }

  /**
   *
   * 验证用户提交的送修表单并存储
   *
   **/
  public function submit_form()
  {
    $this->load->library('form_validation');
    $this->load->database();/* 初始化数据库连接 */

    /* 验证部分字段不为空 */

    $this->form_validation->set_rules('buy_date','Buy_date','callback_check_null');  //购买日期
    $this->form_validation->set_rules('customer_name','Customer_name','callback_check_null'); //顾客姓名
    $this->form_validation->set_rules('customer_name','Customer_name','callback_check_null'); //顾客姓名
    $this->form_validation->set_rules('customer_phone','Customer_phone','callback_check_null');  //联系电话
    $this->form_validation->set_rules('brand','Brand','callback_check_null');  //品牌
    $this->form_validation->set_rules('string_code','String_code','callback_check_null');  //串码
    $this->form_validation->set_rules('remarks','Remarks','callback_check_null');  //故障原因

    if ($this->form_validation->run() == FALSE)
    {
      /* 返回添加页面 */
      redirect('store/add');
    }else{

      /* 所属门店 */
      $sector = $this->session->userdata('sector');

      /* 运送状态 (门店送往仓库)*/
      $state  = '1';

      /* 获取数据 */
      $data = array(
        'buy_date'      => $this->input->post('buy_date'),
        'customer_name' => $this->input->post('customer_name'),
        'customer_phone'=> $this->input->post('customer_phone'),
        'brand'         => $this->input->post('brand'),
        'string_code'   => $this->input->post('string_code'),
        'appearance'    => $this->input->post('appearance'),
        'screen'        => $this->input->post('screen'),
        'parts'         => $this->input->post('parts'),
        'digital_type'  => $this->input->post('digital_type'),
        'digital_color' => $this->input->post('digital_color'),
        'fault'         => $this->input->post('fault'),
        'remarks'       => $this->input->post('remarks'),
        'repair_type'   => $this->input->post('repair_type'),
        'repair_type'   => $this->input->post('repair_type'),
        'start_date'    => date('Y-m-d'),
        'state'         => $state,
        'from_s'        => $sector
      );


      /* 插入数据库 */
      if($this->db->insert('records', $data)){
        $data = array(
          'tips'=>'插入成功'
        );
        $this->load->view('store/add_success',$data);
      }else{
        $this->load->view('store/add_fail');
      }
    }

  }

  /**
   *
   * 验证字符串不为空
   *
   **/
  public function check_null($str)
  {
    if( $str == '' ){
      $this->form_validation->set_message('customer_name','带*的为必填表单');
      return false;
    }else{
      return true;
    }
  }

  /**
   * 删除用户送修记录
   **/
  public function del()
  {

    $id = empty($this->uri->segment(3))?'':$this->uri->segment(3);

    if($id == ''){
      echo '非法操作';
      return false;
    }else{
      /* 获取该记录转态。如果大于等于2。则失败  */
      if($this->Stores->get_record_status($id))
      {
        if($this->Stores->del_record($id))
        {
          echo '已经删除';
        }else{
          echo '删除不成功';
        }
      }else{
        echo '已经送出。无法删除';
      }
    }
  }


}
