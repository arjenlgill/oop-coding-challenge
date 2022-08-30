<?php

namespace App;

class Product
{

    public $name;
    public $price;

    public function __construct(string $name)
    {
        $this->name = $name;

        //in a real program, the prices would be fetched from a relational database.
        switch (strtolower($name)) {
            case "soup":
                $this->price = 0.65;
                break;
            case "bread":
                $this->price = 0.80;
                break;
            case "milk":
                $this->price = 1.30;
                break;
            case "apples":
                $this->price = 1.00;
                break;
        }
    }
}