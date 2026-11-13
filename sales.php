<?php
require_once 'functions.php'; require_roles(['admin','manager','staff']); verify_csrf();
$products=$pdo->query('SELECT id,name,quantity,selling_price,unit FROM products WHERE quantity>0 ORDER BY name')->fetchAll();$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $pid=(int)$_POST['product_id'];$qty=(float)$_POST['quantity'];
 try{
  $pdo->beginTransaction();$s=$pdo->prepare('SELECT * FROM products WHERE id=? FOR UPDATE');$s->execute([$pid]);$p=$s->fetch();
  if(!$p || $qty<=0 || $p['quantity']<$qty) throw new Exception('Insufficient stock or invalid quantity.');
  $total=$qty*(float)$p['selling_price'];$receipt='GS-'.date('Ymd-His').'-'.random_int(10,99);
  $pdo->prepare('INSERT INTO sales(receipt_no,customer_name,customer_phone,total,payment_method,created_by) VALUES(?,?,?,?,?,?)')->execute([$receipt,trim($_POST['customer']),trim($_POST['phone']),$total,$_POST['payment'],user()['id']]);
  $sid=(int)$pdo->lastInsertId();$pdo->prepare('INSERT INTO sale_items(sale_id,product_id,quantity,unit_price,line_total) VALUES(?,?,?,?,?)')->execute([$sid,$pid,$qty,$p['selling_price'],$total]);
  $pdo->prepare('UPDATE products SET quantity=quantity-? WHERE id=?')->execute([$qty,$pid]);
  $pdo->prepare("INSERT INTO stock_movements(product_id,movement_type,quantity_change,reference_id,note,user_id) VALUES(?,'sale',?,?,?,?)")->execute([$pid,-$qty,$sid,'Sale '.$receipt,user()['id']]);
  $pdo->commit();notify_owners($pdo,"Sale recorded: $receipt, KSh ".number_format($total,2));header('Location: sales.php?done='.$receipt);exit;
 }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();$error=$e->getMessage();}
}
$recent=$pdo->query('SELECT s.*,u.name staff FROM sales s JOIN users u ON u.id=s.created_by ORDER BY s.id DESC LIMIT 30')->fetchAll();
page_header('Sales'); ?><div class="hero"><div><p class="eyebrow">POINT OF SALE</p><h1>Record a sale</h1></div></div>
<?php if($error): ?><p class="error"><?=h($error)?></p><?php endif; ?><?php if(isset($_GET['done'])): ?><p class="success">Sale saved. Receipt <?=h($_GET['done'])?></p><?php endif; ?>
<section class="panel"><form method="post" class="formgrid"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>">
<label class="wide">Product<select name="product_id" required><?php foreach($products as $p): ?><option value="<?=$p['id']?>"><?=h($p['name'])?> — <?=h($p['quantity'])?> available · KSh <?=number_format($p['selling_price'],2)?></option><?php endforeach; ?></select></label>
<label>Quantity<input name="quantity" type="number" step="0.01" min="0.01" required></label><label>Customer name<input name="customer"></label><label>Customer phone<input name="phone"></label>
<label>Payment method<select name="payment"><option>Cash</option><option>M-Pesa</option><option>Bank transfer</option><option>Card</option><option>Credit</option></select></label><div><button>Complete sale</button></div></form></section>
<section class="panel"><h2>Recent sales</h2><div class="tablewrap"><table><tr><th>Receipt</th><th>Date</th><th>Customer</th><th>Total</th><th>Payment</th><th>Recorded by</th></tr><?php foreach($recent as $r): ?><tr><td><?=h($r['receipt_no'])?></td><td><?=h($r['created_at'])?></td><td><?=h($r['customer_name'])?></td><td>KSh <?=number_format($r['total'],2)?></td><td><?=h($r['payment_method'])?></td><td><?=h($r['staff'])?></td></tr><?php endforeach; ?></table></div></section><?php page_footer(); ?>
