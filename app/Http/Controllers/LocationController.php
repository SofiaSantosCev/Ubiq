<?php

namespace App\Http\Controllers;

use App\location;
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
        if (!parent::isLoggedIn())
        {   
            return parent::error(403,"You don't have permission");
        }

        $locations = Location::all();
        $locationName = [];
        $locationDescription = [];

        if(empty($categories))
        {
            return parent::error(400,"There are no locations created");
        } 

        foreach ($locations as $location) {
            array_push($locationName, $location->name);
            array_push($locationDescription, $location->description);
        } 
        return response($locations);
       
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
        if (parent::isLoggedIn())
        {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $x_coordinate = $_POST['x_coordinate'];
            $y_coordinate = $_POST['y_coordinate'];


            if (empty($name)) {
                return response("The name of the location is empty", 400);
            }

            $location = Location::where('name', $name)->first();

            if ($location != null) {
                if ($name != $location->name) {
                    return parent::error(400,"This location already exists");
                }
            }
                    
            $user_id = parent::getUserfromToken()->id;

            $location = new Location;

            $location->name = $name;
            $location->description = $description;
            $location->start_date = $start_date;
            $location->end_date = $end_date;
            $location->x_coordinate = $x_coordinate;
            $location->y_coordinate = $y_coordinate;
            $location->user_id = $user_id;

            $location->save();

            return parent::success("Location created","");

            } else {
                return $this->error(403, "You don't have permission");
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(location $location)
    {
        //
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
    public function update(Request $request, $id)
    {
        if(parent::checkLogin() == false){
            return $this->error(301, "There is a problem with your session");
        }
        parse_str(file_get_contents("php://input"), $putData);
        $name = $putData['name'];
        $description = $putData['description'];
        $start_date = $putData['start_date'];
        $end_date = $putData['put_data'];
        $x_coordinate = $putData['x_coordinate'];
        $y_coordinate = $putData['y_coordinate'];
        $user_id = $putData['user_id'];
        //the rest of the data

        //comprobación de que todos los campos estan correctamente rellenados
        if ($title == "" or $description == ""){
            return $this->error(400, "Tienes que rellenar todos los campos");
        }
        
        $location = Location::where('id',$id)->first();

        $location->title = $title;
        $location->description = $description;
        $location->start_date = $start_date;
        $location->end_date = $end_date;
        $location->x_coordinate = $x_coordinate;
        $location->y_coordinate = $y_coordinate;
        $location->user_id = $user_id;

        $location->update();
        return $this->success('Localizacion modificada', "");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\rol  $rol
     * @return \Illuminate\Http\Response
     */

    //Eliminar localizaciones
    public function destroy(location $location)
    {
        if(parent::checkLogin() == false) 
        {
            return $this->error(301,'Ha ocurrido un problema con su sesión.');
        }

        $location = Location::where('id',$id)->first();
        $location->delete();
        return $this->success('Localizacion eliminada.');
    }
}
