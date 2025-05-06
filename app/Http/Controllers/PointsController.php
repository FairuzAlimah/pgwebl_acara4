<?php

namespace App\Http\Controllers;

use App\Models\PointsModel;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    public function __construct()
    {
        $this->points = new PointsModel();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'title' => 'Map',
        ];
        return view('map', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //Validate request
        $request->validate(
            [
                'name' => 'required|unique:points,name',
                'description' => 'required',
                'geom_point' => 'required',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:10240',
            ],
            [
                'name.required' => 'Name is Required',
                'name.unique' => 'Name Already Exists',
                'description.required' => 'Description is Required',
                'geom_point.required' => 'Geometry Point is Required',

            ]

        );

        //create images direktori if not exist
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777);
        }

        //get image file
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_point." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        } else {
            $name_image = null;
        }


        $data = [
            'geom' => $request->geom_point,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image,
        ];


        //Create Data
        if (!$this->points->create($data)) {
            return redirect()->route('map')->with('error', 'Point Failed to Add');
        }

        //kembali ke map
        return redirect()->route('map')->with('success', 'Point has been Added');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       $imagefile = $this->points->find($id)->image;

        if (!$this->points->destroy($id)) {
            return redirect()->route('map')->with('error', 'Point failed to delete');
        }

        //de;ete image
        if ($imagefile !=null) {
            if (file_exists('./storage/images/' .$imagefile)) {
                unlink('./storage/images/' .$imagefile);
            }
        }

        return redirect()->route('map')->with('success', 'Point has been deleted');
    }
}
