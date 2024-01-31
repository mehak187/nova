@extends('layouts.app')

@section('content')
<div class="w-full h-full flex items-center justify-center">
    <div>
        @foreach ($workspaces as $workspace)
            <form action="/simulation" method="POST" class="mb-2 text-center px-6">
                @csrf
                <input type="hidden" name="workspace_id" value="{{ $workspace->id }}">
                <button class="block w-full px-4 py-2 {{ in_array($workspace->id, $runningShifts) ? 'bg-green-500 border-green-600' : 'bg-blue-500 border border-blue-600' }} rounded text-white" type="submit">{{ $workspace->name }}</button>
            </form>
        @endforeach
    </div>
</div>
@endsection
