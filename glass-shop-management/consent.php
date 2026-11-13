<?php
require_once 'functions.php';require_roles(['admin','manager']);verify_csrf();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $pdo->prepare('UPDATE users SET notify_email=?,notify_sms=?,notify_whatsapp=?,consent_asked=1 WHERE id=?')->execute([isset($_POST['email'])?1:0,isset($_POST['sms'])?1:0,isset($_POST['whatsapp'])?1:0,user()['id']]);
 header('Location: index.php');exit;
}
page_header('Notification preferences');?><section class="panel narrow"><h1>Notification consent</h1><p>Choose which business notifications you want to receive. You can change these preferences later.</p><form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><label class="check"><input type="checkbox" name="email"> Email notifications</label><label class="check"><input type="checkbox" name="sms"> SMS notifications</label><label class="check"><input type="checkbox" name="whatsapp"> WhatsApp notifications</label><button>Save preferences</button></form></section><?php page_footer();?>
