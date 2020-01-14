<?php

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
        $z = 1;
        $y = 1;
        foreach ($categoriesContents as $key => $categoriesContent){

            foreach ($categoriesContent['icons'] as $icon){
                $arrayCategories[$i]['icons'][$y]['slug'] = $icon;
                $arrayCategories[$i]['icons'][$y]['en_label'] = $iconsContents[$icon]['label'];

                $arrayCategories[$i]['icons'][$y]['searchs'][$z] = [];
                foreach ($iconsContents[$icon]['search']['terms'] as $term){

                    $arrayCategories[$i]['icons'][$y]['searchs'][$z] = $term;
                    $z++;
                }

                $y++;
            }

            $arrayCategories[$i]['en_label'] = $categoriesContent['label'];
            $arrayCategories[$i]['slug'] = $key;
            $i++;
        }

        $newIconSearchJoinId = 1;
        $newSearchTermId = 1;
        $newCategoryIconJoinId = 1;
        $newIconId = 1;
        $newCategoryId = 1;
        foreach ($arrayCategories as $category){
            $tempCategory = $category;
            unset($tempCategory["icons"]);
            DB::table('categories')->updateOrInsert(['id'=>$newCategoryId], $tempCategory);
            $this->command->info('Category ' . $tempCategory['en_label'] . ' inserted');

            foreach ($category['icons'] as $icon){
                $tempsIcon = $icon;
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
                DB::table('categories_icons_join')->updateOrInsert(['id'=>$newCategoryIconJoinId], $tempCategoryIconJoin);
                $newCategoryIconJoinId++;

                foreach ($icon['searchs'] as $search){
                    if($search){
                        $tempsSearch = [];
                        $tempsSearch['en_slug'] = $search;

                        $searchTerm = SearchTerm::where('en_slug', $search)->first();

                        if($searchTerm == null){
                            DB::table('search_terms')->updateOrInsert(['id'=>$newSearchTermId], $tempsSearch);
                            $searchTerm = SearchTerm::find($newSearchTermId);
                            $newSearchTermId++;
                        }
                        $tempIconSeachJoin = [];
                        $tempIconSeachJoin['icon_id'] = $searchIcon->id;
                        $tempIconSeachJoin['search_term_id'] = $searchTerm->id;
                        DB::table('icons_search_terms_join')->updateOrInsert(['id'=>$newIconSearchJoinId], $tempIconSeachJoin);
                        $newIconSearchJoinId++;
                    }
                }
            }

            $newCategoryId++;
        }

        /* second part */

        $arrayIcons = [];

        $i=1;

        foreach ($iconsContents as $key => $iconsContent){

            $arrayIcons[$i]['en_label'] = $iconsContent['label'];
            $arrayIcons[$i]['slug'] = $key;

            $arrayIcons[$i]['searchs'][] = [];
            foreach ($iconsContent['search']['terms'] as $term){

                $arrayIcons[$i]['searchs'][] = $term;
            }

            $i++;
        }

        $newIconSearchJoinId = 1;
        $newSearchTermId = 1;
        $newIconId = 1;
        foreach ($arrayIcons as $icon){

            $tempsIcon = $icon;
            unset($tempsIcon["searchs"]);

            $searchIcon = Icon::where('slug', $tempsIcon['slug'])->first();

            if($searchIcon == null){

                DB::table('icons')->updateOrInsert(['id'=>$newIconId], $tempsIcon);
                $this->command->info('Icon ' . $tempsIcon['en_label'] . ' inserted');
                $searchIcon = Icon::find($newIconId);
                $newIconId++;
            }

            foreach ($icon['searchs'] as $search){
                if($search){
                    $tempsSearch = [];
                    $tempsSearch['en_slug'] = $search;

                    $searchTerm = SearchTerm::where('en_slug', $search)->first();

                    if($searchTerm == null){
                        DB::table('search_terms')->updateOrInsert(['id'=>$newSearchTermId], $tempsSearch);
                        $searchTerm = SearchTerm::find($newSearchTermId);
                        $newSearchTermId++;
                    }else{
                        $this->command->warn('Icon ' . $searchTerm->en_label . ' already exists');
                    }
                    $tempIconSeachJoin = [];
                    $tempIconSeachJoin['icon_id'] = $searchIcon->id;
                    $tempIconSeachJoin['search_term_id'] = $searchTerm->id;
                    DB::table('icons_search_terms_join')->updateOrInsert(['id'=>$newIconSearchJoinId], $tempIconSeachJoin);
                    $newIconSearchJoinId++;
                }
            }

        }
    }
}
