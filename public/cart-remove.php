<?php

require_once '../classes/Cart.php';
require_once '../classes/Flash.php';

$key = $_GET['key'] ?? null;

$cart = new Cart();

if ($key && $cart->remove($key)) {
    Flash::set('success', 'Item removed from cart.');
}else {
    Flash::set('error', 'Cart item not found.');
}

header("Location: cart.php");
exit;