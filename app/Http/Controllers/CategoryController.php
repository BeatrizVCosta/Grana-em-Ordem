<?php

namespace App\Http\Controllers;

use App\Models\Category; // Importe o Model Category
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importe o Facade Auth
use Illuminate\Validation\Rule; // Importe Rule para validação unique

class CategoryController extends Controller
{
    
    public function index()
    {
        $categories = Auth::user()->categories()->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

   public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
        ], [ 
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.string' => 'O nome da categoria deve ser um texto.',
            'name.max' => 'O nome da categoria não pode ter mais de :max caracteres.',
            'name.unique' => 'Já existe uma categoria com este nome. Escolha outro.',
        ]); 

        Auth::user()->categories()->create($validated);

        return redirect()->route('categories.index')->with('success', 'Categoria adicionada com sucesso!');
    }

      public function edit(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        return view('categories.edit', compact('category'));
    }
    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->where(fn ($query) => $query->where('user_id', Auth::id()))->ignore($category->id),
            ],
        ], [ 
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.string' => 'O nome da categoria deve ser um texto.',
            'name.max' => 'O nome da categoria não pode ter mais de :max caracteres.',
            'name.unique' => 'Já existe uma categoria com este nome. Escolha outro.',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Categoria atualizada com sucesso!');
    }

     public function destroy(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categoria excluída com sucesso!');
    }
}