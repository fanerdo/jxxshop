<?php

namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller
{
	public $is_check_rule = true;
	public $user = array();

	public function __construct()
	{
		parent::__construct();

		$admin = cookie('admin');
		if (!$admin) {
			$this->error('没有登陆！', U('Login/login'));
		}

		//$this->user = S('user_'.$admin['id']);

        S(array('type'=>'Memcache','host'=>'localhost','port'=>'8889'));
        $this->user = S('user_'.$admin['id']);

        //dump($this->user);die;

		if (!$this->user) {
			//echo '11';
			$this->user = $admin;
			$role_info = M('AdminRole')->where("admin_id =" . $admin['id'])->find();
			//dump($role_info);die;
			$this->user['role_id'] = $role_info['role_id'];
			$ruleModel = D('Rule');

			if ($role_info['role_id'] == 1) {
				$this->is_check_rule = false;
				$rule_list = $ruleModel->select();
               // dump($rule_list);die;
			} else {
				$rules = D('RoleRule')->getRules($role_info['role_id']);

				foreach ($rules as $v) {
					$rules_ids[] = $v['rule_id'];
				}

				$rules_ids = implode(',', $rules_ids);
				//dump($rules_ids);die;

				$rule_list = $ruleModel->where("id in ($rules_ids)")->select();
				//dump($rule_list);die;

			}

			foreach ($rule_list as $k => $v) {
				$this->user['rules'][] = strtolower($v['module_name'] . '/' . $v['controller_name'] . '/' . $v['action_name']);
				//dump($this->user['rules']);
				if ($v['is_show'] == 1) {
					$this->user['menus'][] = $v;
				}
			}
			//die;
			//dump($this->user);die;
			S('user_' . $admin['id'], $this->user,60);
		}

		//dump($this->user);die;
		if ($this->user['role_id'] == 1) {
			$this->is_check_rule = false;
		}

		if ($this->is_check_rule) {

			$this->user['rules'][] = 'admin/index/index';
			$this->user['rules'][] = 'admin/index/top';
			$this->user['rules'][] = 'admin/index/menu';
			$this->user['rules'][] = 'admin/index/main';

			$action = strtolower(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME);
			//dump(in_array($action, $this->user['rules']));die;

			if (!in_array($action, $this->user['rules'])) {

				if (IS_AJAX) {
					$this->ajaxReturn(array('status' => 1, 'msg' => '没有权限！'));
				} else {
					echo '没有权限！';
					die;
				}
			}
		}

	}


}