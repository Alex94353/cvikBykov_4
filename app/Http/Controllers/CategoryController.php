<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::orderBy('name')->get();

        return response()->json($categories);
    }


    public function store(Request $request)
    {
        $category = Category::create([
            'id' => $request->id,
            'name' => $request->name
        ]);

        return response()->json($category, 201);
    }


    public function show(int $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Kategória nebola nájdená'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($category);
    }


    public function update(Request $request, int $id)
    {

        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Kategória nebola nájdená'], Response::HTTP_NOT_FOUND);
        }

        $category->name = $request->name;
        $category->save();

        return response()->json(['message' => 'Kategória bola aktualizovaná', 'category' => $category]);
    }


    public function destroy(int $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Kategória nebola nájdená'], Response::HTTP_NOT_FOUND);
        }

        $category->delete();

        return response()->json(['message' => 'Kategória bola vymazaná']);
    }
      
    public function getByName(string $name)
    {
        $category = Category::getByName($name);

        if (!$category) {
            return response()->json(['message' => 'Kategória nebola nájdená'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Kategória bola nájdená', 'category' => $category]);
    }
}
