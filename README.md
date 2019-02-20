## Installation

Add to dependencies

	"cartalyst/sentinel": "^2.0",
    "google/apiclient": "^2.0"

Add to providers

	Rjvim\Connect\ConnectServiceProvider::class,

Add to facades

	'Connect'   => Rjvim\Connect\ConnectFacade::class,

Run: `php artisan vendor:publish`

Add more columns to users table:

	$table->string('name')->nullable();
    $table->enum('gender', ['male', 'female', 'others'])->nullable();

Extend User model with `Rjvim\Connect\Models\User`

Add routes:
	
	Connect::google();
