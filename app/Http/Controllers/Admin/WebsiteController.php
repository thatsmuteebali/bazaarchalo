<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSectionSliderImage;
use App\Models\WebContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteController extends Controller
{
    public function setting(Request $request)
    {
        $page = $request->query('page', 'setting');

        return view('admin.setting', [
            'page' => $page,
            'pageTitle' => Str::headline($page),
            'webcontents' => WebContent::where('page', $page)->orderBy('id')->get(),
            'sliders' => $page === 'home'
                ? HeroSectionSliderImage::orderBy('id')->get()
                : collect(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['page' => ['required', 'string', 'max:100']]);

        $page = $request->input('page');
        $isHome = $page === 'home';

        $webcontents = WebContent::where('page', $page)->get();

        abort_if($webcontents->isEmpty() && ! $isHome, 404);

        // ---------- Rules for web content rows ----------
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
                    default    => ['required', 'string', 'max:255'],
                };
            }

            $attributes[$key] = $content->field;
        }

        // ---------- Rules for hero slider rows (home page only) ----------
        $syncSliders = $isHome && $request->boolean('has_sliders');
        $sliderRows = $syncSliders ? (array) $request->input('sliders', []) : [];

        $n = 0;
        foreach ($sliderRows as $i => $row) {
            $n++;
            $isExisting = ! empty($row['id']);

            $rules["sliders.$i.id"] = ['nullable', 'integer', 'exists:hero_section_slider_images,id'];
            $rules["sliders.$i.name"] = ['required', 'string', 'max:255'];
            $rules["sliders.$i.image"] = [
                $isExisting ? 'nullable' : 'required',
                'file',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:4096',
            ];

            $attributes["sliders.$i.name"] = "slide $n name";
            $attributes["sliders.$i.image"] = "slide $n image";
        }

        $request->validate($rules, [], $attributes);

        // ---------- Save web content rows ----------
        foreach ($webcontents as $content) {
            if ($content->type === 'image') {
                $file = $request->file('images.' . $content->id);

                if (! $file) {
                    continue;
                }

                $oldPath = $content->value;

                $content->value = $file->store('website', 'public');
                $content->save();

                $this->deleteImage($oldPath);

                continue;
            }

            $content->value = $request->input('contents.' . $content->id) ?? '';
            $content->save();
        }

        // ---------- Save hero sliders ----------
        if ($syncSliders) {
            $this->syncSliders($request, $sliderRows);
        }

        return redirect()
            ->route('admin.website.setting', ['page' => $page])
            ->with('success', 'Website content updated successfully');
    }

    private function syncSliders(Request $request, array $rows): void
    {
        $keepIds = collect($rows)->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();

        // Delete slides that were removed in the form (and their images)
        HeroSectionSliderImage::whereNotIn('id', $keepIds)->get()->each(function ($slide) {
            $this->deleteImage($slide->slider_image);
            $slide->delete();
        });

        // Create or update the rest
        foreach ($rows as $i => $row) {
            $file = $request->file("sliders.$i.image");

            $slide = ! empty($row['id']) ? HeroSectionSliderImage::find($row['id']) : null;
            $slide ??= new HeroSectionSliderImage();

            $oldPath = $slide->slider_image;

            $slide->name = $row['name'];

            if ($file) {
                $slide->slider_image = $file->store('website/hero-sliders', 'public');
            }

            $slide->save();

            if ($file) {
                $this->deleteImage($oldPath);
            }
        }
    }

    private function deleteImage(?string $path): void
    {
        if ($path && $path !== 'no_image' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}