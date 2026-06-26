<?php

require_once '../classes/Cart.php';
require_once '../classes/Flash.php';

$cart = new Cart();
$cart->clear();

Flash::set('success', 'Cart cleared successfully.');

header("Location: cart.php");
exit;