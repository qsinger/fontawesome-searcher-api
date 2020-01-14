<?php

use App\Http\Controllers\Controller;
use App\Models\Icon;
use App\Models\SearchTerm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Yaml\Yaml;

/**
 * Class MainSeeder
 * @Version 1.1
 * @author qsinger
 * @compagny Valdev
 * @url https://valdev.ch
 * @date 14.01.2020
 *
 * Import Categories, Icons and Search terms from yaml files of Fontawesome
 *
 */

class MainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $categoriesContents = Yaml::parseFile(resource_path() . '/fontawesome/5.12.0/categories.yml');
        $iconsContents = Yaml::parseFile(resource_path() . '/fontawesome/5.12.0/icons.yml');

        $arrayCategories = [];

        $i=1;
        $y = 1;
        foreach ($categoriesContents as $key => $categoriesContent){

            foreach ($categoriesContent['icons'] as $icon){
                $arrayCategories[$i]['icons'][$y]['slug'] = $icon;
                $arrayCategories[$i]['icons'][$y]['slug_to_display'] = 'fas fa-' . $icon;
                $arrayCategories[$i]['icons'][$y]['en_label'] = $iconsContents[$icon]['label'];

                $arrayCategories[$i]['icons'][$y]['searchs'] = $iconsContents[$icon]['search']['terms'];
                $y++;

            }

            $arrayCategories[$i]['en_label'] = $categoriesContent['label'];
            $arrayCategories[$i]['slug'] = $key;
            $i++;
        }

        $newCategoryIconJoinId = 1;
        $newIconId = 1;
        $newCategoryId = 1;
        foreach ($arrayCategories as $category){
            $tempCategory = $category;
            unset($tempCategory["icons"]);
            DB::table('categories')->updateOrInsert(['id'=>$newCategoryId], $tempCategory);
            $this->command->info('Category ' . $tempCategory['en_label'] . ' inserted');

            foreach ($category['icons'] as $icon){

                $searchConcat = Controller::cleanString($icon['slug']);
                foreach ($icon['searchs'] as $search){
                    if($search){
                        $searchConcat .= Controller::cleanString($search);
                    }
                }

                $tempsIcon = $icon;
                $tempsIcon['search_terms'] = $searchConcat;
                unset($tempsIcon["searchs"]);

                $searchIcon = Icon::where('slug', $tempsIcon['slug'])->first();

                if($searchIcon == null){

                    DB::table('icons')->updateOrInsert(['id'=>$newIconId], $tempsIcon);
                    $this->command->info('Icon ' . $tempsIcon['en_label'] . ' inserted');
                    $searchIcon = Icon::find($newIconId);
                    $newIconId++;
                }

                $tempCategoryIconJoin = [];
                $tempCategoryIconJoin['icon_id'] = $searchIcon->id;
                $tempCategoryIconJoin['category_id'] = $newCategoryId;
                DB::table('category_icon')->updateOrInsert(['id'=>$newCategoryIconJoinId], $tempCategoryIconJoin);
                $newCategoryIconJoinId++;

            }

            $newCategoryId++;
        }

        /* second part */

        $arrayIcons = [];

        $i=1;

        foreach ($iconsContents as $key => $iconsContent){

            $arrayIcons[$i]['en_label'] = $iconsContent['label'];
            $arrayIcons[$i]['slug'] = $key;
            $arrayIcons[$i]['slug_to_display'] = 'fas fa-' . $key;
            $arrayIcons[$i]['searchs'] = $iconsContent['search']['terms'];

            $i++;
        }

        $newIconId = 1;
        foreach ($arrayIcons as $icon){

            $searchConcat = Controller::cleanString($icon['slug']);
            foreach ($icon['searchs'] as $search){
                if($search){
                    $searchConcat .= Controller::cleanString( $search);
                }
            }

            $tempsIcon = $icon;
            $tempsIcon['search_terms'] = $searchConcat;
            unset($tempsIcon["searchs"]);

            $searchIcon = Icon::where('slug', $tempsIcon['slug'])->first();

            if($searchIcon == null){

                DB::table('icons')->updateOrInsert(['id'=>$newIconId], $tempsIcon);
                $this->command->info('Icon ' . $tempsIcon['en_label'] . ' inserted');
                $newIconId++;
            }

        }
    }
}
