<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Upload;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $uploadsQuery = $user->isAdmin()
            ? Upload::query()->with(['user', 'categoryRef'])
            : $user->uploads()->with('categoryRef');

        $week = (clone $uploadsQuery)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $stats = [
            'total' => (clone $uploadsQuery)->count(),
            'images' => (clone $uploadsQuery)->where('type', 'image')->count(),
            'documents' => (clone $uploadsQuery)->where('type', 'document')->count(),
            'week' => $week,
        ];

        $recent = (clone $uploadsQuery)->latest()->limit(8)->get();

        return view('dashboard', compact('user', 'stats', 'recent'));
    }
}
