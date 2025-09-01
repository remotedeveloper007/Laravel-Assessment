# Laravel Developer – Final Requirements

Objective:
Build a secure and optimized Laravel web application demonstrating your expertise in:

- Multi-authentication system
- Real-time updates using websockets
- Web push notifications
- Efficient large-scale product import using Laravel queues and batch processing

## Setup (local)

1. Clone the repository

`git clone https://github.com/remotedeveloper007/Laravel-Assessment.git`

`cd Laravel-Assessment`


2. Install dependencies

`composer install`
`npm install`


3. Environment configuration

`cp .env.example .env`
`php artisan key:generate`


4. Configure your database in .env and run migrations

`php artisan migrate --seed`


## Authentication Strategy

The system implements a multi-auth approach using Laravel's built-in authentication:

- Customers: Regular users who can place orders
- Admins: Privileged users with access to product management and order processing

Route protection is implemented through middleware:

- `auth:customer` for customer-specific routes
- `auth:admin` for administrative routes

## WebSocket Implementation

Real-time features are powered by Laravel WebSockets, providing:

- Live order status updates
- User presence tracking
- Scalable WebSocket server implementation

To install WebSocket server:

`composer require beyondcode/laravel-websockets --with-all-dependencies`

To start the WebSocket server:

`php artisan websockets:serve`


## Web Push Notifications

Push notifications are implemented using the Web Push protocol:

- VAPID key-based authentication
- Service Worker integration (`public/sw.js`)
- Browser-based subscription management

Key features:
- Real-Time Order status change notifications
- Custom notification preferences
- Subscription management through API endpoints

To install WebPush package:

`composer require minishlink/web-push`

## Bulk Product Import

The system features an optimized bulk import process for products:

- Queued job processing for large datasets
- Chunk-based CSV handling
- Memory-efficient processing
- Error handling and reporting

Sample import file (`products_sample_import.csv`) is included in the repository for testing.

## Testing

The project includes comprehensive test coverage:

`php artisan test`


Test suites cover:
- Product creation
- Order Placement
- Bulk import functionality

Test Passed:

## Unit Test: ImportValidationTest → validates import job.

-   PASS  Tests\Unit\ImportValidationTest
	✓ import job validates rows and creates products   16.74s  

## Feature Test: OrderPlacementTest => ensures customer can place orders.

-   PASS  Tests\Feature\OrderPlacementTest
	✓ customer can place order                          0.80s  

## Feature Test: ProductCreationTest => ensures admin can create products.

-   PASS  Tests\Feature\ProductCreationTest
	✓ admin can create product                          0.14s  

  Tests:    3 passed (10 assertions)          Duration: 18.10s

## Performance Considerations

- Database indexing on frequently queried fields
- Queued job processing for resource-intensive tasks
- Optimized database queries using eager loading
- Chunked processing for large datasets

## Technical Stack

- Laravel 10.x
- MySQL
- Laravel WebSockets
- Web Push
- Laravel Queue
- Laravel Events/Listeners
- PHPUnit for testing

## License

This project is licensed under the MIT License.