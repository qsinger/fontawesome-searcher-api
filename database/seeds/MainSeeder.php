<?php

use Illuminate\Database\Seeder;

class MainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $categoriesContents = \Symfony\Component\Yaml\Yaml::parseFile(storage_path() . '/app/fontawesome/5.12.0/categories.yml');
        $iconsContents = \Symfony\Component\Yaml\Yaml::parseFile(storage_path() . '/app/fontawesome/5.12.0/icons.yml');

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

                $searchIcon = \App\Models\Icon::where('slug', $tempsIcon['slug'])->first();

                if($searchIcon == null){

                    DB::table('icons')->updateOrInsert(['id'=>$newIconId], $tempsIcon);
                    $this->command->info('Icon ' . $tempsIcon['en_label'] . ' inserted');
                    $searchIcon = \App\Models\Icon::find($newIconId);
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

                        $searchTerm = \App\Models\SearchTerm::where('en_slug', $search)->first();

                        if($searchTerm == null){
                            DB::table('search_terms')->updateOrInsert(['id'=>$newSearchTermId], $tempsSearch);
                            $searchTerm = \App\Models\SearchTerm::find($newSearchTermId);
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

            $searchIcon = \App\Models\Icon::where('slug', $tempsIcon['slug'])->first();

            if($searchIcon == null){

                DB::table('icons')->updateOrInsert(['id'=>$newIconId], $tempsIcon);
                $this->command->info('Icon ' . $tempsIcon['en_label'] . ' inserted');
                $searchIcon = \App\Models\Icon::find($newIconId);
                $newIconId++;
            }

            foreach ($icon['searchs'] as $search){
                if($search){
                    $tempsSearch = [];
                    $tempsSearch['en_slug'] = $search;

                    $searchTerm = \App\Models\SearchTerm::where('en_slug', $search)->first();

                    if($searchTerm == null){
                        DB::table('search_terms')->updateOrInsert(['id'=>$newSearchTermId], $tempsSearch);
                        $searchTerm = \App\Models\SearchTerm::find($newSearchTermId);
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
