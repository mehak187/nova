<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_documents_for_the_current_client()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();

        $client2->documents()->create([
            'name' => 'test.pdf',
            'is_public' => true,
            'kdrive_file_id' => 1,
            'size' => 100,
            'mime_type' => 'application/pdf',
        ]);

        $this->actingAs($client1)->get('/documents')->assertDontSee('test.pdf');

        $this->actingAs($client2)->get('/documents')->assertSee('test.pdf');
    }
}
