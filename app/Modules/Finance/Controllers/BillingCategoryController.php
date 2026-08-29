<?php
namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Requests\BillingCategoryRequest;
use App\Modules\Finance\Services\BillingCategoryServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Modules\Base\Traits\BaseApiResponse;

class BillingCategoryController extends Controller {
    use BaseApiResponse;
    protected $service;
    
    public function __construct(BillingCategoryServiceInterface $service) { 
        $this->service = $service; 
    }
    
    public function index(Request $request) {
        Gate::authorize('finance.view');
        $categories = $this->service->getAll();
        if ($request->wantsJson()) return $this->successResponse($categories);
        return view('finance::billing_categories.index', compact('categories'));
    }

    public function create() {
        Gate::authorize('finance.create');
        return view('finance::billing_categories.create');
    }

    public function store(BillingCategoryRequest $request) {
        Gate::authorize('finance.create');
        $category = $this->service->create($request->validated());
        if ($request->wantsJson()) return $this->successResponse($category, 'Category created', 201);
        return redirect()->route('billing-categories.index')->with('success', 'Kategori tagihan berhasil dibuat.');
    }

    public function edit($id) {
        Gate::authorize('finance.update');
        $category = $this->service->getById($id);
        return view('finance::billing_categories.edit', compact('category'));
    }

    public function update(BillingCategoryRequest $request, $id) {
        Gate::authorize('finance.update');
        $category = $this->service->update($id, $request->validated());
        if ($request->wantsJson()) return $this->successResponse($category, 'Category updated');
        return redirect()->route('billing-categories.index')->with('success', 'Kategori tagihan berhasil diperbarui.');
    }

    public function destroy(Request $request, $id) {
        Gate::authorize('finance.delete');
        $this->service->delete($id);
        if ($request->wantsJson()) return $this->successResponse(null, 'Category deleted');
        return redirect()->route('billing-categories.index')->with('success', 'Kategori tagihan berhasil dihapus.');
    }
}
