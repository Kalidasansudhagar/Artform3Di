<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('admin.projects.index', [
            'projects' => Project::query()->orderBy('sort_order')->orderBy('id')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.projects.form', ['project' => new Project(['is_active' => true, 'sort_order' => Project::max('sort_order') + 1])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $project = new Project;
        $this->save($request, $project);

        return redirect()->route('admin.projects.index')->with('status', "“{$project->title}” added.");
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.form', ['project' => $project]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->save($request, $project);

        return redirect()->route('admin.projects.index')->with('status', "“{$project->title}” saved.");
    }

    public function destroy(Project $project): RedirectResponse
    {
        Uploads::delete([$project->image]);
        $project->delete();

        return redirect()->route('admin.projects.index')->with('status', "“{$project->title}” deleted.");
    }

    private function save(Request $request, Project $project): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:80'],
            'summary' => ['required', 'string', 'max:400'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $image = $project->image;
        if ($request->hasFile('image')) {
            Uploads::delete([$project->image]);
            $image = Uploads::store([$request->file('image')], 'projects')[0];
        }

        $project->fill([
            'title' => $data['title'],
            'category' => $data['category'],
            'summary' => $data['summary'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'image' => $image,
        ])->save();
    }
}
