### How to run
To start copy the .env.example to a new .env file and fill in the db connection.

then run:
```shell
composer update
php artisan key:generate
php artisan migrate
```

run this for JWT key (used for signing tokens):
```shell
php artisan jwt:secret
```

to add the admin user and default customers run:
```shell
php artisan db:seed
```

admin credentials:
```text
email:admin@kiansoft.ir
password:@Aa1234567
```

#### To interact with the app you can use the APIs.

[API Documentation](https://documenter.getpostman.com/view/2535242/2s8YzXvfSp)

[Postman Collection](https://api.postman.com/collections/2535242-1eefcbf9-7b84-410a-a8a6-40b3d7af09e9?access_key=PMAT-01GME2F3HNKM9G4ZKHV95XMST5)

This project's APIs have been implemented using JSend standard.

[JSend Standard Documentation](https://github.com/omniti-labs/jsend)

#### Testing
You can run the feature and unit tests for endpoints and models by:
```shell
composer test
```


