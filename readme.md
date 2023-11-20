# Laravel - Password Manager 
*A package for managing the laravel application password*

## USER MANUAL 
**STEPS:-**
1. Create a `packages/souravmsh/` directory at the root of the Laravel application.
```json
	mkdir packages
	cd packages
	mkdir souravmsh
```
2. Clone the Repository from github.
```json
git clone https://github.com/souravmsh/password-manager.git
```
3. Add package repositories to the application-root `composer.json` file

```json
"repositories": [ 
    {
        "type": "path",
        "url": "./packages/souravmsh/password-manager"
    } 
]
```
```json
"require": { 
    "souravmsh/password-manager": "dev-main"
},
```

4. install package via comopser
```json
composer require souravmsh/password-manager:dev-main
```
or delete the ```composer.lock``` file and run
```json
composer install
```
5. It will automatically add the package to your application

6. Now, publish the app and select the package
```json
php artisan vendor:publish --provider="Souravmsh\PasswordManager\PackageServiceProvider"
```

7. Run migration 
```json
php artisan migrate
```


### HOW TO USE

##### PASSWORD VALIDATION 

To browse 
> {you_site}/password-manager 

To enable/disable the package, add the below variable to the .env file
> PASSWORD_MANAGER_EANBLE=true/false

Password expiry time (minitues)
> PASSWORD_MANAGER_EXPIRY_TIME=720

To check old password on update
> PASSWORD_MANAGER_CHECK_OLD=true/false

To change the default model of the user table
> PASSWORD_MANAGER_USER_MODEL="App\Models\User"

Use trait in a class
> use Souravmsh\PasswordManager\Http\Traits\PasswordManager;

> use PasswordManager;

Default fields - password, old_password, password_confirmation
> $this->passwordValidate();

Custom fields
> $this->passwordValidate('password_custom_name',
> 'old_password_custom_name');

Combine password rules with other existing rules
>     $this->validate($request, $this->passwordValidate('password', '', '', [
>         'name'  => 'required',
>         'email' => 'required',
>     ]));


