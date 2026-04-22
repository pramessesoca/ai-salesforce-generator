<?php

namespace App\Http\Controllers;

use App\Exceptions\SalesPageGenerationException;
use App\Http\Requests\RegenerateSalesPageRequest;
use App\Http\Requests\StoreSalesPageRequest;
use App\Models\SalesPage;
use App\Services\SalesPageGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class SalesPageController extends Controller
{
    public function __construct(private readonly SalesPageGeneratorService $generator)
    {
    }

    public function index(): Response
    {
        $this->authorize('viewAny', SalesPage::class);

        $salesPages = auth()->user()
            ->salesPages()
            ->latest()
            ->get();

        return Inertia::render('SalesPages/Index', [
            'salesPages' => $salesPages,
        ]);
    }

    public function show(SalesPage $salesPage): Response
    {
        $this->authorize('view', $salesPage);

        return Inertia::render('SalesPages/Show', [
            'salesPage' => $salesPage,
        ]);
    }

    public function store(StoreSalesPageRequest $request): RedirectResponse
    {
        $this->authorize('create', SalesPage::class);

        $validated = $request->validated();

        try {
            $generated = $this->generator->generate($validated);
        } catch (SalesPageGenerationException $e) {
            Log::error('Sales page generation failed', [
                'user_id' => $request->user()?->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['generation' => 'Failed to generate sales page. Please try again.']);
        }

        $salesPage = $request->user()->salesPages()->create([
            ...$validated,
            ...$generated,
        ]);

        return redirect()
            ->route('dashboard', ['preview' => $salesPage->id])
            ->with('status', 'Sales page generated successfully.');
    }

    public function regenerate(RegenerateSalesPageRequest $request, SalesPage $salesPage): RedirectResponse
    {
        $this->authorize('update', $salesPage);

        $input = [
            'product_name' => $salesPage->product_name,
            'description' => $salesPage->description,
            'key_features' => $salesPage->key_features,
            'target_audience' => $salesPage->target_audience,
            'price' => $salesPage->price,
            'unique_selling_points' => $salesPage->unique_selling_points,
        ];

        try {
            $generated = $this->generator->generate($input);
        } catch (SalesPageGenerationException $e) {
            Log::error('Sales page re-generation failed', [
                'user_id' => $request->user()?->id,
                'sales_page_id' => $salesPage->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['generation' => 'Failed to regenerate sales page. Please try again.']);
        }

        $salesPage->update($generated);

        return back()->with('status', 'Sales page regenerated successfully.');
    }

    public function destroy(SalesPage $salesPage): RedirectResponse
    {
        $this->authorize('delete', $salesPage);

        $salesPage->delete();

        return redirect()
            ->route('sales-pages.index')
            ->with('status', 'Sales page deleted successfully.');
    }
}
