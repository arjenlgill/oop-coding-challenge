<?php

namespace App;

require_once "Checkout.php";

$basket;

foreach (array_slice($argv, 1) as $arg) :
    $basket[] = ucfirst($arg);
endforeach;

$checkout = new Checkout($basket);
echo "Basket: " . implode(', ', $basket) . "\n";
$checkout->subTotal();
echo "Subtotal: £" . number_format($checkout->subTotal, 2) . "\n";
$checkout->applyDiscounts();
$checkout->displayDiscountMessages();
$checkout->total();
echo 'Total: £' . number_format($checkout->total, 2);