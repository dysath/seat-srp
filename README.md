# seat-srp
A module for SeAT that tracks SRP requests

This plugin write for [SeAT](https://github.com/eveseat/seat) is providing to your instance a way to manage your ship replacement program (SRP)

[![Latest Stable Version](https://img.shields.io/packagist/v/denngarr/seat-srp.svg?style=flat-square)]()
[![Build Status](https://img.shields.io/travis/dysath/seat-srp.svg?style=flat-square)](https://travis-ci.org/dysath/seat-srp)
[![License](https://img.shields.io/badge/license-GPLv2-blue.svg?style=flat-square)](https://raw.githubusercontent.com/dysath/seat-srp/master/LICENSE)

If you have issues with this, you can contact me on Eve as **Denngarr B'tarn**, or on email as 'denngarr@cripplecreekcorp.com'

## Quick Installation:

In your seat directory (By default:  /var/www/seat), type the following:

```
php artisan down
composer require denngarr/seat-srp
```

After a successful installation, you can include the actual plugin by editing **config/app.php** and adding the following after:

```
        /*
         * Package Service Providers...
         */
```
add
```
        Denngarr\Seat\SeatSrp\SrpServiceProvider::class
```

and save the file.  Now you're ready to tell SeAT how to use the plugin:

```
php artisan vendor:publish --force
php artisan migrate

php artisan up
```

And now, when you log into 'Seat', you should see a 'Ship Replacement Program' link on the left.

Good luck, and Happy Hunting!!  o7


