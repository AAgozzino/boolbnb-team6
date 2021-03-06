<?php

namespace App\Http\Controllers;

use App\House;
use App\Service;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $houses = House::inRandomOrder()->limit(6)->get();
        $user = Auth::user();
        
        return view("houses.index", compact('houses', 'user'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\House  $house
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $house = House::where('slug', $slug)->first();
        $user_id = Auth::user();
        $user = User::where('id', $house->user_id)->get();
        return view('houses.show', compact('house', 'user_id'));
    }

    public function search(Request $request)
    {
        $data = $request->all();
        $houses = House::all();
        $services = Service::all();

        $houses_filtered = $houses->filter(function ($house) use($data) {
            return $this->distance($data['lat'], $data['lon'], $house->latitude, $house->longitude) < 20;
        });
        
        return view('houses.search', compact('houses_filtered', 'data', 'services'));
    }

    private function distance($lat1, $lon1, $lat2, $lon2) 
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
          return 0;
        }
        else {
          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
      
          return ($miles * 1.609344);
        }
      }
   
}
