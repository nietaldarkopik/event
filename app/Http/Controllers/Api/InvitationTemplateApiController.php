<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvitationTemplate;

class InvitationTemplateApiController extends Controller
{
    public function getByCategory($category)
    {
        $templates = InvitationTemplate::where('category', $category)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'category']);

        return response()->json($templates);
    }
}
