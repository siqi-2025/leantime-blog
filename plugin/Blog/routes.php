<?php

/**
 * Blog Plugin Routes
 *
 * Define all web routes for the Blog plugin
 */

use Illuminate\Support\Facades\Route;
use Leantime\Plugins\Blog\Controllers\PostList;
use Leantime\Plugins\Blog\Controllers\Create;
use Leantime\Plugins\Blog\Controllers\View;

// Blog routes group
Route::prefix('blog')->group(function () {

    // List all posts
    Route::get('/list', [PostList::class, 'get'])->name('blog.list');

    // Create new post
    Route::get('/create', [Create::class, 'get'])->name('blog.create.form');
    Route::post('/create', [Create::class, 'post'])->name('blog.create.submit');

    // View single post
    Route::get('/view/{id}', [View::class, 'get'])->name('blog.view');

    // Edit post (to be implemented)
    // Route::match(['GET', 'POST'], '/edit/{id}', [EditController::class, 'handle'])->name('blog.edit');

    // Delete post (to be implemented)
    // Route::get('/delete/{id}', [DeleteController::class, 'delete'])->name('blog.delete');

    // Categories management (to be implemented)
    // Route::match(['GET', 'POST'], '/categories', [CategoriesController::class, 'handle'])->name('blog.categories');
});
