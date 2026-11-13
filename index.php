<?php
require_once 'functions.php'; require_login();
$products=(int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$low=(int)$pdo->query('SELECT COUNT(*) FROM products WHERE quantity<=reorder_level')->fetchColumn();
$sales=(float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM sales WHERE DATE(created_at)=CURDATE()")->fetchColumn();
$pending=(int)$pdo->query("SELECT COUNT(*) FROM customer_orders WHERE status='pending'")->fetchColumn();
page_header('Dashboard'); ?>
<div class="hero"><div><p class="eyebrow">BUSINESS OVERVIEW</p><h1>Good day, <?=h(user()['name'])?></h1><p>Here is what's happening in your glass shop.</p></div><span class="role"><?=h(strtoupper(user()['role']))?></span></div>
<div class="stats"><article><small>Products</small><strong><?=$products?></strong></article><article><small>Today's sales</small><strong>KSh <?=number_format($sales,2)?></strong></article><article><small>Low-stock items</small><strong><?=$low?></strong></article><article><small>Pending orders</small><strong><?=$pending?></strong></article></div>
<section class="panel"><h2>Quick actions</h2><div class="actions"><a class="action" href="products.php">Manage products & stock</a><a class="action" href="sales.php">Record a sale</a><a class="action" href="purchases.php">Record a purchase</a><a class="action" href="orders.php">View customer orders</a></div></section>
<?php if(in_array(user()['role'],['admin','manager'],true)): ?><section class="panel"><h2>Management</h2><p>Use the product, sales, purchases and orders sections to manage daily operations. Add staff accounts through a protected user-management module before opening this to your team.</p></section><?php endif; ?>
<?php page_footer(); ?>
