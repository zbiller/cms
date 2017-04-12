<?php

use App\Models\Test\Brand;
use App\Models\Test\Car;
use App\Models\Test\Mechanic;
use App\Models\Test\Owner;
use App\Models\Test\Book;
use App\Models\Test\Piece;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cars_mechanics_ring')->delete();
        DB::table('cars_mechanics')->delete();
        DB::table('cars_pieces')->delete();
        DB::table('cars_books')->delete();
        DB::table('cars')->delete();
        DB::table('cars_brands')->delete();
        DB::table('cars_owners')->delete();

        $owner1 = Owner::create([
            'first_name' => 'Andrei',
            'last_name' => 'Badea',
        ]);

        $owner2 = Owner::create([
            'first_name' => 'Ion',
            'last_name' => 'Gheorghe',
        ]);

        $brand1 = Brand::create([
            'name' => 'BMW',
        ]);

        $brand2 = Brand::create([
            'name' => 'Audi',
        ]);

        $brand3 = Brand::create([
            'name' => 'Mercedes',
        ]);

        $car1 = Car::create([
            'owner_id' => $owner1->id,
            'brand_id' => $brand1->id,
            'name' => 'BMW Seria 7',
            'slug' => 'bmw-seria-7',
        ]);

        $car2 = Car::create([
            'owner_id' => $owner2->id,
            'brand_id' => $brand2->id,
            'name' => 'Audi A5',
            'slug' => 'audi-a5',
        ]);

        $car3 = Car::create([
            'owner_id' => $owner1->id,
            'brand_id' => $brand3->id,
            'name' => 'Mercedes C Class',
            'slug' => 'mercedes-c-class',
        ]);

        $book1 = Book::create([
            'car_id' => $car1->id,
            'name' => 'BMW Identity Book',
        ]);

        $book2 = Book::create([
            'car_id' => $car2->id,
            'name' => 'Audi Identity Book',
        ]);

        $book3 = Book::create([
            'car_id' => $car3->id,
            'name' => 'Mercedes Identity Book',
        ]);

        $piece1 = Piece::create([
            'car_id' => $car1->id,
            'name' => 'Wheel',
        ]);

        $piece2 = Piece::create([
            'car_id' => $car1->id,
            'name' => 'Clutch',
        ]);

        $piece3 = Piece::create([
            'car_id' => $car1->id,
            'name' => 'Chair',
        ]);

        $mechanic1 = Mechanic::create([
            'name' => 'Mechanic 1',
        ]);

        $mechanic2 = Mechanic::create([
            'name' => 'Mechanic 2',
        ]);

        $mechanic3 = Mechanic::create([
            'name' => 'Mechanic 3',
        ]);

        $car1->mechanics()->attach([
            $mechanic1->id,
            $mechanic2->id,
            $mechanic3->id,
        ]);
    }
}
