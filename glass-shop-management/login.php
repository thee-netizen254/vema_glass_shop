<?php
require_once 'functions.php';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 if(isset($_POST['verify_otp'])){
  $uid=(int)($_SESSION['pending_otp_user']??0); $code=trim($_POST['otp']??'');
  $s=$pdo->prepare('SELECT * FROM otp_codes WHERE user_id=? AND used=0 AND expires_at>NOW() ORDER BY id DESC LIMIT 1');
  $s->execute([$uid]); $otp=$s->fetch();
  if($otp && password_verify($code,$otp['code_hash'])){
   $pdo->prepare('UPDATE otp_codes SET used=1 WHERE id=?')->execute([$otp['id']]);
   $u=$pdo->prepare('SELECT * FROM users WHERE id=?');$u->execute([$uid]);$row=$u->fetch();
   session_regenerate_id(true); $_SESSION['user']=['id'=>$row['id'],'name'=>$row['name'],'role'=>$row['role'],'email'=>$row['email']];
   unset($_SESSION['pending_otp_user']); header('Location: index.php');exit;
  } else $error='OTP is invalid or expired.';
 } else {
  $identity=trim($_POST['identity']??''); $s=$pdo->prepare('SELECT * FROM users WHERE (email=? OR phone=?) AND active=1 LIMIT 1');
  $s->execute([$identity,$identity]);$u=$s->fetch();
  if($u && password_verify($_POST['password']??'',$u['password_hash'])){
   if(in_array($u['role'],['admin','manager'],true)){
    $code=(string)random_int(100000,999999);
    $pdo->prepare('INSERT INTO otp_codes(user_id,code_hash,expires_at) VALUES(?,?,DATE_ADD(NOW(),INTERVAL 5 MINUTE))')->execute([$u['id'],password_hash($code,PASSWORD_DEFAULT)]);
    $_SESSION['pending_otp_user']=$u['id'];
    $destination=$u['email'] ?: $u['phone'];
    // Configure real email/SMS delivery in functions.php before production.
    if(APP_DEBUG_OTP){ $_SESSION['debug_otp']=$code; }
    else { send_notification($u['email']?'email':'sms',$destination,"Your Glass Shop login code is $code"); }
    header('Location: login.php?otp=1');exit;
   }
   session_regenerate_id(true);$_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'role'=>$u['role'],'email'=>$u['email']];
   header('Location: index.php');exit;
  } else $error='Incorrect login details.';
 }
}
page_header('Login'); ?>
<section class="panel narrow"><h1>Welcome back</h1><p>Sign in to your business account.</p>
<?php if($error): ?><p class="error"><?=h($error)?></p><?php endif; ?>
<?php if(isset($_GET['otp'])): ?>
<p>Enter the six-digit code sent through your configured notification channel.</p>
<?php if(isset($_SESSION['debug_otp'])): ?><p class="notice">LOCAL TEST OTP: <strong><?=h($_SESSION['debug_otp'])?></strong>. Debug mode only.</p><?php unset($_SESSION['debug_otp']); endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><input type="hidden" name="verify_otp" value="1"><label>One-time code<input name="otp" inputmode="numeric" maxlength="6" required></label><button>Verify and continue</button></form>
<?php else: ?>
<form method="post"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>"><label>Email or phone number<input name="identity" required></label><label>Password<input type="password" name="password" required></label><button>Login</button></form>
<?php endif; ?></section><?php page_footer(); ?>
