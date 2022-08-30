<?php

class priceBasketTest extends \PHPUnit\Framework\TestCase {
    public function testSubtotal(){
        $checkout = new App\Checkout(['Soup', 'Soup', 'Bread']);
        $checkout->subTotal();
        $this->assertEquals(2.10, $checkout->subTotal);
    }

    public function testTotal(){
        $checkout = new App\Checkout(['Soup', 'Soup', 'Bread']);
        $checkout->subTotal();
        $this->assertEquals(2.10, $checkout->subTotal);
        $checkout->applyDiscounts();
        $checkout->total();
        $this->assertEquals(1.70, $checkout->total);
    }

    public function testDiscounts() {
        $checkout = new App\Checkout(['Soup', 'Soup', 'Bread', 'Apples']);
        $checkout->applyDiscounts();
        $this->assertNotEmpty($checkout->discountMessages);
        $this->assertEquals(['Apples 10% off: -£0.10', 'Bread 50% off: -£0.40'], $checkout->discountMessages);
    }

    public function testCumulativePercentageOff(){
        $checkout = new App\Checkout(['Apples', 'Apples', 'Apples']);
        $checkout->applyDiscounts();
        $checkout->total();

        $this->assertEquals(['Apples 10% off: -£0.30'], $checkout->discountMessages);
    }

    public function testNoAvailableDiscounts(){
        $checkout = new App\Checkout(['Soup', 'Bread']);
        $checkout->applyDiscounts();
        $this->assertEmpty($checkout->discountMessages);
    }

    public function testBuyTwoGetHalfOff(){
        $fourSoupsOneBread = new App\Checkout(['Soup', 'Soup', 'Soup', 'Soup', 'Bread']);
        $fourSoupsTwoBreads = new App\Checkout(['Soup', 'Soup', 'Soup', 'Soup', 'Bread', 'Bread']);
        $threeSoupsOneBread = new App\Checkout(['Soup', 'Soup', 'Soup', 'Bread']);
        
        foreach([$fourSoupsOneBread, $fourSoupsTwoBreads, $threeSoupsOneBread] as $variation):
            $variation->applyDiscounts();
        endforeach;

        $this->assertEquals(['Bread 50% off: -£0.40'], $fourSoupsOneBread->discountMessages);
        $this->assertEquals(['Bread 50% off: -£0.40', 'Bread 50% off: -£0.40'], $fourSoupsTwoBreads->discountMessages);
        $this->assertEquals(['Bread 50% off: -£0.40'], $threeSoupsOneBread->discountMessages);
        
    }

}