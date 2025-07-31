<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['order_new', 'order_status', 'payment_received', 'retur_request', 'stock_low', 'delivery_scheduled'];
        $recipientTypes = ['employee', 'customer'];
        $priorities = ['low', 'normal', 'high', 'urgent'];
        
        return [
            'type' => $this->faker->randomElement($types),
            'title' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'recipient_type' => $this->faker->randomElement($recipientTypes),
            'recipient_id' => $this->faker->numberBetween(1, 10),
            'recipient_role' => $this->faker->randomElement(['admin', 'driver', 'gudang', 'keuangan', 'customer']),
            'data_type' => $this->faker->optional()->randomElement(['order', 'payment', 'retur', 'product']),
            'data_id' => $this->faker->optional()->numberBetween(1, 100),
            'is_read' => $this->faker->boolean(20), // 20% chance of being read
            'read_at' => $this->faker->optional()->dateTime(),
            'action_url' => $this->faker->optional()->url(),
            'icon' => $this->faker->randomElement(['fas fa-bell', 'fas fa-shopping-cart', 'fas fa-credit-card', 'fas fa-undo']),
            'priority' => $this->faker->randomElement($priorities),
        ];
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Indicate that the notification is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * Indicate that the notification is urgent.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }
}
