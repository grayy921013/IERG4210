<?php
require_once('class.phpmailer.php');
require_once("class.smtp.php"); 
$mail  = new PHPMailer(); 

$mail->CharSet    ="UTF-8";                 //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置为 UTF-8
$mail->IsSMTP();                            // 设定使用SMTP服务
$mail->SMTPAuth   = true;                   // 启用 SMTP 验证功能
$mail->SMTPSecure = "ssl";                  // SMTP 安全协议
$mail->Host       = "smtp.gmail.com";       // SMTP 服务器
$mail->Port       = 465;                    // SMTP服务器的端口号
$mail->Username   = "grayy921013@gmail.com";  // SMTP服务器用户名
$mail->Password   = "ZZh!930313";        // SMTP服务器密码
$mail->SetFrom('grp14@ierg4210.ie.cuhk.edu.hk', 'admin');    // 设置发件人地址和名称
$mail->Subject    = '';                     // 设置邮件标题
$mail->AltBody    = "为了查看该邮件，请切换到支持 HTML 的邮件客户端"; 
                                            // 可选项，向下兼容考虑
$mail->MsgHTML('hahahha!');                         // 设置邮件内容
$mail->AddAddress('419281699@qq.com', "grayy");
//$mail->AddAttachment("images/phpmailer.gif"); // 附件 
if(!$mail->Send()) {
    echo "fail：" . $mail->ErrorInfo;
} else {
    echo "congratulation!";
}
?>