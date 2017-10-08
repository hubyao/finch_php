<?php
header('Content-Type: text/html; charset=utf-8');

require '/app/admin/lib/PHPMailer/PHPMailerAutoload.php';
if(!defined('InEmpireCMS'))
{
    exit();
}


$file       =   $_FILES['file']['tmp_name'];
$file_name  =   $_FILES['file']['name'];
move_uploaded_file($file,'../../member/message/'.$file_name);
$destination='../../member/message/'.$file_name;

$mail = new PHPMailer;

$u_mail = F::input('post.mail');
$u_title =F::input('post.title');
$u_content = F::input('post.mycontent');

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.163.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'y1127056420@163.com';                 // SMTP username
$mail->Password = 'Y1127056420';                           // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;                                    // TCP port to connect to

$mail->From = 'y1127056420@163.com';
$mail->FromName = 'admin';
$mail->addAddress($u_mail);     // Add a recipient
//$mail->addAddress('75952895@qq.com');               // Name is optional
//$mail->addReplyTo('info@example.com', 'Information');
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments

$mail->addAttachment($file_name, $file_name);    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $u_title;
$mail->Body    = $u_content;
// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'err';
  
   echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    $link        =   db_connect();
    $empire      =   new mysqlquery(); //声明数据库操作类
    $date        =   time();
    $content     =   F::input('post.mycontent');
    $sql         =   "INSERT INTO phome_messages(mycontent, add_time,userpic) VALUES('{$content}',$date,'$file_name')";
    $empire->query($sql);
    db_close();  //关闭MYSQL链接
    $empire  = null; //注消操作类变量
    echo 'ok';

}

?>