<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
class CategoryController extends Controller
{
    
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        return response()->json($categories);
    }


    public function store(Request $request)
    {

        try {
            $validated = $request->validate([
                'name' => 'required|string|min:3|max:255|unique:categories,name',
            ]);

            $category = Category::create([
                'name' => $validated['name'],
            ]);

            return response()->json(['message' => 'Kategória bola vytvorená', 'category' => $category]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Chyba pri validácii kategórie',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neočakávaná chyba pri vytváraní kategórie',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


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
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json(['message' => 'Kategória nebola nájdená'], Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'name' => 'required|string|min:3|max:255|unique:categories,name,' . $id,
            ]);

            $category->name = $validated['name'];
            $category->save();

            return response()->json(['message' => 'Kategória bola aktualizovaná', 'category' => $category]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Chyba pri validácii kategórie',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neočakávaná chyba pri aktualizácii kategórie',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


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
