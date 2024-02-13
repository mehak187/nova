<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use App\Models\paragraph;

class paragraphController extends Controller
{
    public function index($asid)
    {
        return redirect(Nova::path().'/resources/paragraphs/new?viaResource=assistants&viaResourceId='.$asid);
    }
}
