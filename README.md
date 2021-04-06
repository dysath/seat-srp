# seat-srp
A module for SeAT that tracks SRP requests

This plugin write for [SeAT](https://github.com/eveseat/seat) is providing to your instance a way to manage your ship replacement program (SRP)

[![Latest Stable Version](https://img.shields.io/packagist/v/denngarr/seat-srp.svg?style=flat-square)]()
[![Build Status](https://img.shields.io/travis/dysath/seat-srp.svg?style=flat-square)](https://travis-ci.org/dysath/seat-srp)
[![License](https://img.shields.io/badge/license-GPLv2-blue.svg?style=flat-square)](https://raw.githubusercontent.com/dysath/seat-srp/master/LICENSE)

If you have issues with this, you can contact me on Eve as **Crypta Electrica**, or on email as 'crypta@crypta.tech'

## Quick Installation:

In your seat directory (By default:  /var/www/seat), type the following:

```
php artisan down
composer require denngarr/seat-srp
```

(You can skip this step if you are running 3.x)
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

## Discord Webhook (optional)

Automated notifications of new SRP Requests submitted in Discord

***In Discord application:***

1. On a channel of your choice, click the cog icon to open the channel settings
2. In the channel settings, navigate to the Webhooks tab
3. Click `Create Webhook`
4. Fill in name for the webhook and (optional) image
5. Copy the Webhook URL
6. Click `Save` to finish creating the webhook

***In SeAT file:***

The Ship Replacement Program Settings page accepts two variables for the webhook:

1. (required) `Webhook URL`: this is the url you copied when creating the webhook in Discord
2. (optional) `Discord Mention Role`: this can be a room mention (e.g. `@here`), a Discord role ID, or a specific user ID
        - Role ID and User ID can be obtained by typing `/@rolename` into a channel (e.g. `/@srp_manager`) 


Example of entries:

```
Webhook URL = https://discordapp.com/api/webhooks/513619798362554369/Px9VQwiE5lhhBqOjW7rFBuLmLzMimwcklC2kIDJhQ9hLcDzCRPCkbI0LgWq6YwIbFtuk
Discord Mention Role = <@&198725153385873409>
```


Good luck, and Happy Hunting!!  o7


## Usage Tracking

In order to get an idea of the usage of this plugin, a very simplistic form of anonymous usage tracking has been implemented.

Read more about the system in use [here](https://github.com/Crypta-Eve/snoopy)
