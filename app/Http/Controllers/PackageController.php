<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Package;

class PackageController extends Controller
{
    /** 
     * [GET] -> returns all Records from Package 
     *
     * @return App\Package
     */
    public function getPackages(){
        return Package::orderBy("created_at","desc")->get();
    }

    /** 
     * [POST] -> Sort Via Column @$column 
     *
     * @return App\Package
     */
    public function SortColumn($column){
        try{
            return Package::orderBy($column,"asc")->get();
        }
        catch(\Illuminate\Database\QueryException $ex){
            return response("Column Not Found.",500);
        }
    }

    /** 
     * [POST] -> Add New Package @$request
     *
     * @return App\Package
     */
    public function addNew(Request $request){

        $new = Package::create(
            ['tracking' => $request->tracking,
            'name' => $request->name,
            'weight' => $request->weight,
            'price' => $request->price
            ]
        );

        if(!$new) return responce("Error While Adding.",500);
        $new->save();
        return Package::latest()->first();
    }

    /** 
     * [POST] -> Update Package info TAKEOUT @$tracking,$value
     *
     * @return responce()
     */
    public function updateTakeout($tracking,$value){

        $package = Package::where("tracking",$tracking)->first();
        if(!$package) return response("Not Found.",500);
        
        
        $package->update(
                ['takeout' => $value,'totalprice' => ($package->price + $package->takeout)]
        );
        return response($package->totalprice,200);
    }

    /** 
     * [POST] -> Update Package info STATE @$id
     *
     * @return responce()
     */
    public function updateState($id){
        $package = Package::find($id);
        if(!$package) return response("Not Found.",500);

        if($package->finished == 0){
            $package->update(['finished' => 1]);
            return response(1,200);
        }
        else 
        {
            $package->update(['finished' => 0]);
            return response(0,200);
        }
    }

    /** 
     * [POST] -> Remove Package With @$id
     *
     * @return responce()
     */
    public function removeItem($id){
        $package = Package::find($id);
        if(!$package) return response("Error",500);
        $package->delete();
        return response("Success",200);
    }
}
