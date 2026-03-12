<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!function_exists('thaifaCartCount')) {
    function thaifaCartCount()
    {
        $cart = $_SESSION['shop_cart'] ?? [];
        if (!is_array($cart)) {
            return 0;
        }
        $total = 0;
        foreach ($cart as $qty) {
            $q = (int)$qty;
            if ($q > 0) {
                $total += $q;
            }
        }
        return $total;
    }
}

