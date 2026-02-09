# Laravel Elastic Email Driver

A Laravel mail driver for [Elastic Email](https://elasticemail.com/) service.

## Compatibility

- **Laravel**: 9.x, 10.x, 11.x, 12.x
- **PHP**: 8.0+

## Installation

Install the package via Composer:

```bash
composer require ebethus/laravel-elastic-email-driver
```

## Configuration

### 1. Add Elastic Email Credentials

Add the following configuration to your `config/services.php`:

```php
'elastic_email' => [
    'key' => env('ELASTIC_KEY'),
    'account' => env('ELASTIC_ACCOUNT'),
],
```

### 2. Set Environment Variables

Add your Elastic Email credentials to your `.env` file:

```env
ELASTIC_KEY=your-elastic-email-api-key
ELASTIC_ACCOUNT=your-elastic-email-account
```

### 3. Configure Mail Driver

For **Laravel 9+**, update your `config/mail.php` to add the Elastic Email mailer:

```php
'mailers' => [
    // ... other mailers
    
    'elastic_email' => [
        'transport' => 'elastic_email',
    ],
],
```

Then set it as your default mailer in `.env`:

```env
MAIL_MAILER=elastic_email
```

**For older Laravel versions**, set:

```env
MAIL_DRIVER=elastic_email
```

## Usage

This package integrates seamlessly with Laravel's mail system. Use Laravel's `Mail` facade as you normally would:

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

Mail::to('user@example.com')->send(new WelcomeEmail());
```

For more information on sending emails, refer to the [Laravel Mail documentation](https://laravel.com/docs/mail).

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Support

For issues or questions, please contact [alberroteran@gmail.com](mailto:alberroteran@gmail.com).
