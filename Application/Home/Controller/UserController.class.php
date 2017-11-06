<?php
namespace Home\Controller;

class UserController extends CommonController {
    public function register(){

		if(IS_GET){
			$this->display();
		}else{
			$username = I('post.username');
			$password = I('post.password');
			$checkcode = I('post.checkcode');
			$email = I('post.email');
			$tel = I('post.tel');
            $msgtel = I('post.msgtel');
            //dump($email);die;
			$obj =  new \Think\Verify();
			if(!$obj->check($checkcode)){
				$this->ajaxReturn(array('status'=>0,'msg'=>'验证码错误！'));

			}

			$msgcode = session('msgcode');
			//dump($msgcode);die;
			if(!$msgcode || $msgcode!=$msgtel){

                $this->ajaxReturn(array('status'=>0,'msg'=>'短信验证码错误！！'));
            }

			$model = D('User');

            $resEmail = $model->email($username,$email);
            if(!$resEmail){
                $this->ajaxReturn(array('status'=>0,'msg'=>'邮件发送失败！'));
            }

			$res = $model->register($username,$password,$email,$tel);
			if(!$res){
				$this->ajaxReturn(array('status'=>0,'msg'=>$model->getError()));
			}
            session('msgcode',null);
			$this->ajaxReturn(array('status'=>1,'msg'=>'注册成功！请接收邮件进行认证激活！'));
			header("location:U('login')");

		}

    }

    public function code(){

    	$config = array('codeSet'=>'123','length'=>3);
	    $obj = new \Think\Verify($config);
	    $obj ->entry();
    }

	public function login(){

    	if(IS_GET){

    		$this->display();
	    }else{
    		$username = I('post.username');
    		$password = I('post.password');
    		$checkcode = I('post.checkcode');

    		$obj = new \Think\Verify();
    		if(!$obj->check($checkcode)){

    		    $this->ajaxReturn(array('status'=>'0','msg'=>'验证码错误！'));
				return false;
		    }

		    $model = D('User');
    		$res = $model->login($username,$password);
    		if(!$res){
			    $this->ajaxReturn(array('status'=>'0','msg'=>$model->getError()));
			    return false;
		    }

		    $this->ajaxReturn(array('status'=>'1','msg'=>'登录成功!正在跳转。。。'));

	    }

	}

	public function logout(){
		session('user',null);
		session('user_id',null);
		$this->redirect('/');
	}


    //邮箱激活技术
	public function jihuo(){

	    $username = I('get.username');
        $model = D('User');
        $res = $model->checkUser($username);
	    if(!$res){
            $this->error('非法连接！',U('index/index'));
        }

        $this->success('激活成功！',U('User/login'));
    }

    //微博登陆
    public function weiboLogin(){

        session_start();

        include_once( '../sina/config.php' );
        include_once( '../sina/saetv2.ex.class.php' );

        $o = new \SaeTOAuthV2( WB_AKEY , WB_SKEY );

        $code_url = $o->getAuthorizeURL( WB_CALLBACK_URL );
        //dump($code_url);die;
        //echo '<a href="'.$code_url.'"><img src="/sina/weibo_login.png" title="点击进入授权页面" alt="点击进入授权页面" border="0" /></a>';

        header("location:$code_url");

    }


    public function callback(){

        session_start();

        include_once( '../sina/config.php' );
        include_once( '../sina/saetv2.ex.class.php' );

        $o = new \SaeTOAuthV2( WB_AKEY , WB_SKEY );
        //dump($o);die;
        if (isset($_REQUEST['code'])) {
            $keys = array();
            $keys['code'] = $_REQUEST['code'];
            $keys['redirect_uri'] = WB_CALLBACK_URL;
            try {
                $token = $o->getAccessToken( 'code', $keys ) ;
            } catch (OAuthException $e) {
            }
        }

        //dump($token);die;
        if ($token) {
            $_SESSION['token'] = $token;
            setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );

            //echo '授权完成,<a href="weibolist.php">进入你的微博列表页面</a><br />';

           //header('location:'.U('xinxi'));
            $this->xinxi();

        } else {

            echo '授权失败。';

        }


    }

    public function xinxi()
    {
        session_start();

        include_once('../sina/config.php');
        include_once('../sina/saetv2.ex.class.php');


        $c = new \SaeTClientV2(WB_AKEY, WB_SKEY, $_SESSION['token']['access_token']);
        $ms = $c->home_timeline(); // done
        $uid_get = $c->get_uid();
        $uid = $uid_get['uid'];
        $user_message = $c->show_user_by_id($uid);//根据ID获取用户等基本信息

//        echo "<pre>";
//        var_dump($user_message);
//        die;

        $res = M('User')->where('sina='.$user_message['id'])->find();
        if($res){
            session('user',$res);
            session('user_id',$res['id']);
            header('location:'.U('Index/index'));

        }else{
            $password = 123456;
            $salt = rand(10000,99999);
            $db_password = md5(md5($password).$salt);

            $data = array(
                'username' => $user_message['name'],
                'sina' => $user_message['id'],
                'password' => $db_password,
                'salt'  => $salt,
                'status' => 1,
            );

            $id = M('User')->add($data);

            $res = M('User')->find($id);
            //dump($res);die;
            session('user',$res);
            session('user_id',$res['id']);
            header('location:'.U('Index/index'));
        }


    }


    public function sendMsg(){
        $tel = I('post.tel');

        if(!$tel){
            $this->ajaxReturn(array('status'=>0,'msg'=>'手机号不允许为空！！'));die;
        }

        $code = rand(1000,9999);
        //$code = '我们的秒杀图呢';
        $res = $this->sendTemplateSMS($tel,array($code,'60'),"1");
        //dump($res);die;
        if(!$res){
            $this->ajaxReturn(array('status'=>0,'msg'=>'信息发送失败！'));
        }
        session('msgcode',$code);
        $this->ajaxReturn(array('status'=>1,'msg'=>'发送成功！'));

    }



    function sendTemplateSMS($to,$datas,$tempId)
    {
        include_once("../msg/CCPRestSmsSDK.php");
        //dump($row);die;
        //主帐号,对应开官网发者主账号下的 ACCOUNT SID
        $accountSid= '8aaf07085f004cdb015f1382e83d086e';

        //主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
        $accountToken= '801831c7db3a40529e8c79eb3ec15975';

        //应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
        //在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
        $appId='8aaf07085f004cdb015f1384307b089d';

        //请求地址
        //沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
        //生产环境（用户应用上线使用）：app.cloopen.com
        $serverIP='sandboxapp.cloopen.com';


        //请求端口，生产环境和沙盒环境一致
        $serverPort='8883';

        //REST版本号，在官网文档REST介绍中获得。
        $softVersion='2013-12-26';

        // 初始化REST SDK
       // global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
        $rest = new \REST($serverIP,$serverPort,$softVersion);
        //echo "<pre>";

        $rest->setAccount($accountSid,$accountToken);
        //dump($rest);die;
        $rest->setAppId($appId);

        // 发送模板短信
        //echo "Sending TemplateSMS to $to <br/>";
        $result = $rest->sendTemplateSMS($to,$datas,$tempId);
        //var_dump($result);die;
        if($result == NULL ) {
            //echo "result error!";
            return false;
            //break;
        }
        if($result->statusCode!=0) {
            //echo "error code :" . $result->statusCode . "<br>";
           // echo "error msg :" . $result->statusMsg . "<br>";
            return false;
        }else{
            //echo "Sendind TemplateSMS success!<br/>";
            // 获取返回信息
            $smsmessage = $result->TemplateSMS;
            //echo "dateCreated:".$smsmessage->dateCreated."<br/>";
           // echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
            //TODO 添加成功处理逻辑
            return $smsmessage;
        }
    }





}