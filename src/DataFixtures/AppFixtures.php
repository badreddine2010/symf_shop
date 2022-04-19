<?php

namespace App\DataFixtures;

use App\Entity\Images;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        // $product = new Product();
        // $manager->persist($product);
        // create 5 categories! Bam!
        for ($cat = 0; $cat <3; $cat++) {
            $category = new Category();
            $category->setName($faker->word());
            $manager->persist($category);
        
        // create 20 products! Bam!
        for ($i = 1; $i <mt_rand(2,5); $i++) {
            $product = new Product();
            $product->setName($faker->word());
            $product->setImage('livre'.$i.'.jpg');
            $product->setCategory($category);
            $product->setQuantity($faker->numberBetween($min = 2, $max = 4));
            $product->setPrice($faker->randomFloat(2, 20, 30));
            // $product->setPrice(mt_rand(10, 100));
            $manager->persist($product);
            // create 20 products! Bam!
        for ($i = 1; $i <mt_rand(1,11); $i++) {
            $image = new Images();
            $image->setLink('livre'.$i.'.jpg');
            $image->setProduct($product);
            $manager->persist($image); 
        }
    }

        $manager->flush();
    }
}}
