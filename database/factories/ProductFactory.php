<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Product::class, function (Faker $faker) {
    $image = $faker->randomElement([
        "https://lccdn.phphub.org/uploads/images/201806/01/5320/7kG1HekGK6.jpg",
        "https://lccdn.phphub.org/uploads/images/201806/01/5320/1B3n0ATKrn.jpg",
        "https://lccdn.phphub.org/uploads/images/201806/01/5320/r3BNRe4zXG.jpg",
        "https://lccdn.phphub.org/uploads/images/201806/01/5320/C0bVuKB2nt.jpg",
        "https://lccdn.phphub.org/uploads/images/201806/01/5320/82Wf2sg8gM.jpg",
        "https://lccdn.phphub.org/uploads/images/201806/01/5320/nIvBAQO5Pj.jpg",
        "https://lccdn.phphub.org/uploads/images/201806/01/5320/XrtIwzrxj7.jpg",
        "https://lccdn.phphub.org/uploads/images/201806/01/5320/uYEHCJ1oRp.jpg",
        "https://lccdn.phphub.org/uploads/images/201806/01/5320/2JMRaFwRpo.jpg",
        "https://lccdn.phphub.org/uploads/images/201806/01/5320/pa7DrV43Mw.jpg",
    ]);

    return [
        'title'         => $faker->word,
        'title_en'      => $faker->word,
        'category'      => $faker->word,
        'barcode'       => $faker->numberBetween(10000000, 99999999),
        'barcode_family'=> $faker->numberBetween(10000000, 99999999),
        'description'   => $faker->sentence,
        'specialnote'   => $faker->sentence,
        'image'         => $image,
        'on_sale'       => true,
        'rating'        => $faker->numberBetween(0, 5),
        'stock'         => 0,
        'sold_count'    => 0,
        'review_count'  => 0,
        'price'         => 10,
        'price_m_au'    => 10,
        'price_vip_au'  => 10,
        'price_vvip_au' => 10,
        'price_rmb'     => 10,
        'price_vip_rmb' => 10,
        'price_20_rmb'  => 10,
        'price_vvip_rmb'=> 10,
        'cost'          => 10,
        'real_cost'     => 50,
        'gst'           => 5,
        'size'          => $faker->numberBetween(100, 1000,50),
        'weight'        => 20,

    ];
});
