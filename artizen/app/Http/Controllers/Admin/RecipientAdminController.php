<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CelebrationRecipient;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class RecipientAdminController extends Controller
{
    /**
     * Display a listing of celebration recipients.
     */
    public function index(Request $request)
    {
        $recipients = CelebrationRecipient::ordered()->get();
        $totalCount = $recipients->count();
        $publishedCount = $recipients->where('is_active', true)->count();
        $draftCount = $recipients->where('is_active', false)->count();

        return view('admin.pages.recipients', [
            'recipients'       => $recipients,
            'totalCount'       => $totalCount,
            'publishedCount'   => $publishedCount,
            'draftCount'       => $draftCount,
            'adminActivePage'  => 'recipients',
        ]);
    }

    /**
     * Show form for creating a new celebration recipient.
     */
    public function create()
    {
        $recipient = new CelebrationRecipient([
            'is_active'     => true,
            'display_order' => (CelebrationRecipient::max('display_order') ?? 0) + 1,
        ]);
        $isEdit = false;

        return view('admin.pages.recipient-form', [
            'recipient'        => $recipient,
            'isEdit'           => $isEdit,
            'adminActivePage'  => 'recipients',
        ]);
    }

    /**
     * Show form for editing an existing celebration recipient.
     */
    public function edit($id)
    {
        $recipient = CelebrationRecipient::findOrFail($id);
        $isEdit = true;

        return view('admin.pages.recipient-form', [
            'recipient'        => $recipient,
            'isEdit'           => $isEdit,
            'adminActivePage'  => 'recipients',
        ]);
    }

    /**
     * Store or update a celebration recipient.
     */
    public function save(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'slug'           => 'nullable|string|max:100',
            'display_order'  => 'nullable|integer|min:1',
            'is_active'      => 'nullable',
            'image_file'     => 'nullable|file|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'existing_image' => 'nullable|string',
        ]);

        $id = $request->input('id');
        $recipient = $id ? CelebrationRecipient::findOrFail($id) : new CelebrationRecipient();

        $name = trim($request->input('name'));
        $slug = trim($request->input('slug')) ?: Str::slug($name);

        // Ensure unique slug except for current record
        $slugCheck = CelebrationRecipient::where('slug', $slug);
        if ($recipient->exists) {
            $slugCheck->where('id', '!=', $recipient->id);
        }
        if ($slugCheck->exists()) {
            $slug = $slug . '-' . time();
        }

        $recipient->name          = $name;
        $recipient->slug          = $slug;
        $recipient->category_slug = null;
        $recipient->custom_url    = null;
        $recipient->display_order = (int)($request->input('display_order', 1) ?: 1);
        $recipient->is_active     = $request->has('is_active') ? (bool)$request->input('is_active') : false;

        // Validate image presence for new records
        if (!$recipient->exists && !$request->hasFile('image_file') && !$request->filled('existing_image')) {
            return back()->withInput()->withErrors(['image_file' => 'Please upload or paste an image for this celebration persona.']);
        }

        // Process Image Upload with WebP conversion
        if ($request->hasFile('image_file')) {
            $uploadedImagePath = $this->convertAndSaveWebp($request->file('image_file'), $name);
            if ($uploadedImagePath) {
                $recipient->image = $uploadedImagePath;
            }
        } elseif ($request->filled('existing_image')) {
            $recipient->image = $request->input('existing_image');
        } else {
            $recipient->image = null;
        }

        $recipient->save();

        return redirect()->route('admin.recipients')->with('success', "Celebration recipient '{$recipient->name}' saved successfully.");
    }

    /**
     * Toggle active/draft status via AJAX or POST.
     */
    public function toggleStatus(Request $request, $id)
    {
        $recipient = CelebrationRecipient::findOrFail($id);
        $recipient->is_active = !$recipient->is_active;
        $recipient->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'is_active' => $recipient->is_active,
                'message'   => "Status updated to " . ($recipient->is_active ? 'Active' : 'Draft'),
            ]);
        }

        return redirect()->back()->with('success', "Status updated for '{$recipient->name}'.");
    }

    /**
     * Delete a celebration recipient.
     */
    public function destroy($id)
    {
        $recipient = CelebrationRecipient::findOrFail($id);
        $name = $recipient->name;
        $recipient->delete();

        return redirect()->route('admin.recipients')->with('success', "Recipient '{$name}' deleted successfully.");
    }

    /**
     * Convert an uploaded image to optimized WebP format.
     */
    protected function convertAndSaveWebp($file, string $name): ?string
    {
        $dir = public_path('images/for-everyone');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filename = 'recipient_' . Str::slug($name) . '_' . time() . '.webp';
        $targetPath = $dir . DIRECTORY_SEPARATOR . $filename;

        $sourcePath = $file->getRealPath();
        $mime = $file->getMimeType();

        $image = null;
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = @imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($sourcePath);
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;
            case 'image/webp':
                $image = @imagecreatefromwebp($sourcePath);
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($sourcePath);
                break;
            default:
                $image = @imagecreatefromstring(file_get_contents($sourcePath));
                break;
        }

        if ($image && function_exists('imagewebp')) {
            // Save at quality 85 for crisp resolution with lightweight file size
            imagewebp($image, $targetPath, 85);
            imagedestroy($image);
            return '/images/for-everyone/' . $filename;
        }

        // Fallback: move file if imagewebp is not available
        $file->move($dir, $filename);
        return '/images/for-everyone/' . $filename;
    }
}
