<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function index()
    {
        return response()->json(Cache::remember('stats', 3600, function () {
            return [
                'total_users' => User::count(),
                'total_posts' => Post::count(),
                'users_without_posts' => User::doesntHave('posts')->count()
            ];
        }));
    }
}
