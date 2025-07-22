<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteCategoryController extends Controller
{
    /**
     * Store user's favorite categories
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'required|integer|exists:categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $categoryIds = $request->category_ids;

            // Remove existing favorites and add new ones
            $user->favoriteCategories()->detach();
            $user->favoriteCategories()->attach($categoryIds);

            return response()->json([
                'message' => 'Favorite categories saved successfully',
                'data' => [
                    'user_id' => $user->id,
                    'category_ids' => $categoryIds,
                    'total_favorites' => count($categoryIds)
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save favorite categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user's favorite categories (alternative method for partial updates)
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'category_ids' => 'required|array',
            'category_ids.*' => 'required|integer|exists:categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $categoryIds = $request->category_ids;

            // Sync favorites (this will add new ones and remove ones not in the list)
            $user->favoriteCategories()->sync($categoryIds);

            return response()->json([
                'message' => 'Favorite categories updated successfully',
                'data' => [
                    'user_id' => $user->id,
                    'category_ids' => $categoryIds,
                    'total_favorites' => count($categoryIds)
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update favorite categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's favorite categories
     */
    public function index(Request $request)
    {
        $user = $request->user();

        try {
            $favoriteCategories = $user->favoriteCategories()->get();

            return response()->json([
                'message' => 'Favorite categories retrieved successfully',
                'data' => $favoriteCategories,
                'total_favorites' => $favoriteCategories->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve favorite categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a single category to favorites
     */
    public function addFavorite(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $categoryId = $request->category_id;

            // Check if already favorited
            if ($user->favoriteCategories()->where('category_id', $categoryId)->exists()) {
                return response()->json([
                    'message' => 'Category is already in favorites',
                    'data' => ['category_id' => $categoryId]
                ], 409);
            }

            $user->favoriteCategories()->attach($categoryId);

            return response()->json([
                'message' => 'Category added to favorites successfully',
                'data' => ['category_id' => $categoryId]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add category to favorites',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a single category from favorites
     */
    public function removeFavorite(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $categoryId = $request->category_id;

            $user->favoriteCategories()->detach($categoryId);

            return response()->json([
                'message' => 'Category removed from favorites successfully',
                'data' => ['category_id' => $categoryId]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove category from favorites',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
