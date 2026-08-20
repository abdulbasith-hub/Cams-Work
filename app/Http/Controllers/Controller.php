<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

   /* public function ChangeDateFormat($timestamp) {
        // Check if the timestamp contains time or not
        if (strpos($timestamp, ' ') === false) {
            // If no time is provided, assume it's in the 'd/m/Y' format
            $date = \DateTime::createFromFormat('d/m/Y', $timestamp);
            print_r($date->format('d-m-Y'));
            if ($date === false) {
                // If parsing fails, throw an exception or return an error
                throw new \Exception("Invalid date format: " . $timestamp);
            }
            return $date->format('d-m-Y');
        } else {
            // If time is provided, assume it's in 'd/m/Y H:i' format
            $date = \DateTime::createFromFormat('d/m/Y H:i', $timestamp);
            if ($date === false) {
                // If parsing fails, throw an exception or return an error
                throw new \Exception("Invalid date and time format: " . $timestamp);
            }
            return $date->format('d-m-Y h:i A');
        }
    }*/
    
    public function ChangeDateFormat($timestamp) {
        // Create a DateTime object from the PostgreSQL timestamp
        $date = new \DateTime($timestamp);  // Use \DateTime to reference the global DateTime class

        // Check if the timestamp contains time or not
        if (strpos($timestamp, ' ') === false) {
            // If no time is provided, return date only
            return $date->format('d-m-Y');
        } else {
            // Format for date and time
            return $date->format('d-m-Y h:i A');
        }
    }

    //$timestamp1 = '2025-02-07 19:12:00';  // Example timestamp
    //  $timestamp2 = '2025-02-07';  // Example date only
    //  $controller = new Controller();

    //  // Call the method from Controller.php
    // echo $controller-> ChangeDateFormat($timestamp1);
    // echo $controller-> ChangeDateFormat($timestamp2);

}
