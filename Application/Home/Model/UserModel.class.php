<?php
namespace Home\Model;

class UserModel extends CommonModel {

	protected $fields = array('id','username','password','salt','email','status','tel');
    public function register($username,$password,$email,$tel){
		$info = $this->where("username='$username'")->find();
		if($info){
			$this->error = '用户名重复！';
			return false;
		}

		$salt = rand(10000,99999);
		$db_password = md5(md5($password).$salt);
		$data = array(
			'username' => $username,
			'password' => $db_password,
			'salt' => $salt,
            'email'=>$email,
            'tel'=>$tel,
		);

		return $this->add($data);


    }

    public function login ($username,$password){

		$info = $this->where("username='$username'")->find();

		if(!$info){
			$this->error = '用户名不存在！';
			return false;
		}

		$pwd = md5(md5($password).$info['salt']);
		if($pwd != $info['password']){
			$this->error = '密码不对！';
			return false;
		}

		session('user',$info);
		session('user_id',$info['id']);

		D('Cart')->cookie2db();
		return true;

    }

    public function email($username,$email){

        $text= "$username,你好！欢迎注册JXSHOP会员。<a href='local.jxshop.com/Home/User/jihuo?username=$username'>点击此处激活邮件</a>";

        require '../email/class.phpmailer.php';
        $mail             = new \PHPMailer();

        $mail->IsSMTP();
        $mail->SMTPAuth   = true;
        $mail->Host       = 'smtp.163.com';
        $mail->Username   = 'fanerdo';
        $mail->Password   = '3890195d';
        /*ÄÚÈÝÐÅÏ¢*/
        $mail->IsHTML(true);
        $mail->CharSet    ="UTF-8";
        $mail->From       = 'fanerdo@163.com';
        $mail->FromName   ="商城管理老大";
        $mail->Subject    = '激活邮件';
        $mail->MsgHTML($text);


        $mail->AddAddress($email);
        //$mail->AddAttachment("./test/test.png");
        return $mail->Send();

    }


    public function checkUser($username){

        $res = $this->where("username='$username'")->find();
        //dump($res);die;
        if(!$res){
           return false;
        }

        $row = $this->where('id='.$res['id'])->setField('status',1);
        return $row;
    }

}