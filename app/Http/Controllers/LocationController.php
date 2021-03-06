<?php

namespace App\Http\Controllers;

use App\Location;
use Illuminate\Http\Request;
use App\Validator;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Mostrar localizaciones
    public function index()
    {
        if (parent::checkLogin())
        {   
            return response()->json([
            'locations' => Location::where('user_id', parent::getUserfromToken()->id)->get()
            ],200);
        }
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    //Crear y guardar localizaciones
    public function store(Request $request)
    {
        if (!parent::checkLogin())
        {
            return parent::response("You don't have permission",403);
        }
                
        $location = new Location();
        $location->name = $request->name;
        if(!isset($request->name) || !isset($request->description) || !isset($request->start_date) || !isset($request->end_date) || !isset($request->y_coordinate) || !isset($request->x_coordinate)){
            return parent::response("All fields have to be filled", 400);
        }

        $existingLocation = Location::where('name', $request->name)->first();
        if ($existingLocation != null) {
            return parent::response("This location already exists", 401);
        }

        $location->description = $request->description;
        $location->start_date = $request->start_date;
        $location->end_date = $request->end_date;
        $location->x_coordinate = $request->x_coordinate;
        $location->y_coordinate = $request->y_coordinate;
        $location->user_id =  parent::getUserfromToken()->id;

        $location->save();

        return response()->json([
            'message' => "Location created",
            
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(location $location)
    {
        return response()->json([
                'location' => $location,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function edit(location $location)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\rol  $rol
     * @return \Illuminate\Http\Response
     */

    //Modificar localizaciones
    public function update(Request $request, Location $location)
    {
        if(parent::checkLogin() == false){
            return parent::response("There is a problem with your session",301);
        }

        $location->update($request->all());
        return parent::response("Localization modified", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\location  $location
     * @return \Illuminate\Http\Response
     */

    //Eliminar localizaciones
    public function destroy(location $location)
    {
        if(!parent::checkLogin()) 
        {
            return parent::response('Ha ocurrido un problema con su sesión.',301);
        }

        $location->delete();
        return parent::response("Deleted",200);
    }
}
