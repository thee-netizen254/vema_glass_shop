<?php
require_once __DIR__ . '/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
 session_set_cookie_params(['httponly'=>true, 'samesite'=>'Lax', 'secure'=>!empty($_SERVER['HTTPS'])]);
 session_start();
}
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!user()) { header('Location: login.php'); exit; } }
function require_roles(array $roles): void {
 require_login();
 if (!in_array(user()['role'], $roles, true)) { http_response_code(403); exit('Access denied.'); }
}
function csrf_token(): string {
 if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
 return $_SESSION['csrf'];
}
function verify_csrf(): void {
 if ($_SERVER['REQUEST_METHOD']==='POST' && !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
  http_response_code(419); exit('Invalid form token. Refresh and try again.');
 }
}
function notify_owners(PDO $pdo, string $message): void {
 $q=$pdo->query("SELECT email,phone,notify_email,notify_sms,notify_whatsapp FROM users WHERE role IN ('admin','manager') AND active=1");
 foreach($q as $u) {
  if ($u['notify_email'] && $u['email']) queue_notification($pdo,'email',$u['email'],$message);
  if ($u['notify_sms'] && $u['phone']) queue_notification($pdo,'sms',$u['phone'],$message);
  if ($u['notify_whatsapp'] && $u['phone']) queue_notification($pdo,'whatsapp',$u['phone'],$message);
 }
}
function queue_notification(PDO $pdo,string $channel,string $recipient,string $message): void {
 $s=$pdo->prepare('INSERT INTO notification_queue(channel,recipient,message) VALUES(?,?,?)');
 $s->execute([$channel,$recipient,$message]);
 // Provider adapter goes here. Do not pretend queued messages have been delivered.
}
function send_notification(string $channel,string $recipient,string $message): bool {
 // TODO: connect a provider. Return true only after the provider confirms acceptance.
 // SMS: Africa's Talking or a locally connected GSM modem adapter.
 // Email: authenticated SMTP (e.g. PHPMailer); WhatsApp: official WhatsApp Business Platform.
 return false;
}
function page_header(string $title): void { ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=h($title)?> | <?=h(APP_NAME)?></title><link rel="stylesheet" href="assets/style.css"></head><body>
<header class="top"><a href="index.php" class="brand">GLASS<span>MANAGER</span></a>
<nav><?php if(user()): ?><a href="index.php">Dashboard</a><a href="products.php">Products</a><a href="sales.php">Sales</a><a href="purchases.php">Purchases</a><a href="orders.php">Orders</a><a href="logout.php">Logout</a><?php else: ?><a href="login.php">Staff login</a><a href="customer_order.php">Place order</a><?php endif; ?></nav></header><main class="wrap">
<?php }
function page_footer(): void { ?></main><footer>Glass Shop Management System · Local business software</footer></body></html><?php }
