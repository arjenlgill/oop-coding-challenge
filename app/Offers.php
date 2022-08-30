<?php 

namespace App;

class Offers
{
    public $products;
    public $productsArray;
    public $productPrice;
    public $individualDiscount;
    public $totalDiscount;
    public $discountMessages;

    // since the Offers class will always be based on an array of products, the sole concern of the __construct() method is
    // to adopt the original array of Product objects (assigned to $products), as well as another array of just the product 
    // names (assigned to $productsArray) for easier handling.
    public function __construct($products)
    {
        $this->products = $products;

        foreach ($this->products as $product) :
            $this->productsArray[] = $product->name;
        endforeach;
    }

    // abstracted commands which just assign all of the processed discount data so that it can be passed back to methods within the
    // Checkout class.
    protected function assignValues($productName, $discount)
    {
        if (array_keys($this->productsArray, $productName) && $discount > 0.00) :
            $this->individualDiscount = ($this->productPrice / 100 * $discount);
            $this->totalDiscount += ($this->productPrice / 100 * $discount);
            $this->discountMessages[] = "$productName $discount% off: -£" . number_format($this->individualDiscount, 2);
        endif;
    }

    // the percentageOff() method loops through all of the eligible products and cumulatively applies a discount.
    public function percentageOff(string $productName, int $discount)
    {
        $this->productPrice = 0;

        foreach ($this->products as $product) :
            if ($product->name === $productName) :
                $this->productPrice += $product->price; // when applying the discount, this makes sure that we apply it to the total price of eligible products.
            endif;
        endforeach;

        $this->assignValues($productName, $discount);
    }

    // the buyTwoGetHalfOff() method applies the 2-for-1 offer by evaluating how many 2:1 sets are available 
    // from the purchase item quantity and the discount item quantity.
    public function buyTwoGetHalfOff(string $purchaseItem, string $discountItem, int $discount)
    {

        // need to create two separate arrays containing the purchase item and the discount item, respectively.
        // this is so that we can compose 2:1 sets and evaluate how many times we need to apply the 2-for-1 discount.

        //the $purchaseItemArray is wrapped in the array_chunk() method to gather 2 purchase items for every 1 discount item.
        $purchaseItemArray = array_chunk(array_filter(array_values($this->productsArray), function ($value) use ($purchaseItem) {
            return $value === $purchaseItem;
        }), 2);

        $discountItemArray = array_filter(array_values($this->productsArray), function ($value) use ($discountItem) {
            return $value === $discountItem;
        });

        // creates an array formed of sub-arrays that contain aforementioned 2:1 sets.
        $twoForOneSets = array_map(function ($p, $d) {
            if ($p && $d) :
                $set = [$p, [$d]];
                return $set;
            endif;
        }, $purchaseItemArray, $discountItemArray);

        // loops through products in the basket and retrieves the price of the discount item for calculation.
        foreach ($this->products as $product) :
            if ($product->name === $discountItem) :
                $this->productPrice = $product->price; // contrary to the similar loop in percentageOff(), only one price value is being assigned, since we want to apply the 2-for-1 discount individually for each set, rather than cumulatively.
            endif;
        endforeach;

        // loops through the generated 2:1 sets and only assigns the discount values if there is strictly a 2:1 ratio.
        foreach ($twoForOneSets as $set) {
            if ((!empty($set[0]) && count($set[0]) === 2) && (!empty($set[1]) && count($set[1]) === 1)) :
                $this->assignValues($discountItem, $discount);
            else :
                return;
            endif;
        }
    }

    // utilising private and public states makes it simple to switch special offers on and off, without cluttering the Checkout class.
    private function someOtherOfferWhichIsCurrentlyInactiveForIllustrativePurposes()
    {
        //...
    }
}