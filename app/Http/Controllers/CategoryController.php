<?php

namespace App\Http\Controllers;

use App\Models\Category; // Importe o Model Category
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importe o Facade Auth
use Illuminate\Validation\Rule; // Importe Rule para validação unique

class CategoryController extends Controller
{
    /**
     * Exibe a lista de categorias do usuário logado.
     */
    public function index()
    {
        // Pega as categorias do usuário logado, ordenadas pelo nome
        $categories = Auth::user()->categories()->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Exibe o formulário para criar uma nova categoria.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Armazena uma nova categoria no banco de dados.
     */
    public function store(Request $request)
    {
        // Validação dos dados do formulário
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Garante que o nome da categoria seja único para o usuário logado
                Rule::unique('categories')->where(fn ($query) => $query->where('user_id', Auth::id())),
            ],
        ]);

        // Cria a categoria associada ao usuário logado
        Auth::user()->categories()->create($validated);

        return redirect()->route('categories.index')->with('success', 'Categoria adicionada com sucesso!');
    }

    /**
     * Exibe o formulário para editar uma categoria existente.
     */
    public function edit(Category $category)
    {
        // Garante que o usuário só pode editar as próprias categorias
        if ($category->user_id !== Auth::id()) {
            abort(403); // Acesso negado
        }

        return view('categories.edit', compact('category'));
    }

    /**
     * Atualiza uma categoria existente no banco de dados.
     */
    public function update(Request $request, Category $category)
    {
        // Garante que o usuário só pode atualizar as próprias categorias
        if ($category->user_id !== Auth::id()) {
            abort(403); // Acesso negado
        }

        // Validação dos dados do formulário
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Garante que o nome da categoria seja único para o usuário logado, exceto para a própria categoria
                Rule::unique('categories')->where(fn ($query) => $query->where('user_id', Auth::id()))->ignore($category->id),
            ],
        ]);

        $category->update($validated); // Atualiza a categoria

        return redirect()->route('categories.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        
        if ($category->user_id !== Auth::id()) {
            abort(403); // Acesso negado
        }

        $category->delete(); // Exclui a categoria

        return redirect()->route('categories.index')->with('success', 'Categoria excluída com sucesso!');
    }
}