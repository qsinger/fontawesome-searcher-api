<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function fa(Request $request){

        $yamlContents = Yaml::parseFile(public_path() . '/metadata/icons.yml');

        //$yamlContents = Yaml::dump($yamlContents,2 ,8);

        echo "<pre>";

        foreach ($yamlContents as $key => $yamlContent){
            echo '<br>';
            echo $key;
        }

        //var_dump($yamlContents);

    }
}
