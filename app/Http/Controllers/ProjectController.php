<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Services\ProjectServiceInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(private readonly ProjectServiceInterface $projectService) {}

    public function index(Request $request): Response
    {
        return Inertia::render('projects/Index', [
            'projects' => $this->projectService->getUserProjects($request->user()),
        ]);
    }

    public function show(int $id): Response
    {
        return Inertia::render('projects/Show', [
            'project' => $this->projectService->getProjectById($id),
        ]);
    }
}
