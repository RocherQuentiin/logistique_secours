<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Batch;
use App\Models\Product;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'name' => $this->faker->bothify('LOT-####'),
            'quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
