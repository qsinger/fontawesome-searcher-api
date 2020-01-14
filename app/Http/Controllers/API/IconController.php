<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\IconResource;
use App\Models\Category;
use App\Models\Icon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IconController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if($request->has('search')) {

            $finalSearch = '%' . Controller::cleanString($request->input('search')) . '%';

            $data = IconResource::collection(DB::table('icons')->where('search_terms', 'like', $finalSearch)->get());

        }else{
            $data = IconResource::collection(Icon::all());
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $model = Icon::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new IconResource($model)
        ], 200);
    }

    /**
     * Display the specified resource by category id.
     *
     * @param  int  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByCategory($category)
    {
        /** @var Category $model */
        $model = Category::findOrFail($category);

        return response()->json([
            'success' => true,
            'data' => IconResource::collection($model->icons)
        ], 200);
    }

}
