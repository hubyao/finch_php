<?php
class comm_controller extends controller{
	/**
	 * 初始化网站系统
	 */
	public function __construct(){
		$this->init();
	}

	/*
	初始化系统
	*/
	public function init(){
		//获取网站配置文件
		$site = $this->db->table('system')->where("name='site_config'")->getval('value');
		$site = unserialize($site);
		$this->view['site'] = $site;
	}
	
	public function send_mail(){
		//标题或姓名
		$title=$_POST['u_title'];
		//内容
		$content=$_POST['u_content'];
		//连接数据库
		$maillist=$this->db->table('email')->where('id=1')->get();
		//发送错误返回页面
		$href="../send";
		//后台收件人邮箱
		$toemail=$maillist["email_receiver"];
		$arry = preg_split("/[\s,;]+/",$toemail);
		//邮件点击跳转网页
		$baidu ='http://4399.com';
		$time=time();
		// var_dump($time);
		//判断邮箱类型代码
		$maile = preg_split("/[\s@]+/",strstr($maillist["email_sender"], '@'));
		//样式
		$ppap ='<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 100%;min-width: 100%;/* border-collapse: collapse; */mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%" class="mcnTextContentContainer"><tbody><tr><td align="center"><table align="center" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" valign="top" background="http://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1505799645345&di=eef4e37ab83a31593e59b1c1b7e93b40&imgtype=0&src=http%3A%2F%2Fpic2.16pic.com%2F00%2F00%2F09%2F16pic_9076_b.jpg" width="600" height="242" style="background-repeat:repeat; padding: 0px 0px 0px 0px"><table align="center" cellpadding="0" cellspacing="0"><tbody><tr><td colspan="4" align="center" width="600" height="37"></td></tr><tr><td align="center"><table align="center" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" width="35" height="205"></td><td align="center" valign="top" style="font-family:\'Oswald\', Haettenschweiler, Arial, Helvetica, sans-serif; color:white; font-size:10px; -webkit-text-size-adjust:none; padding:0px 0px 0px 0px;"><a href="'.$baidu.'" name="twitchcon" target="_blank" style="text-decoration:none; color:white"><img src="http://www.easyicon.net/api/resizeApi.php?id=518333&size=128" width="168" height="168" style="border:none; display:block"></a></td><td align="center" valign="top" style="font-family:\'Oswald\', Haettenschweiler, Arial, Helvetica, sans-serif; color:white; font-size:10px; -webkit-text-size-adjust:none; padding:10px 0px 0px 0px;"><a href="'.$baidu.'" name="twitchcon" target="_blank" style="text-decoration:none; color:white"><h1>某神秘网站的神秘邮件！！</h1><br><h2>'.$title.'<br/>'.$content.'</h2></a></td><td align="center" width="35" height="205"></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />';
		 /*评论、回复邮件开始*/ 
		require './lib/PHPMailer-master/PHPMailerAutoload.php';
   

		$mail = new PHPMailer;
		$mail->isSMTP();               // Set mailer to use SMTP
		$mail->Host = "smtp.".$maile[1];  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;          // Enable SMTP authentication
		$mail->Username = $maillist["email_sender"];      // SMTP username
		$mail->Password = $maillist["email_password"];    // SMTP password
		$mail->SMTPSecure = 'SSL';			//SSL or TLS
		$mail->Port = $maillist["email_smtp"];   // TCP port to connect to
		$mail->CharSet  = "UTF-8"; //字符集 
		$mail->Encoding = "base64"; //编码方式
		$mail->From = $maillist["email_sender"];
		$mail->FromName = '我的网站';
		foreach($arry as $key=>$val){
		            $mail->addAddress($val);
		}
		$mail->isHTML(true);      // Set email format to HTML
		//  var_dump($mail);
		// die();
		$mail->Subject = '[NPC]提示您有一封留言邮件';
		$mail->Body    = $ppap;
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
		echo "<div style='width:300px; margin:36px auto;'>";
			if(!$mail->send()){
				echo "对不起，邮件发送失败！请检查邮箱填写是否有误。";
				echo "失败原因: " . $mail->ErrorInfo;
				echo "<a href='$href'>点此返回</a>";
				exit();
			}
			echo "恭喜！邮件发送成功！！";
			echo "<a href='$href'>点此返回</a>";
			echo "</div>";

	}
}
?>