<?php

use App\Mail\NewRequestNotification;
use App\Models\Request as BuzonRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

test('the public form page loads', function () {
    $this->get(route('reportar.create'))->assertOk();
});

test('a visitor can submit a request with an attachment and receives a folio', function () {
    Storage::fake('local');
    Mail::fake();

    $file = UploadedFile::fake()->image('evidencia.jpg');

    $response = $this->post(route('reportar.store'), [
        'request_type' => 'queja',
        'is_anonymous' => '0',
        'full_name' => 'Juan Pérez',
        'department' => 'sistemas',
        'location' => 'Planta principal',
        'description' => 'Esta es una descripción de prueba con más de veinte caracteres.',
        'involved_people' => '',
        'urgency_level' => 'alto',
        'has_evidence' => '1',
        'wants_follow_up' => '1',
        'contact_info' => 'juan@example.com',
        'accepted_terms' => '1',
        'attachments' => [$file],
    ]);

    $request = BuzonRequest::firstOrFail();

    $response->assertRedirect(route('reportar.exito', ['folio' => $request->folio]));

    expect($request->folio)->toMatch('/^BZ-\d{4}-\d{6}$/');
    expect($request->attachments)->toHaveCount(1);

    Storage::disk('local')->assertExists($request->attachments->first()->file_path);

    Mail::assertQueued(NewRequestNotification::class, fn ($mail) => $mail->request->is($request));
});

test('anonymous requests never store the name or contact info', function () {
    Mail::fake();

    $this->post(route('reportar.store'), [
        'request_type' => 'sugerencia',
        'is_anonymous' => '1',
        'full_name' => 'Nombre que no debe guardarse',
        'department' => 'operaciones',
        'location' => 'Almacén',
        'description' => 'Esta es una descripción de prueba con más de veinte caracteres.',
        'urgency_level' => 'bajo',
        'has_evidence' => '0',
        'wants_follow_up' => '0',
        'contact_info' => 'no-deberia-guardarse@example.com',
        'accepted_terms' => '1',
    ]);

    $request = BuzonRequest::firstOrFail();

    expect($request->is_anonymous)->toBeTrue();
    expect($request->full_name)->toBeNull();
    expect($request->contact_info)->toBeNull();
});

test('the success page shows the folio without exposing sensitive data', function () {
    Mail::fake();

    $this->post(route('reportar.store'), [
        'request_type' => 'otro',
        'is_anonymous' => '1',
        'department' => 'otro',
        'location' => 'Oficinas',
        'description' => 'Esta es una descripción de prueba con más de veinte caracteres.',
        'urgency_level' => 'medio',
        'has_evidence' => '0',
        'wants_follow_up' => '0',
        'accepted_terms' => '1',
    ]);

    $request = BuzonRequest::firstOrFail();

    $response = $this->get(route('reportar.exito', ['folio' => $request->folio]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('public/ReportarExito')
        ->where('folio', $request->folio)
    );
});

test('the public form is rate limited', function () {
    Mail::fake();

    $payload = [
        'request_type' => 'queja',
        'is_anonymous' => '1',
        'department' => 'sistemas',
        'location' => 'Planta principal',
        'description' => 'Esta es una descripción de prueba con más de veinte caracteres.',
        'urgency_level' => 'bajo',
        'has_evidence' => '0',
        'wants_follow_up' => '0',
        'accepted_terms' => '1',
    ];

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('reportar.store'), $payload)->assertRedirect();
    }

    $this->post(route('reportar.store'), $payload)->assertStatus(429);
});

test('only admins can manage users', function () {
    $supervisor = User::factory()->create();

    $this->actingAs($supervisor)
        ->get(route('usuarios.index'))
        ->assertForbidden();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('usuarios.index'))
        ->assertOk();
});

test('inactive users cannot access the admin panel', function () {
    $user = User::factory()->inactive()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});
