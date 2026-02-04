<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use App\Models\Diagnostic;

class ScanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_scan_page_loads()
    {
        // Test que la page de scan se charge correctement
        $response = $this->get(route('scan.upload'));
        $response->assertStatus(200);
        $response->assertViewIs('scan.upload');
    }

    public function test_history_page_loads()
    {
        // Test que la page historique se charge
        $response = $this->get(route('scan.history'));
        $response->assertStatus(200);
    }

    public function test_stats_page_loads()
    {
        // Test que la page stats se charge
        $response = $this->get(route('scan.stats'));
        $response->assertStatus(200);
    }

    public function test_analyze_rejects_missing_image()
    {
        // Test que l'analyse rejette une requête sans image
        // envoyer JSON/AJAX pour obtenir réponse 422 de validation
        $response = $this->postJson(route('scan.analyze'), []);
        $response->assertStatus(422); // Validation failed
    }
}
