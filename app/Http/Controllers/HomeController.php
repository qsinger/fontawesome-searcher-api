<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function fa(Request $request){
        $categoriesContents = \Symfony\Component\Yaml\Yaml::parseFile(storage_path() . '/app/fontawesome/5.12.0/categories.yml');
        $iconsContents = \Symfony\Component\Yaml\Yaml::parseFile(storage_path() . '/app/fontawesome/5.12.0/icons.yml');

        $arrayCategories = [];

        $i=1;
        foreach ($categoriesContents as $key => $yamlContent){
            $y = 1;
            foreach ($yamlContent['icons'] as $icon){
                $arrayCategories[$i]['icons'][$y]['slug'] = $icon;
                $arrayCategories[$i]['icons'][$y]['en_label'] = $iconsContents[$icon]['label'];
                $z = 1;
                $arrayCategories[$i]['icons'][$y]['searchs'][$z] = [];
                foreach ($iconsContents[$icon]['search']['terms'] as $term){
                    echo '<pre>';

                    $arrayCategories[$i]['icons'][$y]['searchs'][$z] = $term;
                    $z++;
                }
                $y++;
            }

            $arrayCategories[$i]['en_label'] = $yamlContent['label'];
            $arrayCategories[$i]['slug'] = $key;
            $i++;
        }

    }
}
