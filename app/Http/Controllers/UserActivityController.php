<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;

class UserActivityController extends Controller
{
    public function index()
    {
        $activities = UserActivity::latest()->paginate(20);
        return view('admin.activities.index', compact('activities'));
    }
}
