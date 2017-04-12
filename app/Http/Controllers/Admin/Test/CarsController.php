<?php

namespace App\Http\Controllers\Admin\Test;

use App\Http\Controllers\Controller;
use App\Http\Filters\CarFilter;
use App\Http\Requests\CarRequest;
use App\Http\Sorts\CarSort;
use App\Models\Test\Book;
use App\Models\Test\Brand;
use App\Models\Test\Car;
use App\Models\Test\Mechanic;
use App\Models\Test\Owner;
use App\Options\CrudOptions;
use App\Traits\CanCrud;
use Illuminate\Http\Request;

class CarsController extends Controller
{
    use CanCrud;

    /**
     * @param Request $request
     * @param CarFilter $filter
     * @param CarSort $sort
     * @return \Illuminate\View\View
     */
    public function index(Request $request, CarFilter $filter, CarSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = Car::filtered($request, $filter)->sorted($request, $sort)->paginate(10);

            $this->vars = [
                'owners' => Owner::all(),
                'brands' => Brand::all(),
                'books' => Book::all(),
                'mechanics' => Mechanic::all(),
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->_create();
    }

    /**
     * @param TestRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TestRequest $request)
    {
        return $this->_store(function () use ($request) {
            $this->item = Car::create($request->all());
        }, $request);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $req = [
            'owner_id' => 4,
            'brand_id' => 5,
            'name' => 'BMW Seria 5',
            'slug' => 'bmw-seria-5',
            'metadata' => [
                'title' => 'Title goes here',
                'subtitle' => 'Subtitle goes here',
                'content' => 'Content goes here',
            ],
            'owner' => [
                'first_name' => 'Ion',
                'last_name' => 'Gheorghe',
            ],
            'brand' => [
                'name' => 'Audi',
            ],
            'book' => [
                'name' => 'BMW Identity Book',
            ],
            'mechanics' => [
                4, 5, 6
            ],
            'pieces' => [
                [
                    'id' => 4,
                    'name' => 'Wheel MODIFIED',
                ],
                [
                    'id' => 5,
                    'name' => 'Clutch MODIFIED',
                ],
                [
                    'id' => 6,
                    'name' => 'Chair MODIFIED',
                ]
            ]
        ];

        $model = Car::findOrFail($id);

        $model->updateWithRelations($req);







        return $this->_edit(function () use ($id) {
            $this->item = Car::findOrFail($id);

            $this->vars = [
                'owners' => Owner::all(),
                'brands' => Brand::all(),
                'books' => Book::all(),
                'mechanics' => Mechanic::all(),
            ];
        });
    }

    /**
     * @param CarRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CarRequest $request, $id)
    {
        return $this->_update(function () use ($request, $id) {

            $this->item = Car::findOrFail($id);
            $this->item->update($request->all());

            //dd($request->get('book'));

            $this->item->book()->update($request->get('book'));

        }, $request);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        return $this->_destroy(function () use ($id) {
            $this->item = Car::findOrFail($id)->delete();
        });
    }

    /**
     * @return CrudOptions
     */
    public static function getCrudOptions()
    {
        return CrudOptions::instance()
            ->setModel(app(Car::class))
            ->setListRoute('admin.cars.index')
            ->setListView('admin.test.cars.index')
            ->setAddRoute('admin.cars.create')
            ->setAddView('admin.test.cars.add')
            ->setEditRoute('admin.cars.edit')
            ->setEditView('admin.test.cars.edit');
    }
}