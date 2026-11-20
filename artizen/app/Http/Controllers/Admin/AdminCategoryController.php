<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use App\Services\CelebrationCatalogService;
use App\Services\ImageOptimizationService;
use App\Services\JsonStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    /**
     * Save dynamic categories.
     */
    public function saveCategory(Request $request)
    {
        $request->validate([
            'title'               => 'required|string|max:255',
            'nav_slug'            => 'nullable|string|max:100',
            'icon'                => 'nullable|string|max:100',
            'bg_color'            => 'nullable|string|max:20',
            'dropdown_badge'      => 'nullable|string|max:100',
            'display_order'       => 'nullable|integer',
            'slider_image_file'   => 'nullable|file|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'dropdown_image_file' => 'nullable|file|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $id = $request->input('id');
        $targetOrder = max(1, (int) $request->input('display_order', 1));
        $dbCat = $id ? Category::find($id) : null;

        // Shift existing category display orders
        if (!$id) {
            Category::where('display_order', '>=', $targetOrder)->increment('display_order');
        } else {
            if ($dbCat) {
                $oldOrder = (int) $dbCat->display_order;
                if ($oldOrder !== $targetOrder) {
                    if ($targetOrder < $oldOrder) {
                        Category::where('id', '!=', $dbCat->id)
                            ->where('display_order', '>=', $targetOrder)
                            ->where('display_order', '<', $oldOrder)
                            ->increment('display_order');
                    } else {
                        Category::where('id', '!=', $dbCat->id)
                            ->where('display_order', '>', $oldOrder)
                            ->where('display_order', '<=', $targetOrder)
                            ->decrement('display_order');
                    }
                }
            }
        }

        // Clean slug generation & collision resolution
        $title = trim($request->input('title'));
        $baseSlug = Str::slug($title);
        $candidateSlug = $baseSlug;
        $slugExists = Category::where('slug', $candidateSlug)
            ->when($id, fn($q) => $q->where('id', '!=', $id))
            ->exists();
        if ($slugExists) {
            $candidateSlug = $baseSlug . '-' . time();
        }

        $rawNavSlug = $request->input('nav_slug');
        $navSlug = $rawNavSlug ? Str::slug($rawNavSlug) : ('cat-' . $candidateSlug);

        // Build DB payload
        $dbPayload = [
            'title'          => $title,
            'slug'           => $candidateSlug,
            'icon'           => $request->input('icon', null),
            'bg_color'       => $request->input('bg_color', '#F6CFB2'),
            'nav_slug'       => $navSlug,
            'dropdown_badge' => $request->input('dropdown_badge', null),
            'display_order'  => $targetOrder,
            'active'         => $request->boolean('active', true),
        ];

        // Slider Image upload or URL with preservation on edit
        if ($request->hasFile('slider_image_file')) {
            try {
                $dbPayload['slider_image'] = app(ImageOptimizationService::class)->optimize(
                    file: $request->file('slider_image_file'),
                    targetSubdir: 'uploads/categories',
                    profile: 'category',
                    prefix: 'slider',
                    quality: ImageOptimizationService::DEFAULT_QUALITY
                );
            } catch (\Throwable $e) {
                Log::warning('Slider image optimization failed: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Slider image processing failed: ' . $e->getMessage());
            }
        } elseif ($request->has('slider_image')) {
            $dbPayload['slider_image'] = trim((string)$request->input('slider_image')) ?: null;
        } elseif ($dbCat) {
            $dbPayload['slider_image'] = $dbCat->slider_image;
        }

        // Dropdown Image upload or URL with preservation on edit
        if ($request->hasFile('dropdown_image_file')) {
            try {
                $dbPayload['dropdown_image'] = app(ImageOptimizationService::class)->optimize(
                    file: $request->file('dropdown_image_file'),
                    targetSubdir: 'uploads/categories',
                    profile: 'category',
                    prefix: 'dropdown',
                    quality: ImageOptimizationService::DEFAULT_QUALITY
                );
            } catch (\Throwable $e) {
                Log::warning('Dropdown image optimization failed: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Dropdown image processing failed: ' . $e->getMessage());
            }
        } elseif ($request->has('dropdown_image')) {
            $dbPayload['dropdown_image'] = trim((string)$request->input('dropdown_image')) ?: null;
        } elseif ($dbCat) {
            $dbPayload['dropdown_image'] = $dbCat->dropdown_image;
        }

        // Persist to DB
        if ($id && $dbCat) {
            $dbCat->update($dbPayload);
        } else {
            $dbPayload['count'] = 0;
            $dbCat = Category::create($dbPayload);
        }

        // Normalize full sequence to guarantee contiguous 1, 2, 3, ... N
        $allCategories = Category::orderBy('display_order')->orderBy('updated_at', 'desc')->get();
        foreach ($allCategories as $index => $catItem) {
            $expected = $index + 1;
            if ($catItem->display_order != $expected) {
                $catItem->update(['display_order' => $expected]);
            }
        }

        // Handle Subcategories JSON (Strictly capped to 18 for mega-menu balance)
        $subcatsJson = $request->input('subcategories_json', null);
        if ($subcatsJson !== null && $dbCat) {
            $subcats = json_decode($subcatsJson, true);
            if (is_array($subcats)) {
                $subcats = array_slice($subcats, 0, 18);
                $existingIds = [];
                foreach ($subcats as $idx => $sub) {
                    $name = trim($sub['name'] ?? '');
                    if (!$name) continue;
                    $group = trim($sub['group_name'] ?? '') ?: 'Setup Themes';
                    $subModel = Subcategory::updateOrCreate(
                        ['category_id' => $dbCat->id, 'name' => $name],
                        [
                            'slug'          => Str::slug($name),
                            'group_name'    => $group,
                            'badge'         => trim($sub['badge'] ?? '') ?: null,
                            'display_order' => $idx + 1,
                            'is_active'     => true,
                        ]
                    );
                    $existingIds[] = $subModel->id;
                }
                Subcategory::where('category_id', $dbCat->id)
                    ->whereNotIn('id', $existingIds)
                    ->delete();
            }
        }

        // Synchronize JSON legacy fallback
        $path = storage_path('app/categories.json');
        $categories = [];
        if (File::exists($path)) {
            $categories = json_decode(File::get($path), true) ?? [];
        }

        $catData = [
            'id'     => $dbCat ? $dbCat->id : time(),
            'title'  => $dbPayload['title'],
            'active' => $dbPayload['active'],
            'image'  => $dbPayload['slider_image'] ?? ($categories[0]['image'] ?? ''),
        ];

        $existingIndex = -1;
        foreach ($categories as $index => $c) {
            if ((string)($c['id'] ?? '') === (string)($id ?? '')) {
                $existingIndex = $index;
                break;
            }
        }
        if ($existingIndex !== -1) {
            $categories[$existingIndex] = array_merge($categories[$existingIndex], $catData);
        } else {
            $catData['count'] = 0;
            $categories[] = $catData;
        }
        JsonStorageService::write('categories.json', $categories);
        CelebrationCatalogService::clearCache();

        return redirect()->route('admin.categories')->with('success', 'Category saved successfully!');
    }

    /**
     * Delete Category.
     */
    public function deleteCategory($id)
    {
        $dbCat = Category::find($id);
        if ($dbCat) {
            // Disassociate packages safely
            \App\Models\Package::where('category_id', $dbCat->id)->update(['category_id' => null]);
            Subcategory::where('category_id', $dbCat->id)->delete();
            $dbCat->delete();

            // Re-normalize display_order sequence after deletion
            $remaining = Category::orderBy('display_order')->orderBy('id')->get();
            foreach ($remaining as $idx => $item) {
                $expected = $idx + 1;
                if ($item->display_order != $expected) {
                    $item->update(['display_order' => $expected]);
                }
            }
        }

        $cats = JsonStorageService::read('categories.json', []);
        $updatedCats = array_values(array_filter($cats, fn($c) => (string)($c['id'] ?? '') !== (string)$id));
        JsonStorageService::write('categories.json', $updatedCats);
        CelebrationCatalogService::clearCache();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!',
            ]);
        }

        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
    }

    /**
     * Bulk delete multiple categories safely.
     */
    public function bulkDeleteCategories(Request $request)
    {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required',
        ]);

        $ids = $validated['ids'];
        $deletedCount = 0;

        foreach ($ids as $id) {
            $dbCat = Category::find($id);
            if ($dbCat) {
                // Disassociate packages safely without data loss
                \App\Models\Package::where('category_id', $dbCat->id)->update(['category_id' => null]);
                // Delete linked subcategories
                Subcategory::where('category_id', $dbCat->id)->delete();
                $dbCat->delete();
                $deletedCount++;
            }
        }

        // Re-normalize display_order sequence after deletion
        $remaining = Category::orderBy('display_order')->orderBy('id')->get();
        foreach ($remaining as $idx => $item) {
            $expected = $idx + 1;
            if ($item->display_order != $expected) {
                $item->update(['display_order' => $expected]);
            }
        }

        // Update JSON storage fallback
        $idSet = array_flip(array_map('strval', $ids));
        $cats = JsonStorageService::read('categories.json', []);
        $updatedCats = array_values(array_filter($cats, fn($c) => !isset($idSet[(string)($c['id'] ?? '')])));
        JsonStorageService::write('categories.json', $updatedCats);
        CelebrationCatalogService::clearCache();

        return response()->json([
            'success'            => true,
            'message'            => $deletedCount . ' category(ies) permanently deleted!',
            'deleted_ids'        => $ids,
            'remaining_count'    => Category::count(),
            'active_count'       => Category::where('active', true)->count(),
            'inactive_count'     => Category::where('active', false)->count(),
            'subcategories_count'=> Subcategory::count(),
        ]);
    }

    /**
     * Toggle Category active status via AJAX.
     */
    public function toggleCategoryStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->active = !$category->active;
        $category->save();
        CelebrationCatalogService::clearCache();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'active'  => $category->active,
                'message' => 'Category status updated to ' . ($category->active ? 'Active' : 'Inactive') . '.',
            ]);
        }

        return redirect()->back()->with('success', 'Category status updated.');
    }
}
