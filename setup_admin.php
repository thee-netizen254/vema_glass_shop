<?php
require_once 'functions.php';
$count=(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
if($count){exit('An admin already exists. Delete setup_admin.php now.');}
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $name=trim($_POST['name']); $email=trim($_POST['email']); $phone=trim($_POST['phone']);
 $hash=password_hash($_POST['password'],PASSWORD_DEFAULT);
 $s=$pdo->prepare("INSERT INTO users(name,email,phone,password_hash,role) VALUES(?,?,?,?, 'admin')");
 $s->execute([$name,$email,$phone,$hash]);
 $msg='Admin created. Delete setup_admin.php now, then log in.';
}
page_header('Create first admin'); ?>
<section class="panel narrow"><h1>Create first administrator</h1><p>Use the business owner's details. Keep this account secure.</p>
<?php if($msg): ?><p class="success"><?=h($msg)?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>">
<label>Full name<input name="name" required></label><label>Email<input type="email" name="email"></label><label>Phone (+254...)<input name="phone"></label>
<label>Password<input type="password" name="password" minlength="10" required></label><button>Create admin</button></form></section>
<?php page_footer(); ?>
