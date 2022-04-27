# Market API Documents

## Authorization
You can read about authorization, scopes and grant types here - [Forum API documents](https://github.com/grisha2217/Lolzteam-Public-API/blob/master/docs/api.markdown) `docs/api`
For using this api you need to get Access Token with **market** scope

## About Market API
Market API almost completely repeats WEB requests. Query parameters match. The only differences are the presence of PUT and DELETE methods (more on that below).
For example, a request to book an account on the WEB looks like this: `lolz.guru/market/:itemId/reserve?price=:accountPrice`, and an API request looks like this: `api.lolz.guru/market/:itemId/reserve?price=accountPrice`.

### API Base URI is **api.lolz.guru/**

### Rate limit is 20 request per minute.


### GET `/market`
Displays a list of latest accounts

Parameters:

 * N/A

### GET `/market/:categoryName`
Displays a list of accounts in a specific category according to your parameters

Parameters:

 * `pmin` (_optional_): Minimal price of account (Inclusive)
 * `pmax` (_optional_): Maximum price of account (Inclusive)
 * `title` (_optional_): The word or words contained in the account title
 * `showStickyItems` (_optional_): If yes, API will return stickied accounts in results
 * `Optional category parametes` (_optional_): You can find it using "Inspect code element" in your browser


### GET `/market/user/:userId`
Displays info about your account

Parameters:

 * N/A

### GET `/market/user/:userId/payments`
Displays list of your payments
 * `type` (_optional_): Type of operation
 **Allowed operation types:**
    income, 
    cost,
    refilled_balance,
		withdrawal_balance,
		paid_item,
		sold_item,
		money_transfer,
		receiving_money,
		internal_purchase,
		claim_hold
 * `pmin` (_optional_): Minimal price of operation (Inclusive)
 * `pmax` (_optional_): Maximum price of operation (Inclusive)
 * `receiver` (_optional_): Username of user, which receive money from you
 * `sender` (_optional_): Username of user, which sent money to you
 * `startDate` (_optional_): Start date of operation (RFC 3339 date format)
 * `endDate` (_optional_): End date of operation (RFC 3339 date format)
 * `wallet` (_optional_): Wallet, which used for money payots
 * `is_hold` (_optional_): Display hold operations

### GET `/market/user/:userId/orders`
Displays a list of purchased accounts

Parameters:

 * N/A

### GET `/market/user/:userId/orders/:categoryName`
Displays a list of purchased accounts

Parameters:
 * `pmin` (_optional_): Minimal price of account (Inclusive)
 * `pmax` (_optional_): Maximum price of account (Inclusive)
 * `title` (_optional_): The word or words contained in the account title
 * `showStickyItems` (_optional_): If yes, API will return stickied accounts in results
 * `Optional category parametes` (_optional_): You can find it using "Inspect code element" in your browser

### GET `/market/:itemId`
Displays account information

Parameters:

 * N/A

## Account purchasing
You need to make 3 requests:
**POST `/market/:itemId/reserve`**, **POST `/market/:itemId/check-account`** and **POST `/market/:itemId/confirm-buy`**

#### POST `/market/:itemId/reserve`
Reserves account for you. Reserve time - 300 seconds.

Parameters:

 * `price` (__required__) Currenct price of account in your currency


#### POST `/market/:itemId/cancel-reserve`
Cancels reserve.

Parameters:

 * N/A

#### POST `/market/:itemId/check-account`
Checking account for validity. If the account is invalid, the purchase will be canceled automatically (you don't need to make request POST `/market/:itemId/cancel-reserve`

Parameters:

 * N/A

#### POST `/market/:itemId/confirm-buy`
Confirm buy.

Parameters:

 * N/A
