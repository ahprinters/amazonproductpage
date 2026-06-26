<?php

class Cart
{
    private $sessionKey = 'cart';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
        }
    }

    public function add($productId, $sku, $price, $quantity = 1, $maxStock = null)
    {
        $productId = (int)$productId;
        $sku = trim($sku);
        $price = (float)$price;
        $quantity = max(1, (int)$quantity);

        if ($productId <= 0 || $sku === '' || $price <= 0) {
            throw new Exception("Invalid cart data.");
        }

        $itemKey = $this->makeKey($productId, $sku);

        $currentQuantity = isset($_SESSION[$this->sessionKey][$itemKey])
            ? (int)$_SESSION[$this->sessionKey][$itemKey]['quantity']
            : 0;

        $newQuantity = $currentQuantity + $quantity;

        if ($maxStock !== null && $newQuantity > (int)$maxStock) {
            throw new Exception("You cannot add more than available stock.");
        }

        if (isset($_SESSION[$this->sessionKey][$itemKey])) {
            $_SESSION[$this->sessionKey][$itemKey]['quantity'] = $newQuantity;
        } else {
            $_SESSION[$this->sessionKey][$itemKey] = [
                'product_id' => $productId,
                'sku' => $sku,
                'price' => $price,
                'quantity' => $quantity
            ];
        }

        return true;
    }

    public function remove($key)
    {
        if (isset($_SESSION[$this->sessionKey][$key])) {
            unset($_SESSION[$this->sessionKey][$key]);
            return true;
        }

        return false;
    }

    public function updateQuantity($key, $quantity, $maxStock = null)
    {
        $quantity = (int)$quantity;

        if ($quantity <= 0) {
            return $this->remove($key);
        }

        if (!isset($_SESSION[$this->sessionKey][$key])) {
            return false;
        }

        if ($maxStock !== null && $quantity > (int)$maxStock) {
            throw new Exception("You cannot select more than available stock.");
        }

        $_SESSION[$this->sessionKey][$key]['quantity'] = $quantity;

        return true;
    }

    public function all()
    {
        return $_SESSION[$this->sessionKey];
    }

    public function count()
    {
        $count = 0;

        foreach ($_SESSION[$this->sessionKey] as $item) {
            $count += (int)$item['quantity'];
        }

        return $count;
    }

    public function clear()
    {
        $_SESSION[$this->sessionKey] = [];
    }

    private function makeKey($productId, $sku)
    {
        return $productId . '_' . $sku;
    }
}