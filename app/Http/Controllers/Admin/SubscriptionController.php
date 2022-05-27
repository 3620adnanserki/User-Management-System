<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        // abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $subscriptions = Subscription::with(['user', 'created_by'])->orderBy('start_date','desc')->get();

        return view('admin.subscriptions.index', compact('subscriptions'));
    }
}
