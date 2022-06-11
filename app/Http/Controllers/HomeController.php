<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class HomeController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $latitude = 53.3340285;
        $logintude = -6.2535495;
        $file = public_path('affiliates.txt');
        $affiliates = [];
        $total = 0;
        if (file_exists($file)) {
            $content = fopen($file,'r');

            while(!feof($content)){
                $total++;
                $line = fgets($content);
                if(!empty($line)){
                    $row_data = json_decode($line);
                    if(!empty($row_data)){
                        $distance = $this->calculateDistanceFromTwoCoordinates($latitude,$logintude,$row_data->latitude,$row_data->longitude,'km');

                        if($distance <= 100){
                            $affiliates[] = [
                                'id' => (int) $row_data->affiliate_id,
                                'name' => $row_data->name
                            ];
                        }

                    }
                    
                }
            }

            fclose($content);
        }
        sort($affiliates);
        return view('home',['affiliates' => $affiliates]);
    }

    protected function calculateDistanceFromTwoCoordinates($lat1, $lon1, $lat2, $lon2, $unit)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }else{
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }
}
