<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Everything editable in the admin must show up on the public website.
 */
class DynamicContentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    private function product(array $attributes = []): Product
    {
        return Product::query()->create($attributes + [
            'name' => 'Desk Organiser', 'slug' => 'desk-organiser', 'category' => 'decorative-sculptures', 'price' => 1499, 'images' => [], 'is_active' => true,
        ]);
    }

    public function test_settings_drive_every_phone_whatsapp_and_address_on_the_site(): void
    {
        $this->actingAs($this->admin())->put('/admin/settings', [
            'contact_phone' => '98765 43210',
            'contact_address' => 'Plot 7, Test Street, Chennai',
            'contact_email' => 'studio@example.test',
            'contact_hours' => 'Mon–Sat 10–7',
            'social_Instagram' => 'https://instagram.com/artform-test',
        ]);

        // WhatsApp button in the closing call-to-action (home, about, 3D printing, drones).
        foreach (['/', '/about', '/3d-printing', '/drones'] as $url) {
            $this->get($url)->assertSee('https://wa.me/919876543210', false)->assertDontSee('916381298393');
        }

        // Contact page text, details, map and footer.
        $this->get('/contact')
            ->assertSee('98765 43210')
            ->assertSee('Plot 7, Test Street, Chennai')
            ->assertSee('studio@example.test')
            ->assertSee('Mon–Sat 10–7')
            ->assertSee('https://instagram.com/artform-test', false)
            ->assertSee(rawurlencode('Plot 7, Test Street, Chennai'), false)
            ->assertDontSee('63812 98393');

        $this->get('/')->assertSee('studio@example.test')->assertSee('Plot 7, Test Street, Chennai');
    }

    public function test_cleared_settings_show_to_be_confirmed_instead_of_old_values(): void
    {
        $this->actingAs($this->admin())->put('/admin/settings', ['contact_phone' => '', 'contact_address' => '']);

        $this->get('/contact')->assertSee('Phone / WhatsApp to be confirmed')->assertSee('Address to be confirmed')->assertDontSee('63812 98393');
        $this->get('/')->assertDontSee('wa.me', false);
    }

    public function test_product_changes_appear_on_home_shop_and_product_page(): void
    {
        $product = $this->product(['description' => 'Holds pens, cards and a phone.', 'material' => 'PETG, matte black']);

        $this->get('/')->assertSee('Desk Organiser');
        $this->get('/shop')->assertSee('Desk Organiser')->assertSee('₹1,499');
        $this->get('/shop/desk-organiser')->assertOk()->assertSee('Holds pens, cards and a phone.')->assertSee('PETG, matte black');

        $this->actingAs($this->admin())->put("/admin/products/{$product->id}", [
            'name' => 'Desk Organiser Pro', 'category' => 'decorative-sculptures', 'price' => '', 'is_active' => 1,
        ]);

        $this->get('/shop')->assertSee('Desk Organiser Pro')->assertSee('Price on request');
        $this->get('/shop/desk-organiser')->assertSee('Desk Organiser Pro')->assertSee('Price on request');
    }

    public function test_hidden_and_deleted_products_disappear_from_the_site(): void
    {
        $product = $this->product();
        $admin = $this->admin();

        $this->actingAs($admin)->put("/admin/products/{$product->id}", ['name' => 'Desk Organiser', 'category' => 'decorative-sculptures', 'is_active' => 0]);
        $this->get('/')->assertDontSee('Desk Organiser');
        $this->get('/shop')->assertDontSee('Desk Organiser');
        $this->get('/shop/desk-organiser')->assertNotFound();

        $this->actingAs($admin)->delete("/admin/products/{$product->id}");
        $this->get('/shop/desk-organiser')->assertNotFound();
    }

    public function test_homepage_features_the_first_six_visible_products_by_sort_order(): void
    {
        foreach (range(1, 8) as $i) {
            $this->product(['name' => "Item {$i}", 'slug' => "item-{$i}", 'sort_order' => 9 - $i]);
        }

        // Lowest sort order first: Item 8 (sort 1) … Item 3 (sort 6) are featured; Item 1 and 2 are not.
        $this->get('/')->assertSeeInOrder(['Item 8', 'Item 7', 'Item 6'])->assertDontSee('Item 1<', false)->assertDontSee('Item 2<', false);
        $this->get('/shop')->assertSee('Item 1')->assertSee('Item 2');
    }

    public function test_project_changes_appear_on_home_and_3d_printing_pages(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/admin/projects', ['title' => 'Sensor Housing', 'category' => 'Product Development', 'summary' => 'Enclosure for a sensor board.', 'is_active' => 1]);
        $project = Project::query()->sole();

        $this->get('/')->assertSee('Sensor Housing')->assertSee('Enclosure for a sensor board.');
        $this->get('/3d-printing')->assertSee('Sensor Housing');

        $this->actingAs($admin)->put("/admin/projects/{$project->id}", ['title' => 'Sensor Housing', 'category' => 'Product Development', 'summary' => 'Enclosure for a sensor board.', 'is_active' => 0]);
        $this->get('/')->assertDontSee('Sensor Housing');
        $this->get('/3d-printing')->assertDontSee('Sensor Housing');
    }

    public function test_homepage_survives_more_than_six_projects(): void
    {
        foreach (range(1, 8) as $i) {
            Project::query()->create(['title' => "Project {$i}", 'category' => 'Test', 'summary' => 'Summary', 'sort_order' => $i]);
        }

        $this->get('/')->assertOk()->assertSee('Project 6')->assertDontSee('Project 7');
    }
}
