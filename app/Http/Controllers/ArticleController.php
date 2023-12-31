<?php

namespace App\Http\Controllers;

use Stringable;
use App\Models\Article;
use Illuminate\Support\Str;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Category;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show', 'search']);
    }

    /**
     * Display articles.
     */
    public function index()
    {

        $articles = Article::when((bool) request('category') ?? false, fn($article) =>
                                $article->whereBelongsTo(Category::whereSlug(request('category'))->first())
                                )
                                ->when(request()->has('s'), function($article) {
                                    return $article->orWhere('title', 'LIKE', '%'.request('s').'%');
                                })
                                ->latest()
                                ->paginate(5)
                                ->withQueryString();

        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating an article.
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created article in database.
     */
    public function store(StoreArticleRequest $request)
    {

        $request->user()->articles()->create(array_merge($request->only('title', 'content'), [
            'slug' => Str::slug($request->title) . uniqid("-"),
            'excerpt' => Str::limit($request->content, 200),
            'category_id' => $request->category
        ]));

        return redirect()->route('articles.index')->with('success', 'Article created successfully');
    }

    /**
     * Display the specified article.
     */
    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    /**
     * form for editing the specified article.
     */
    public function edit(Article $article)
    {
        // authorization
        $this->authorize('view', $article);

        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified article in database.
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        // authorization
        $this->authorize('update', $article);

        $article->update(array_merge($request->only('title', 'content'), [
            'slug' => Str::slug($request->title) . uniqid('-'),
        ]));

        return redirect()->route('articles.index')->with('success', 'Article updated');
    }

    /**
     * Remove the specified resource from database.
     */
    public function destroy(Article $article)
    {
        // authorization
        $this->authorize('delete', $article);

        $article->delete();

        // if admin delete article, redirect back to admin dashboard
        if(auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Article deleted successfully');
        }

        return redirect()->route('articles.index')->with('success', 'Article deleted successfully');
    }

    // search articles
    public function search()
    {
        $articles = Article::when((bool) request('category') ?? false, fn($article) =>
                                $article->whereBelongsTo(Category::whereSlug(request('category'))->first())
                                )
                                ->when(request()->has('s'), function($article) {
                                    return $article->orWhere('title', 'LIKE', '%'.request('s').'%');
                                })
                                ->latest()
                                ->paginate(5)
                                ->withQueryString();

        return view('articles.index', compact('articles'));
    }
}
