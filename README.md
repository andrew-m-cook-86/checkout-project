# Vestiaire Collective Assignment

The assignment brief was as follows:
## Backend - Technical Test

As a marketplace, we need to pay our sellers for every item that has been sold on our platform. In this task, you’ll be working with 2 main entities: Items to sell (products on the website) and Payouts instructions to send (bank transactions to seller accounts). Let’s assume that these entities have the following fields:

-   Item

-   Name
-   Price currency
-   Price amount
-   Seller reference

-   Payout

-   Seller reference
-   Amount
-   Currency

Let’s also assume we’re only working with following currencies: USD, EUR, GBP

### Goal

Expose an API endpoint that accepts a list of sold Items and creates Payouts for the sellers.

Following limitations apply:

-   A Payout is for a single seller, using a single currency.
-   The total amount of the Payout should be equal to the total price of the products in the request.
-   We should minimise the number of transactions as they incur a cost to the company; we should send as little Payouts per seller as possible.

-   Every Payout amount should not exceed a certain limit (we can’t send a million with one single transaction); if a Payout exceeds said amount, we should split it. This amount may be regularly updated by the business team.
-   Every Payout should be linked with at least one Item, so that we know exactly what Items have been paid out with each transaction


# Project Information



## Technology

Laravel 10
php8.2
MySQL
Redis
Docker

## Set Up and Installation

There are two ways to set the project up. Using Laravel Sail:
https://laravel.com/docs/10.x/sail

or docker-compose

From the root directory you can either use
```
sail up
```
or
```
docker-compose up
```

If you use sail, you'll be able to continue to use it to prompt CLI commands to the application. Otherwise you'll need to exec into the docker container. Image is: sail-8.2/app

## Database and Seeding

From inside docker container:

```
php artisan migrate:fresh --seed --seeder=DatabaseSeeder
```

Via Sail:
```
sail artisan migrate:fresh --seed --seeder=DatabaseSeeder
```

This will generate all your database tables in MySQL and also create some randomised users, randomised vendors and randomised products.

*Make sure that the .env file exists if any issues:
```
cp .env.example .env
```

## Logging In

One of the users generated will always have the following details:
email: test@test.com
password: password

You can use this user to test the endpoints. Alternatively you can create a user yourself by using the UI at http://localhost/

If you do create your own user, you'll need to verify their email (it can be fake) using a verification email sent to a local mailpit server at http://localhost:8025/

With your email / password credentials you can use the api endpoint like so:

```
curl --location 'http://localhost/api/login' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "test@test.com",
    "password": "password"
}'
```

This will return a token which you'll use to authenticate the other endpoint by appending it to a Authorization Bearer header.

Example Response:

```
{"token":"1|PQqm9F0JSc1xgCnvVavNLztB7QIjtyXUFWowLnlr"}
```

Example Usage:

```
curl --location 'http://localhost/api/order' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 1|PQqm9F0JSc1xgCnvVavNLztB7QIjtyXUFWowLnlr' \
--data '{"currency":"GBP","total":"2359.42","items":[{"name":"GhostWhite shoes","price":893,"id":6},{"name":"Linen hat","price":697.59,"id":14},{"name":"Maroon shirt","price":526,"id":8},{"name":"Lime coat","price":94,"id":10},{"name":"LightCyan hat","price":148.83,"id":3}],"transaction_id":"ch_fyRxTKzOnXnqKWbHFYQoNf5G"}'
```

A postman collection is included for your convenience


## Sending an Order

Because all products are randomise upon each database seed, to make it easier, there is a command which you can invoke to generate a valid payload for you.

```
php artisan app:generate-payload --items=5 --currency=gbp
```

Via Sail:
```
sail artisan app:generate-payload --items=5 --currency=gbp
```

The items and currency options are optional, but items can be between 1 and 15 and currency can be either gbp, usd, or eur. The payload generated is how a payload may look after a user has successfully submitted a payment to a third party (such as Stripe) before storing the successful response into the application.

The payload generated will be a collection of all the items a user has just purchased, the Stripe token returned from Stripe, and the currency the user selected. This payload would then be sent to our application.

Example payload after running the above artisan command:

```
{
   "currency":"GBP",
   "total":"2413.49",
   "items":[
      {
         "name":"RoyalBlue hat",
         "price":786.81,
         "id":2
      },
      {
         "name":"LightPink trousers",
         "price":209.98,
         "id":4
      },
      {
         "name":"LightSlateGray hat",
         "price":570.28,
         "id":15
      },
      {
         "name":"Linen hat",
         "price":697.59,
         "id":14
      },
      {
         "name":"LightCyan hat",
         "price":148.83,
         "id":3
      }
   ],
   "transaction_id":"ch_1mzDn0Fn6fve4IwrXCLXcQaq"
}
```

With this payload, it can be sent to the Order endpoint, (described earlier), with your auth token

```
curl --location 'http://localhost/api/order' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer 1|PQqm9F0JSc1xgCnvVavNLztB7QIjtyXUFWowLnlr' \
--data '{"currency":"GBP","total":"2359.42","items":[{"name":"GhostWhite shoes","price":893,"id":6},{"name":"Linen hat","price":697.59,"id":14},{"name":"Maroon shirt","price":526,"id":8},{"name":"Lime coat","price":94,"id":10},{"name":"LightCyan hat","price":148.83,"id":3}],"transaction_id":"ch_fyRxTKzOnXnqKWbHFYQoNf5G"}'
```

## POST api/order behaviour

Internally after submitting an order, the system will create a row in the Orders table, which will store the transaction_id, total, currency paid in, buyer id.
A pivot table between Products and Orders will also be populated to track which products were purchased with what Order, and the price of those individual items at the time of the Order (as Product prices can change in the future, so we need to store what they were at the time).

Some basic emails are also sent to the vendors / and buyer, but just demonstrative.

After the Orders and Order_Products are stored, a NewOrderEvent event is dispatched to generate Payouts for the sellers / vendors. Because the application mode is synchronous for this demonstration, this event will complete before the response is returned to the user, however in a real world application we would configure the application to run its queues asynchronously, on Redis or equivalent and so this would reduce response time etc.

## NewOrderEvent behaviour

This event will simply take the order and break it down into the Payouts table. The table will contain all records of payments owed to the vendors / sellers. Because a Order may contain multiple items and multiple vendors, the rows generated in the Payouts table should reflect the total money owed to each vendor.

If there are 4 items each worth $100 in the order, and Vendor A owns 3 and Vendor B owns 1, then there should be two resulting Payout rows generated, one for Vendor A worth $300 and one for Vendor B for $100.  All payout rows created are immediately assigned the status 'Pending'

Because the brief described wanting to send as few payments as possible to save on fees, the records in the payouts table will not immediately be honoured, and instead a scheduled job will run once a week to do all payouts.

This job can be invoked manually however, and is described in the next section.

## Process Payout behaviour

To process payouts you can run the following command

```
php artisan app:process-payout
```

Via Sail:
```
sail artisan app:process-payout
```

This job will select all payouts in a Pending status and begin trying to process them. Because there may be a large amount of rows, we will use Laravel's chunking to perform the queries to avoid large memory usage.

If a vendor has multiple Payouts owed to them, those will all be squashed together and then sent to the users account. In this application, I've decided to design around Stripe Connect, so all sellers /vendors in the application will have a Stripe account, which we will just transfer funds into.

To keep track of Payouts processed, each Instruction sent to the vendor via Stripe will be written into the Instruction table, which will contain a stripe charge token, a total cost, and which currency it was paid in. During the process all currencies in the Payouts table are converted to the Vendor's preferred currency. Also, because an Instruction may have many Payouts, this is again reflected in a pivot table instruction_payout which tracks all the payouts that may have been squashed together to form a single transaction. Alternatively, because the maximum we want to pay in a single transaction is 1,000,000 (according to the brief), if a payout exceeds that amount, then it will create multiple rows in the Instructions table (and populate the pivot table accordingly).

Once all is successful, all payments and instructions are marked as Successful in the Database. If a transaction / instruction fails, then it is marked as Failed. If a instruction / transaction occurs, but its payout was spread over multiple other transactions (such is the case as a payout that exceeds 1M), and those other instructions succeeded, then the payout is marked as Partial.

## Other

Due to time constraints, some parts of the application were not completed. Namely a job to retry partial / failed payouts and instructions. Also test coverage. Some tests were written but the project has exceeded the recommended time. 

