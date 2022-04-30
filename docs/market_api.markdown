# Market API Documents

## Authorization
You can read about authorization, scopes and grant types here - [Forum API documents](https://github.com/grisha2217/Lolzteam-Public-API/blob/master/docs/api.markdown) `docs/api`
For using this api you need to get Access Token with **read+post+market** scope


## API free libraries
- [Python](https://github.com/NztForum/node-lzt) 
- [Node.js](https://github.com/xALEGORx/LolzApi/)

## About Market API
Market API almost completely repeats WEB requests. Query parameters match. The only differences are the presence of PUT and DELETE methods (more on that below).
For example, a request to book an account on the WEB looks like this: `lolz.guru/market/:itemId/reserve?price=:accountPrice`, and an API request looks like this: `api.lolz.guru/market/:itemId/reserve?price=accountPrice`.

### API Base URI
api.lolz.guru/

### Rate limit
20 requests per minute


## Accounts list
### GET `/market`
Displays a list of latest accounts

Parameters:

 * N/A


### Category list:
 * `1` - Steam
 * `2` - VK
 * `3` - Origin
 * `4` - Warface
 * `5` - Uplay
 * `7` - Social Club
 * `9` - Fortnite
 * `12` - Epic Games
 * `10` - Instagram
 * `11` - BattleNet
 * `14` - World Of Tanks
 * `16` - World Of Tanks Blitz
 * `15` - Supercell
 * `17` - Genshin Impact
 * `18` - Tarkov
 * `19` - VPN
 * `20` - TikTok
 * `22` - Discord

### GET `/market/:categoryName`
Displays a list of accounts in a specific category according to your parameters

Parameters:

 * `pmin` (_optional_): Minimal price of account (Inclusive)
 * `pmax` (_optional_): Maximum price of account (Inclusive)
 * `title` (_optional_): The word or words contained in the account title
 * `parse_sticky_items` (_optional_): If yes, API will return stickied accounts in results
 * `Optional category parameters` (_optional_): You can find it using "Inspect code element" in your browser

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

### GET `/market/fave`
Displays a list of favourites accounts

Parameters:
 * N/A

### GET `/market/viewed`
Displays a list of viewed accounts

Parameters:
 * N/A

### GET `/market/:itemId`
Displays account information

Parameters:

 * N/A

## Account purchasing
You need to make 3 requests:
POST `/market/:itemId/reserve`, POST `/market/:itemId/check-account` and POST `/market/:itemId/confirm-buy`

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


## Money transfers and payments list

### POST `/market/balance/transfer/`
Send money to any user.

Parameters:

 * `user_id` (__required__) User id of receiver. If `user_id` specified, `username` is not required.
 * `username` (__required__) Username of receiver. If `username` specified, `user_id` is not required.
 * `amount` (__required__) Amount to send in your currency.
 * `currency` (__required__) Using currency for amount. Allowed values: `cny` `usd` `rub` `eur` `uah` `kzt` `byn` `gbp`
 * `secret_answer` (__required__) Secret answer of your account
* `comment` (_optional_) Transfer comment. Maximum 255 characters
 * `transfer_hold` (_optional_) (Boolean) Hold transfer or not.
 * `hold_length_value` (_optional_) Hold length value (number).
 * `hold_length_option` (_optional_) Hold length option (string). Allowed values: `hour` `day` `week` `month` `year`

#### Hold parameters examples
E.g. you want to hold money transfer on 3 days. `hold_length_value` - will be '3', `hold_length_option` - will be 'days'.

E.g. you want to hold money transfer on 12 hours.
`hold_length_value` - will be '12', `hold_length_option` - will be 'hours'

### GET `/market/user/:userId/payments`
Displays list of your payments
 * `type` (_optional_): Type of operation. Allowed operation types: `income` `cost` `refilled_balance` `withdrawal_balance` `paid_item` `sold_item` `money_transfer` `receiving_money` `internal_purchase` `claim_hold`
 * `pmin` (_optional_): Minimal price of operation (Inclusive)
 * `pmax` (_optional_): Maximum price of operation (Inclusive)
 * `receiver` (_optional_): Username of user, which receive money from you
 * `sender` (_optional_): Username of user, which sent money to you
 * `startDate` (_optional_): Start date of operation (RFC 3339 date format)
 * `endDate` (_optional_): End date of operation (RFC 3339 date format)
 * `wallet` (_optional_): Wallet, which used for money payots
 * `comment` (_optional_): Comment for money transfers
 * `is_hold` (_optional_): Display hold operations


## Account publishing
You need to make 2 requests:
POST `/market/item/add` and POST `/market/:itemId/goods/check`

### POST `/market/item/add/`
Adds account on the market. After this request an account will have `item_state = awaiting` (not displayed in search)

Parameters:

 * `title` (__required__) Russian title of account. If `title` specified and `title_en` is empty, `title_en` will be automatically translated to English language.
 * `title_en` (_optional_) English title of account. If `title_en` specified and `title` is empty, `title` will be automatically translated to Russian language.
 * `price` (__required__) Account price in your currency
 * `category_id` (__required__) Category id
 * `currency` (__required__) Using currency. Allowed values: `cny` `usd` `rub` `eur` `uah` `kzt` `byn` `gbp`
 * `item_origin` (__required__) Item origin
 * `extended_guarantee` (__required__) Guarantee type. Allowed values: `-1` - 12 hours, `0` - 24 hours, `1` - 3 days.
 * `description` (_optional_) Account public description
 * `information` (_optional_) Account private information (visible for buyer only if purchased)
 * `has_email_login_data` __Required__ if a category is one of list of __Required email login data categories__ (see below)
 * `email_login_data` __Required___ if a category is one of list of __Required email login data categories__ (see below) . Email login data (login:pass format) 
 * `email_type` (_optional_) Email type. Allowed values: `native` `autoreg`
 * `allow_ask_discount` (_optional_) Allow users to ask discount for this account
 * `proxy_id` (_optional_) Using proxy id for account checking. See [Proxy Settings](#proxy-settings) to get or edit proxy list


#### Item origin
Account origin. Where did you get it from.
* `brute` - Account received using Bruteforce
* `fishing` - Account received from fishing page
* `stealer` - Account received from stealer logs
* `autoreg` - Account is automatically registered by a tool
* `personal` - Account is yours. You created it yourself.
* `resale` - Account received from another seller
* `retrive` - Account is recovered by email or phone (only for VKontakte category)

#### Required email login data categories
- Fortnite (id 9)
- Epic games (id 12)
- Tarkov (id 18)

### POST `/market/:itemId/goods/check`
Check account on validity. If account is valid, account will be published on the market.

Parameters:
 * `login` (_optional_) Account login (or email)
 * `password` (_optional_) Account password
 * `login_password` (_optional_) Account login data format login:password
 * `close_item` (_optional_) If set, the item will be closed `item_state = closed`
 * `extra` (_optional_) (Array) Extra params for account checking. E.g. you need to put cookies to `extra[cookies]` if you want to upload Fortnite/Epic Games account

### GET `/market/сategory`
Display category list

Parameters:
 * `top_queries` (_optional_) (Boolean) Display top queries for per category

## Accounts managing

### GET `/market/:itemId/email-code/`
Gets confirmation code or link.

Parameters:
 * `email` (__required__) Account email

### POST `/market/:itemId/change-password`
Changes password of account.

Parameters:
 * `_cancel` (_optional_) Cancel change password recommendation. It will be helpful, if you don't want to change password and get login data

### PUT `/market/:itemId/edit/`
Edits any details of account.

Parameters:
* `key` (_optional) Key to edit (key list you can see below). E.g. price.
* `value` (_optional) Value to edit
* `key_values` (_optional_) Key-values to edit (Array). E.g. key_values[title]=Account&key_values[price]=100
* `currency` (__required__) Currency of amount. Required if you are trying to change `amount` field. Allowed values: `cny` `usd` `rub` `eur` `uah` `kzt` `byn` `gbp`

#### Key list:
 * `title` (_optional_) Russian title of account. If `title` specified and `title_en` is empty, `title_en` will be automatically translated to English language.
 * `title_en` (_optional_) English title of account. If `title_en` specified and `title` is empty, `title` will be automatically translated to Russian language.
 * `price` (_optional_) Account price in your currency. Allowed values: `cny` `usd` `rub` `eur` `uah` `kzt` `byn` `gbp`
 * `item_origin` (_optional_) Item origin
 * `description` (_optional_) Account public description
 * `information` (_optional_) Account private information (visible for buyer only if purchased)
 * `has_email_login_data` (_optional_) Set boolean, if you have email login data
 * `email_login_data` (_optional_) Email login data (login:pass format) 
 * `email_type` (_optional_) Email type. Allowed values: `native`, `autoreg`
 * `allow_ask_discount` (_optional_) Allow users to ask discount for this account
 * `proxy_id` (_optional_) Using proxy id for account checking. See [GET /account/market](#account/market) to get or edit proxy list

### DELETE `/market/:itemId/delete/`
Deletes your account from public search. Deletetion type is soft. You can restore account after deletetion if you want. 

Parameters:
* `reason` (__requred__) Delete reason


### POST `/market/:itemId/tag/`
Adds tag for the account

Parameters:
 * `tag_id` (__required__) Tag id (Tag list is available via GET `/market/me`)


### DELETE `/market/:itemId/tag/`
Deletes tag for the account

Parameters:
 * `tag_id` (__required__) Tag id


## Market profile settings

### GET `/market/me`
Displays info about your profile

Parameters:

 * N/A

### PUT `/market/me`
Change settings about your profile on the market

Parameters:

 * `disable_steam_guard` (_optional_) (Boolean) Disable Steam Guard on account purchase moment
 * `user_allow_ask_discount` (_optional_) (Boolean) Allow users ask discount for your accounts
 * `max_discount_percent` (_optional_) (UInt) Maximum discount percents for your accounts
 * `allow_accept_accounts` (_optional_) (String) Usernames who can transfer market accounts to you. Separate values with a comma.
 * `hide_favourites` (_optional_) (Boolean) Hide your profile info when you add an account to favorites


### Proxy settings
#### GET `/market/proxy`
Gets your proxy list

Parameters:

 * N/A

#### POST `/market/proxy`
Add single proxy or proxy list

__To add single proxy use this parameters__:
 * `proxy_ip` (__required__) Proxy ip or host
 * `proxy_port` (__required__) Proxy port
 * `proxy_user` (_optional_) Proxy username
 * `proxy_pass` (_optional_) Proxy password

__To add proxy list use this parameters__:
* `proxy_row` (__required__) Proxy list in String format ip:port:user:pass. Each proxy must be start with new line (use \r\n separator)


#### DELETE `/market/proxy`
Delete single or all proxies

Parameters:
 * `proxy_id` (_optional_) Proxy id
 * `delete_all` (_optional_) Set boolean if you want to delete all proxy
