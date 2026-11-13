<?php
require_once 'functions.php'; require_roles(['admin','manager','staff']); verify_csrf();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $s=$pdo->prepare('INSERT INTO products(name,category,description,sku,thickness_mm,width_mm,height_mm,unit,cost_price,selling_price,quantity,reorder_level,image_url) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)');
 $s->execute([trim($_POST['name']),trim($_POST['category']),trim($_POST['description']),trim($_POST['sku']),$_POST['thickness']?:null,$_POST['width']?:null,$_POST['height']?:null,$_POST['unit'],$_POST['cost'],$_POST['price'],$_POST['quantity'],$_POST['reorder'],trim($_POST['image_url'])]);
 header('Location: products.php');exit;
}
$rows=$pdo->query('SELECT * FROM products ORDER BY name')->fetchAll();
page_header('Products and stock'); ?>
<div class="hero"><div><p class="eyebrow">INVENTORY</p><h1>Products & stock</h1><p>Register glass types, dimensions, prices and available quantities.</p></div></div>
<section class="panel"><h2>Add product</h2><form method="post" class="formgrid"><input type="hidden" name="csrf" value="<?=h(csrf_token())?>">
<label>Product name<input name="name" required></label><label>Category<input name="category" placeholder="Clear glass, mirror, etc." required></label>
<label>SKU / code<input name="sku"></label><label>Unit<select name="unit"><option>piece</option><option>sheet</option><option>square metre</option><option>metre</option></select></label>
<label>Thickness (mm)<input type="number" step="0.01" name="thickness"></label><label>Width (mm)<input type="number" step="0.01" name="width"></label><label>Height (mm)<input type="number" step="0.01" name="height"></label>
<label>Buying cost (KSh)<input type="number" step="0.01" name="cost" required></label><label>Selling price (KSh)<input type="number" step="0.01" name="price" required></label>
<label>Opening stock<input type="number" step="0.01" name="quantity" value="0" required></label><label>Reorder level<input type="number" step="0.01" name="reorder" value="0"></label>
<label class="wide">Product photo URL<input name="image_url" placeholder="Use a licensed, genuine product photograph URL"></label><label class="wide">Description<textarea name="description"></textarea></label><div><button>Add product</button></div></form></section>
<section class="panel"><h2>Current inventory</h2><div class="tablewrap"><table><tr><th>Product</th><th>Category</th><th>Dimensions</th><th>Price</th><th>In stock</th><th>Status</th></tr>
<?php foreach($rows as $r): ?><tr><td><?=h($r['name'])?><small><?=h($r['sku'])?></small></td><td><?=h($r['category'])?></td><td><?=h($r['thickness_mm'])?>mm · <?=h($r['width_mm'])?> × <?=h($r['height_mm'])?>mm</td><td>KSh <?=number_format($r['selling_price'],2)?></td><td><?=h($r['quantity'])?> <?=h($r['unit'])?></td><td><?=$r['quantity']<=$r['reorder_level']?'<span class="low">Low stock</span>':'Available'?></td></tr><?php endforeach; ?>
</table></div></section><?php page_footer(); ?>
