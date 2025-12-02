<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStoredFileRequest;
use App\Http\Requests\UpdateStoredFileRequest;
use App\Models\StoredFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StoredFileController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = StoredFile::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $like = "%{$search}%";
                $q->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('storage_path', 'like', $like);
            });
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        if ($request->filled('is_public')) {
            $isPublic = filter_var($request->query('is_public'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isPublic !== null) {
                $query->where('is_public', $isPublic);
            }
        }

        $allowedOrderColumns = [
            'id',
            'title',
            'description',
            'category',
            'size_mb',
            'storage_path',
            'mime_type',
            'is_public',
            'expires_at',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $orderBy = $request->query('orderBy');
        $orderBy = in_array($orderBy, $allowedOrderColumns, true) ? $orderBy : 'created_at';

        $sortBy = strtolower($request->query('sortBy', 'desc')) === 'asc' ? 'asc' : 'desc';

        $limit = (int) $request->query('limit', 10);
        $limit = max(1, min(100, $limit));

        $files = $query
            ->orderBy($orderBy, $sortBy)
            ->paginate($limit)
            ->appends($request->query());

        return response()->json([
            'status' => 'success',
            'message' => 'Stored files retrieved.',
            'data' => $files->items(),
            'meta' => [
                'current_page' => $files->currentPage(),
                'per_page' => $files->perPage(),
                'total' => $files->total(),
                'last_page' => $files->lastPage(),
                'has_more_pages' => $files->hasMorePages(),
                'query' => $request->only(['limit', 'page', 'search', 'orderBy', 'sortBy', 'category', 'is_public']),
            ],
        ]);
    }

    public function store(StoreStoredFileRequest $request): JsonResponse
    {
        $payload = $this->preparePayload($request->validated());
        $storedFile = StoredFile::create($payload);

        return response()->json([
            'status' => 'success',
            'message' => 'Stored file created.',
            'data' => $storedFile,
        ], 201);
    }

    public function show(StoredFile $storedFile): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Stored file detail.',
            'data' => $storedFile,
        ]);
    }

    public function update(UpdateStoredFileRequest $request, StoredFile $storedFile): JsonResponse
    {
        $storedFile->update($this->preparePayload($request->validated()));

        return response()->json([
            'status' => 'success',
            'message' => 'Stored file updated.',
            'data' => $storedFile->fresh(),
        ]);
    }

    public function destroy(StoredFile $storedFile): JsonResponse
    {
        $storedFile->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Stored file deleted.',
        ]);
    }

        private function preparePayload(array $payload): array
        {
            if (isset($payload['expires_at'])) {
                $payload['expires_at'] = Carbon::parse($payload['expires_at'])->timezone(config('app.timezone'));
            }

            return $payload;
        }
}
