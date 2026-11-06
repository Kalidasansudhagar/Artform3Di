<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Product;
use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create(['email' => 'admin@example.test', 'password' => 'secret-pass-123']);
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    public function test_guests_are_sent_to_the_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/products')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk()->assertSee('Sign in');
    }

    public function test_admin_can_sign_in_and_out(): void
    {
        $this->admin();

        $this->post('/admin/login', ['email' => 'admin@example.test', 'password' => 'secret-pass-123'])->assertRedirect('/admin');
        $this->assertAuthenticated();
        $this->get('/admin')->assertOk()->assertSee('Enquiries received');

        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_wrong_password_and_non_admins_are_rejected(): void
    {
        $this->admin();
        $this->post('/admin/login', ['email' => 'admin@example.test', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $customer = User::factory()->create(['password' => 'customer-pass-1']);
        $this->post('/admin/login', ['email' => $customer->email, 'password' => 'customer-pass-1'])->assertSessionHasErrors('email');
        $this->actingAs($customer)->get('/admin')->assertForbidden();
    }

    public function test_every_admin_page_renders(): void
    {
        $admin = $this->admin();
        $product = Product::query()->create(['name' => 'Test Vase', 'slug' => 'test-vase', 'category' => 'decorative-sculptures', 'images' => []]);
        $project = Project::query()->create(['title' => 'Bracket', 'category' => 'Automotive', 'summary' => 'Recreated bracket.']);
        $enquiry = Enquiry::query()->create(['name' => 'Asha', 'email' => 'asha@example.test', 'subject' => 'CAD design', 'message' => 'Need a model of a part.']);

        foreach (['/admin', '/admin/enquiries', "/admin/enquiries/{$enquiry->id}", '/admin/products', '/admin/products/create', "/admin/products/{$product->id}/edit",
            '/admin/projects', '/admin/projects/create', "/admin/projects/{$project->id}/edit", '/admin/customers', '/admin/settings', '/admin/profile'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_website_form_creates_an_enquiry_the_admin_can_manage(): void
    {
        $this->postJson('/enquiries', ['name' => 'Ravi', 'email' => 'ravi@example.test', 'phone' => '+91 90000 00000', 'subject' => 'Parts restoration', 'message' => 'Broken plastic clip from my car.'])
            ->assertOk()->assertJson(['ok' => true]);
        $this->postJson('/enquiries', ['name' => '', 'email' => 'bad'])->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);

        $enquiry = Enquiry::query()->sole();
        $this->assertSame('new', $enquiry->status);

        $admin = $this->admin();
        $this->actingAs($admin)->get('/admin/enquiries?q=ravi')->assertSee('Ravi');
        $this->actingAs($admin)->get('/admin/customers')->assertSee('ravi@example.test');
        $this->actingAs($admin)->put("/admin/enquiries/{$enquiry->id}", ['status' => 'quoted', 'admin_notes' => 'Sent quote.'])->assertRedirect();
        $this->assertSame('quoted', $enquiry->fresh()->status);

        $this->actingAs($admin)->delete("/admin/enquiries/{$enquiry->id}")->assertRedirect('/admin/enquiries');
        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_honeypot_submissions_are_ignored(): void
    {
        $this->postJson('/enquiries', ['website' => 'spam.example', 'name' => 'Bot'])->assertOk();
        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_admin_can_create_edit_and_delete_a_product_with_images(): void
    {
        Storage::fake('uploads');
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/products', [
            'name' => 'Desk Organiser',
            'category' => 'decorative-sculptures',
            'price' => 1499,
            'is_active' => 1,
            'new_images' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.png')],
        ])->assertRedirect('/admin/products');

        $product = Product::query()->sole();
        $this->assertSame('desk-organiser', $product->slug);
        $this->assertCount(2, $product->images);
        Storage::disk('uploads')->assertExists(substr($product->images[0], strlen('uploads/')));

        $this->get('/shop')->assertSee('Desk Organiser');
        $this->get('/shop/desk-organiser')->assertOk()->assertSee('₹1,499');

        $removed = $product->images[0];
        $this->actingAs($admin)->put("/admin/products/{$product->id}", [
            'name' => 'Desk Organiser', 'category' => 'decorative-sculptures', 'price' => '', 'is_active' => 0, 'remove_images' => [$removed],
        ])->assertRedirect('/admin/products');
        $product->refresh();
        $this->assertCount(1, $product->images);
        Storage::disk('uploads')->assertMissing(substr($removed, strlen('uploads/')));
        $this->get('/shop/desk-organiser')->assertNotFound();

        $this->actingAs($admin)->delete("/admin/products/{$product->id}")->assertRedirect('/admin/products');
        $this->assertDatabaseCount('products', 0);
    }

    public function test_admin_can_manage_projects(): void
    {
        Storage::fake('uploads');
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/projects', ['title' => 'IoT Housing', 'category' => 'Product Development', 'summary' => 'An enclosure for a sensor board.', 'is_active' => 1, 'image' => UploadedFile::fake()->image('p.jpg')])
            ->assertRedirect('/admin/projects');
        $project = Project::query()->sole();
        $this->get('/')->assertSee('IoT Housing');

        $this->actingAs($admin)->delete("/admin/projects/{$project->id}")->assertRedirect('/admin/projects');
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_settings_change_the_public_contact_details(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->put('/admin/settings', [
            'contact_phone' => '+91 99999 11111', 'contact_email' => 'hello@example.test', 'contact_address' => 'Studio address', 'contact_hours' => 'Mon–Sat, 10am–7pm',
            'social_Instagram' => 'https://instagram.com/example',
        ])->assertRedirect();

        $this->assertSame('hello@example.test', Setting::query()->find('contact.email')->value);
        $this->get('/contact')->assertSee('hello@example.test')->assertSee('Mon–Sat, 10am–7pm');

        $this->actingAs($admin)->put('/admin/settings', ['contact_email' => 'not-an-email'])->assertSessionHasErrors('contact_email');
    }

    public function test_admin_can_change_their_password(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->put('/admin/profile', ['name' => 'Owner', 'email' => 'admin@example.test', 'current_password' => 'wrong', 'password' => 'new-password-99', 'password_confirmation' => 'new-password-99'])
            ->assertSessionHasErrors('current_password');
        $this->actingAs($admin)->put('/admin/profile', ['name' => 'Owner', 'email' => 'admin@example.test', 'current_password' => 'secret-pass-123', 'password' => 'new-password-99', 'password_confirmation' => 'new-password-99'])
            ->assertSessionHasNoErrors();

        $this->post('/admin/logout');
        $this->post('/admin/login', ['email' => 'admin@example.test', 'password' => 'new-password-99'])->assertRedirect('/admin');
    }
}
