<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = auth()->user()->posts()
            ->orderBy('pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate();

        return PostResource::collection($posts);
    }

    public function store(PostRequest $request)
    {
        $data = $request->validated();
        $data['cover_image'] = $this->uploadImage($request->file('cover_image'));

        $post = auth()->user()->posts()->create($data);
        $post->tags()->sync($data['tags']);

        return new PostResource($post);
    }

    public function show(Post $post)
    {
        $this->authorize('view', $post);
        return new PostResource($post);
    }

    public function update(PostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $data = $request->validated();
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->uploadImage($request->file('cover_image'));
        }

        $post->update($data);
        $post->tags()->sync($data['tags']);

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return response()->noContent();
    }

    public function trashed()
    {
        $posts = auth()->user()->posts()
            ->onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate();

        return PostResource::collection($posts);
    }

    public function restore(int $id)
    {
        $post = auth()->user()->posts()->onlyTrashed()->findOrFail($id);
        $post->restore();
        return new PostResource($post);
    }

    private function uploadImage($image)
    {
        return $image->store('posts', 'public');
    }
}
