# Re-Eddito

Welcome to Re-eddito project, a reddit / twitter like for a student project 

## Installation DDEV
The project work with DDEV
To install DDEV, see the documentation : https://docs.ddev.com/en/stable/users/install/ddev-installation/
For windows, if you have WSL 2, follow the next instructions (copypaste from ddev docs) : 
Go https://github.com/ddev/ddev/releases
Select the last Windows installer (14/12/2025) : https://github.com/ddev/ddev/releases/download/v1.24.10/ddev_windows_amd64_installer.v1.24.10.exe
Run the installer and choose your installation type :
- Docker CE inside WSL2 (Recommended): The installer will automatically install Docker CE in your WSL2 environment. This is the fastest and most reliable option.
- Docker Desktop/Rancher Desktop: Choose this if you already have Docker Desktop or Rancher Desktop installed or prefer to use them.

Warning : If you’re using Windows WSL2 with “Mirrored” networking mode, see the documentation https://docs.ddev.com/en/stable/#system-requirements

## Install phpstorm plugin install
To work easily with ddev and phpstorm, install ddev integration plugin
Main Menu => Settings => Plugin => search DDEV Integration and install it.
Restart Phpstorm 

## Install the project 
Clone the project (inside )
```bash
git clone git@github.com:plerat/re-eddito.git
```

## Launch the project
Go to re-eddito project
```bash
cd re-eddito
```

Start DDEV

For .env.local, DDEV generate its own
```bash
ddev start 
```
Install composer requirements 
```bash
ddev composer install
```
play migrations (database is created by default)
```bash
ddev console doctrine:migrations:migrate
```

Run fixtures in database
```bash
ddev console doctrine:fixtures:load
```

Build front
```bash
ddev console asset-map:compile
```

## Work with the project
Display all container working for the project and their adresses/url/port
```bash
ddev status 
ddev describe
```

List all DDEV projects
```bash
ddev list
```

Open the project in your browser
```bash
ddev launch
```

Users created in fixtures
```
id : user@user.xyz / pwd : user
id : admin@admin.xyz / pwd : admin
id : shadow@shadow.xyz / pwd : shadow
```

To shutdown DDEV. poweroff to shutdown all DDEV projects
```bash
ddev stop
ddev poweroff
```

## Run test
Create the test database
```bash
ddev console doctrine:database:create --env=test
```
Run migration and datafixtures
```bash
ddev console doctrine:migrations:migrate --env=test
ddev console doctrine:fixtures:load --env=test
```
Run phpunit test
```bash
ddev exec ./vendor/bin/phpunit
```


