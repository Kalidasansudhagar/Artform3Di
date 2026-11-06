<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Customer accounts, bag, Buy Now, checkout and admin orders — end to end.
 */
class ShopFlowTest extends TestCase
{
    use RefreshDatabase;

    private function product(array $attributes = []): Product
    {
        return Product::query()->create($attributes + [
            'name' => 'Desk Organiser', 'slug' => 'desk-organiser', 'category' => 'decorative-sculptures', 'price' => 1499, 'images' => [], 'is_active' => true,
        ]);
    }

    private function customer(): User
    {
        return User::factory()->create(['email' => 'priya@example.test']);
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    private function address(array $overrides = []): array
    {
        return $overrides + [
            'full_name' => 'Priya Raman', 'phone' => '98765 43210', 'line1' => '12 Lake View Road', 'line2' => '',
            'city' => 'Chennai', 'state' => 'Tamil Nadu', 'postcode' => '600119',
        ];
    }

    public function test_customer_can_register_and_is_signed_in(): void
    {
        $this->postJson('/register', [
            'name' => 'Priya Raman', 'email' => 'Priya@Example.test', 'phone' => '98765 43210',
            'password' => 'printing42', 'password_confirmation' => 'printing42', 'terms' => true,
        ])->assertOk()->assertJson(['redirect' => route('account')]);

        $this->assertAuthenticated();
        $user = User::query()->where('email', 'priya@example.test')->sole();
        $this->assertFalse($user->is_admin);
        $this->get('/account')->assertOk()->assertSee('Welcome back,')->assertSee('Priya')->assertSee('priya@example.test');
    }

    public function test_registration_validates_and_rejects_duplicate_email(): void
    {
        $this->customer();

        $this->postJson('/register', ['name' => '', 'email' => 'priya@example.test', 'password' => 'short', 'password_confirmation' => 'other'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password', 'terms']);
        $this->assertGuest();
    }

    public function test_customer_can_sign_in_and_out(): void
    {
        $this->customer();

        $this->postJson('/login', ['email' => 'priya@example.test', 'password' => 'wrong'])->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertGuest();

        $this->postJson('/login', ['email' => 'priya@example.test', 'password' => 'password'])->assertOk()->assertJson(['redirect' => route('account')]);
        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_account_needs_sign_in_and_customers_cannot_open_admin(): void
    {
        $this->get('/account')->assertRedirect('/login');
        $this->get('/checkout')->assertRedirect('/login');

        $this->actingAs($this->customer());
        $this->get('/admin')->assertForbidden();
        $this->get('/login')->assertRedirect('/account');
        // A signed-in customer can still reach the admin sign-in page.
        $this->get('/admin/login')->assertOk();
    }

    public function test_add_to_bag_updates_count_and_bag_page(): void
    {
        $product = $this->product();

        $this->postJson('/cart', ['slug' => 'desk-organiser', 'quantity' => 2])->assertOk()->assertJson(['count' => 2]);
        $this->postJson('/cart', ['slug' => 'desk-organiser'])->assertOk()->assertJson(['count' => 3]);

        $this->get('/cart')->assertOk()->assertSee('Desk Organiser')->assertSee('₹4,497');
        $this->get('/')->assertSee('<meta name="cart-count" content="3">', false);

        $this->patch("/cart/{$product->id}", ['quantity' => 1])->assertRedirect('/cart');
        $this->get('/cart')->assertSee('₹1,499');

        $this->delete("/cart/{$product->id}")->assertRedirect('/cart');
        $this->get('/cart')->assertSee('Your bag is empty');
    }

    public function test_hidden_and_price_on_request_products_cannot_be_added(): void
    {
        $this->product(['slug' => 'hidden-piece', 'is_active' => false]);
        $this->product(['slug' => 'quote-piece', 'price' => null]);

        $this->postJson('/cart', ['slug' => 'hidden-piece'])->assertUnprocessable();
        $this->postJson('/cart', ['slug' => 'quote-piece'])->assertUnprocessable()->assertJsonPath('errors.slug.0', 'This piece is priced on request — please ask us for a quote.');
        $this->get('/shop/quote-piece')->assertSee('Request a Quote')->assertDontSee('Buy Now');
    }

    public function test_product_hidden_after_adding_drops_out_of_the_bag(): void
    {
        $product = $this->product();
        $this->postJson('/cart', ['slug' => 'desk-organiser']);
        $product->update(['is_active' => false]);

        $this->get('/cart')->assertSee('Your bag is empty');
    }

    public function test_admin_created_product_can_be_bought_with_buy_now_and_appears_in_admin_orders(): void
    {
        $admin = $this->admin();

        // Admin creates a product…
        $this->actingAs($admin)->post('/admin/products', [
            'name' => 'Lotus Lamp', 'category' => 'decorative-sculptures', 'price' => 2500, 'summary' => 'A lamp.', 'is_active' => 1,
        ])->assertRedirect();
        $this->post('/admin/logout');
        $this->get('/shop')->assertSee('Lotus Lamp')->assertSee('₹2,500');
        $this->get('/shop/lotus-lamp')->assertOk()->assertSee('Buy Now')->assertSee('Add to Cart');

        // …a customer uses Buy Now (adds to bag, then goes to checkout).
        $customer = $this->customer();
        $this->actingAs($customer);
        $this->postJson('/cart', ['slug' => 'lotus-lamp', 'quantity' => 2])->assertOk();
        $this->get('/checkout')->assertOk()->assertSee('Lotus Lamp')->assertSee('₹5,000');

        $this->post('/checkout', $this->address(['postcode' => '123']) + ['payment_method' => 'cod'])->assertSessionHasErrors('postcode');

        $response = $this->post('/checkout', $this->address() + ['payment_method' => 'cod', 'notes' => 'Gift wrap please']);
        $order = Order::query()->sole();
        $response->assertRedirect(route('account.orders.show', $order));

        $this->assertSame(5000, $order->total);
        $this->assertSame('pending', $order->status);
        $this->assertSame('priya@example.test', $order->customer_email);
        $this->assertSame(2, $order->items->sole()->quantity);
        $this->assertSame('600119', $customer->fresh()->address['postcode']);

        // Bag is emptied; the customer sees the order.
        $this->get('/cart')->assertSee('Your bag is empty');
        $this->get(route('account.orders.show', $order))->assertOk()->assertSee($order->number)->assertSee('Cash on delivery');
        $this->get('/account')->assertSee($order->number)->assertSee('₹5,000');

        // Admin sees and updates it; the customer sees the new status.
        $this->actingAs($admin);
        $this->get('/admin/orders')->assertOk()->assertSee($order->number)->assertSee('Priya Raman');
        $this->get("/admin/orders/{$order->id}")->assertOk()->assertSee('Lotus Lamp')->assertSee('Gift wrap please')->assertSee('12 Lake View Road');
        $this->get('/admin')->assertOk()->assertSee('Orders to confirm');
        $this->get('/admin/customers')->assertOk()->assertSee('priya@example.test');
        $this->put("/admin/orders/{$order->id}", ['status' => 'confirmed'])->assertRedirect();

        $this->actingAs($customer);
        $this->get('/account')->assertSee('Confirmed');
    }

    public function test_customers_cannot_see_each_others_orders(): void
    {
        $owner = $this->customer();
        $order = Order::query()->create([
            'user_id' => $owner->id, 'payment_method' => 'cod', 'subtotal' => 100, 'total' => 100,
            'customer_name' => 'Priya', 'customer_email' => $owner->email, 'customer_phone' => '9876543210', 'shipping_address' => $this->address(),
        ]);

        $this->actingAs(User::factory()->create())->get(route('account.orders.show', $order))->assertNotFound();
    }

    public function test_checkout_with_empty_bag_goes_back_to_bag(): void
    {
        $this->actingAs($this->customer())->get('/checkout')->assertRedirect('/cart');
    }

    public function test_customer_can_update_details_address_and_password(): void
    {
        $customer = $this->customer();
        $this->actingAs($customer);

        $this->put('/account', ['name' => 'Priya R', 'email' => 'priya.r@example.test', 'phone' => '9876543210'])->assertRedirect();
        $this->put('/account/address', $this->address())->assertRedirect();
        $this->put('/account/password', ['current_password' => 'wrong', 'password' => 'newpass99', 'password_confirmation' => 'newpass99'])->assertSessionHasErrorsIn('password', 'current_password');
        $this->put('/account/password', ['current_password' => 'password', 'password' => 'newpass99', 'password_confirmation' => 'newpass99'])->assertSessionHasNoErrors();

        $customer->refresh();
        $this->assertSame('priya.r@example.test', $customer->email);
        $this->assertSame('Chennai', $customer->address['city']);
        $this->get('/account')->assertSee('12 Lake View Road');

        $this->post('/logout');
        $this->postJson('/login', ['email' => 'priya.r@example.test', 'password' => 'newpass99'])->assertOk();
    }
}
