<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteController extends Controller
{
    public function setting(Request $request)
    {
        $page = $request->query('page', 'setting');

        $webcontents = WebContent::where('page', $page)->orderBy('id')->get();

        return view('admin.setting', [
            'page' => $page,
            'pageTitle' => Str::headline($page),
            'webcontents' => $webcontents,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['page' => ['required', 'string', 'max:100']]);

        $page = $request->input('page');
        $webcontents = WebContent::where('page', $page)->get();

        abort_if($webcontents->isEmpty(), 404);

        // Build validation rules from each row's type in the database
        $rules = [];
        $attributes = [];

        foreach ($webcontents as $content) {
            if ($content->type === 'image') {
                $key = 'images.' . $content->id;
                $rules[$key] = ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg,ico', 'max:2048'];
            } else {
                $key = 'contents.' . $content->id;
                $rules[$key] = match ($content->type) {
                    'color'    => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
                    'url'      => ['nullable', 'url', 'max:2048'],
                    'email'    => ['required', 'email', 'max:255'],
                    'textarea' => ['nullable', 'string', 'max:1000'],
                    'editor'   => ['nullable', 'string'],
                    default    => ['required', 'string', 'max:255'], // text
                };
            }

            $attributes[$key] = $content->field;
        }

        $request->validate($rules, [], $attributes);

        foreach ($webcontents as $content) {
            if ($content->type === 'image') {
                $file = $request->file('images.' . $content->id);

                if (! $file) {
                    continue; // no new image, keep the old one
                }

                $oldPath = $content->value;

                $content->value = $file->store('website', 'public');
                $content->save();

                // Delete the previous image only after the new one is saved
                if ($oldPath && $oldPath !== 'no_image' && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }

                continue;
            }

            // The value column is not nullable, so store an empty string instead of null
            $content->value = $request->input('contents.' . $content->id) ?? '';
            $content->save();
        }

        return redirect()
            ->route('admin.website.setting', ['page' => $page])
            ->with('success', 'Website content updated successfully');
    }
}