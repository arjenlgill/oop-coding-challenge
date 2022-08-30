<?php

namespace App;

require_once "Product.php";
require_once "Offers.php";

class Checkout
{

    public $products;
    public $availableOffers;
    public $discount;
    public $discountMessages;
    public $subTotal;
    public $total;

    // generates an array composed of new Product instances for each item in the basket.
    public function __construct(array $products)
    {
        foreach ($products as $product) :
            $this->products[] = new Product($product);
        endforeach;
    }

    public function increment(float $price)
    {
        $this->subTotal += $price;
    }

    public function subTotal()
    {
        foreach ($this->products as $product) :
            $this->increment($product->price);
        endforeach;
    }

    public function checkForOffers()
    {
        $this->availableOffers = get_class_methods("\App\Offers");
    }

    public function assignDiscountVariables($discountedAmount, $discountMessages)
    {
        $this->discount += $discountedAmount;
        $this->discountMessages = $discountMessages;
    }

    public function applyDiscounts()
    {
        $this->checkForOffers();
        $discounts = new Offers($this->products);

        // looks at which offers are set to 'public' within Offers class and parses through them with a switch statement.
        // if there are any other public methods, such as __construct(), then the switch statement will help ignore them
        // and only utilise the relevant ones that pertain to special offers.
        foreach ($this->availableOffers as $offer) :
            switch ($offer) {
                case 'percentageOff':
                    $discounts->$offer('Apples', 10);
                    break;
                case 'buyTwoGetHalfOff':
                    $discounts->$offer('Soup', 'Bread', 50);
                    break;
            }
        endforeach;

        $this->assignDiscountVariables($discounts->totalDiscount, $discounts->discountMessages);
    }

    public function displayDiscountMessages()
    {
        if (empty($this->discountMessages)) :
            print "(no offers available)" . "\n";
            return;
        endif;

        foreach ($this->discountMessages as $discountMessage) :
            print $discountMessage . "\n";
        endforeach;
    }

    public function total()
    {
        $this->total = ($this->subTotal -= $this->discount);
    }
}