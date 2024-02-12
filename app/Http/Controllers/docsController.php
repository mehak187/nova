<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use App\Models\docs;

class docsController extends Controller
{
    public function index($asid)
    {
        return redirect(Nova::path().'/resources/docs/new?viaResource=assistants&viaResourceId='.$asid);
    }
}
