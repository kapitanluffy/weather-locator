## Installation

1. Clone the git repository by entering the following command in your terminal
```
git clone git@github.com:kapitanluffy/weather-locator.git
```

2. Install composer dependencies by entering the following command in your terminal.
```
composer install
```

3. Download database [here](https://drive.google.com/open?id=12O5EkwF6FEXcWugHyy0TKhhbv0re_gw-)

4. Copy database to `./var/data/data.sqlite`.

This app was built and tested under `PHP 7.1`

## Running

1. You can use the built in php web server by running the following command in your terminal.
```
php bin/console server:run
```

2. Access the app in your preferred browser by putting the following url in the address bar.
```
http://localhost:8000/
```

You can use also use a dedicated web server of your choice. Popular choices are `nginx` and `apache`.
You just need to take note that you should point the "public" directory to `./web`

Configuration and setup will vary depending to the web server of your choice.
I suggest you refer to the documentation of the web server of your choice for setting this up.

## Testing

Run unit tests by entering the following command in your terminal.

```
php vendor/bin/simple-phpunit
```

*Take note that all commands are assumed to be run in the applications root directory*
