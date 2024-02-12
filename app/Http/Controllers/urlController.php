<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use App\Models\urls;


class urlController extends Controller
{
    public function index($asid)
    {
        return redirect(Nova::path().'/resources/urls/new?viaResource=assistants&viaResourceId='.$asid);
    }
   
}
