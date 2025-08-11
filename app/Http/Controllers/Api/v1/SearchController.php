<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = User::query();
        if ($request->string('name')) {
            $query->whereHas('doctor')->where('first_name', 'LIKE', '%'.$request->string('name').'%')
                ->orWhere('last_name', 'LIKE', '%'.$request->string('name').'%');
        }

        if ($request->string('specialization')) {
            $query->whereHas('doctor', fn ($q) => $q->where('specialization', 'LIKE', '%'.$request->string('specialization').'%'));
        }

        if ($request->string('city')) {
            $query->whereHas('doctor', fn ($q) => $q->where('city', 'LIKE', '%'.$request->string('city').'%'));
        }

        return Response::json([
            'result' => $query->get() ?? 'no result',
            'message' => 'search result'
        ]);
    }
}
