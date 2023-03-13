# Market API Documents

## Authorization
You can read about authorization, scopes and grant types here - [Forum API documents](https://github.com/grisha2217/Lolzteam-Public-API/blob/master/docs/api.markdown) `docs/api`
For using this api you need to get Access Token with **read+post+market** scope


## API free libraries
- [Node.js](https://github.com/NztForum/node-lzt) 
- [Python](https://github.com/xALEGORx/LolzApi/)
- [C#](https://github.com/fanidamn/LolzMarketAPI)

## About Market API
Market API almost completely repeats WEB requests. Query parameters match. The only differences are the presence of PUT and DELETE methods (more on that below).
For example, a request to book an account on the WEB looks like this: `zelenka.guru/market/:itemId/reserve?price=:accountPrice`, and an API request looks like this: `api.lzt.market/:itemId/reserve?price=accountPrice`.

### API Base URIs
`api.lzt.market/`

### Rate limit
20 requests per minute (3 seconds delay between per request)
If you exceed the limit, the response code 429 will be returned to you.

## Response Example
    {
      item: {
          "item_id": (int),
          "item_state": (string),
          "published_date": (unix timestamp in seconds),
          "title": (string),
          "description": (string),
          "price": (int),
          ...
          },
      "seller": {
          "user_id": (int),
          "username": (string),
          "avatar_date": (unix timestamp in seconds),
          "user_group_id": (int),
          "secondary_group_ids": (string),
          "display_style_group_id": (int),
          "uniq_username_css": (string)
          }
    }
### Success

    {
        "status": "ok",
        "message": "Changes Saved",
        "system_info": {
            "visitor_id": (int),
            "time": (unix timestamp in seconds)
        }
    }

## Accounts list
### GET `/`
Displays a list of latest accounts
    
Parameters:

 * N/A

Response:

    {
      "items": [
        (account)
      ],
      "totalItems": (int),
      "totalItemsPrice": (int),
      "perPage": (int),
      "page": (int),
      "searchUrl": (string),
    }


### GET `/:categoryName`
Displays a list of accounts in a specific category according to your parameters

Parameters:

 * `pmin` (_optional_): Minimal price of account (Inclusive)
 * `pmax` (_optional_): Maximum price of account (Inclusive)
 * `title` (_optional_): The word or words contained in the account title
 * `parse_sticky_items` (_optional_): If yes, API will return stickied accounts in results
 * `Optional category parameters` (_optional_): You can find it using "Inspect code element" in your browser [or in WEB url](#about-market-api)
 * `game[]` (_optional_): The ID of a game found on the account
 * `page` (_optional_): The number of the page to display results from

### Category id-names list:
 * `1` `steam` - Steam
 * `2` `vkontakte` - VK
 * `3` `origin` - Origin
 * `4` `warface` - Warface
 * `5` `uplay` - Uplay
 * `7` `socialclub` - Social Club
 * `9` `fortnite` - Fortnite
 * `10` `instagram` - Instagram
 * `11` `battlenet` - Battle.net
 * `12` `epicgames` - Epic Games
 * `13` `valorant` - Valorant
 * `14` `world-of-tanks` - World Of Tanks
 * `16` `wot-blitz` - World Of Tanks Blitz
 * `15` `supercell` - Supercell
 * `17` `genshin-impact` - Genshin Impact
 * `18` `escape-from-tarkov` - Escape From Tarkov
 * `19` `vpn` - VPN
 * `20` `tiktok` - TikTok
 * `22` `discord` - Discord
 * `23` `cinema` - Online Cinema
 * `24` `telegram` - Telegram
 * `25` `youtube` - YouTube

### GET `/user/:userId/items`
Displays a list of owned accounts

Parameters:
 * `category_id` (_optional_): Accounts category
 * `pmin` (_optional_): Minimal price of account (Inclusive)
 * `pmax` (_optional_): Maximum price of account (Inclusive)
 * `title` (_optional_): The word or words contained in the account title
 * `Optional category parametes` (_optional_): You can find it using "Inspect code element" in your browser [or in WEB url](#about-market-api)

### GET `/user/:userId/orders`
Displays a list of purchased accounts

Parameters:
 * `category_id` (_optional_): Accounts category
 * `pmin` (_optional_): Minimal price of account (Inclusive)
 * `pmax` (_optional_): Maximum price of account (Inclusive)
 * `title` (_optional_): The word or words contained in the account title
 * `Optional category parametes` (_optional_): You can find it using "Inspect code element" in your browser [or in WEB url](#about-market-api)

### GET `/fave`
Displays a list of favourites accounts

Parameters:
 * N/A

### GET `/viewed`
Displays a list of viewed accounts

Parameters:
 * N/A

### GET `/:itemId`
Displays account information

Parameters:

 * N/A

Response:
    
    {
      item: {
          "item_id": (int),
          "item_state": (string),
          "published_date": (unix timestamp in seconds),
          "title": (string),
          "description": (string),
          "price": (int),
          "update_stat_date": (unix timestamp in seconds),
          "refreshed_date": (unix timestamp in seconds),
          "login": (string),
          "temp_email": (string),
          "view_count": (int),
          "information": (string),
          "item_origin": (string),
          ...
          },
      "seller": {
          "user_id": (int),
          "username": (string),
          "avatar_date": (unix timestamp in seconds),
          "user_group_id": (int),
          "secondary_group_ids": (string),
          "display_style_group_id": (int),
          "uniq_username_css": (string)
          }
    }
    
### GET `/:itemId/steam-preview`
Returns Steam account html code

Parameters:

 * `type` (_optional_): Type of page - `profile` or `games`
    
    
### GET `/:categoryName/params`
Displays search parameters for a category

Parameters:

 * N/A

Response:

    {
        "category": {
            "category_id": (int),
            "sub_category_id": (int),
            "category_order": (int),
            "category_title": (string),
            "category_name": (string),
            "category_url": (string),
            ...
        },
        "params": [
            {
                "name": (string),
                "input": (string),
                "description": (string),
                "values": [
                    (string)
                    ...
                ]
            }
            ...
        ]
    }

### GET `/:categoryName/games`
Displays a list of games in the category

Parameters:

 * N/A

Response:

    {
        "games": [
            {
                "app_id": (string),
                "title": (string),
                "category_id": (int),
                "img": (string),
                "ru": [
                    (string)
                    ...
                ],
                "url": (string)
            }
            ...
        ]
    }



## Account purchasing
First way:
#### POST `/:itemId/fast-buy`
Check and buy account.

Parameters:

 * `price` (__required__) Currenct price of account in your currency
 * `buy_without_validation` Put `1` if you want to buy account without account data validation (not safe)

Second way:
You need to make 3 requests:
POST [`/:itemId/reserve`](#post-marketitemidreserve), POST [`/:itemId/check-account`](#post-marketitemidcheck-account) and POST [`/:itemId/confirm-buy`](#post-marketitemidconfirm-buy)

#### POST `/:itemId/reserve`
Reserves account for you. Reserve time - 300 seconds.

Parameters:

 * `price` (__required__) Currenct price of account in your currency
 
Response:

    {
      "status": "ok",
      "reserve_end_date": (unix timestamp in seconds),
      "item": {
          (account)
      },
      "system_info": {
          "visitor_id": (int),
          "time": (unix timestamp in seconds)
      }
    }



#### POST `/:itemId/cancel-reserve`
Cancels reserve.

Parameters:

 * N/A

Response:

    {
        status: "ok",
        message: "Changes Saved"
    }


#### POST `/:itemId/check-account`
Checking account for validity. If the account is invalid, the purchase will be canceled automatically (you don't need to make request POST `/:itemId/cancel-reserve`

Parameters:

 * N/A
 
 Response:
 
    {
      "status": "ok",
      "item": {
          (account)
      },
      "system_info": {
          "visitor_id": (int),
          "time": (unix timestamp in seconds)
      }
    }


#### POST `/:itemId/confirm-buy`
Confirm buy.

Parameters:
 * `buy_without_validation` Put `1` if you want to buy account without account data validation (not safe)

 Response:
 
    {
      "status": "ok",
      "reserve_end_date": (unix timestamp in seconds),
      "item": {
          "loginData": {
            "raw": (string),
            "encodedRaw": (string),
            "login": (string),
            "password": (string),
            "encodedPassword": (string),
            "oldPassword": (string),
            "encodedOldPassword": (string),
            "adviceToChangePassword": (boolean)
        },
        ...
      },
      "system_info": {
          "visitor_id": (int),
          "time": (unix timestamp in seconds)
      }
    }



## Money transfers and payments list

### POST `/balance/transfer`
Send money to any user

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

Response:

    {
        "status": (string),
        "message": (string),
        "system_info": {
            "visitor_id": (int),
            "time": (unix timestamp in seconds)
        }
    }

### GET `/user/:userId/payments`
Displays list of your payments

Parameters:
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
 * `show_payment_stats` (_optional_): Display payment stats for selected period (outgoing value, incoming value)

Response:

    {
        "payments": {
        (payment)
        },
        "hasNextPage": (boolean),
        "lastOperationId": (int),
        "nextPageHref": (string),
        "system_info": {
            "visitor_id": (int),
            "time": (unix timestamp in seconds)
        }
    }

## Account publishing
You need to make 2 requests:
POST `/item/add` and POST `/:itemId/goods/check`
For categories, which required temporary email (Steam, Social Club) you need to make GET `/:itemId/goods/add` to get temporary email 

### POST `/item/add/`
Adds account on the market. After this request an account will have `item_state = awaiting` (not displayed in search)

Parameters:

 * `title` (__required__) Russian title of account. If `title` specified and `title_en` is empty, `title_en` will be automatically translated to English language.
 * `title_en` (_optional_) English title of account. If `title_en` specified and `title` is empty, `title` will be automatically translated to Russian language.
 * `price` (__required__) Account price in your currency
 * `category_id` (__required__) [Category id](#category-id-names-list)
 * `currency` (__required__) Using currency. Allowed values: `cny` `usd` `rub` `eur` `uah` `kzt` `byn` `gbp`
 * `item_origin` (__required__) [Item origin](#item-origin)
 * `extended_guarantee` (__required__) Guarantee type. Allowed values: `-1` - 12 hours, `0` - 24 hours, `1` - 3 days.
 * `description` (_optional_) Account public description
 * `information` (_optional_) Account private information (visible for buyer only if purchased)
 * `has_email_login_data` __Required__ if a category is one of list of __Required email login data categories__ (see below)
 * `email_login_data` __Required___ if a category is one of list of __Required email login data categories__ (see below) . Email login data (login:pass format) 
 * `email_type` (_optional_) Email type. Allowed values: `native` `autoreg`
 * `allow_ask_discount` (_optional_) Allow users to ask discount for this account
 * `proxy_id` (_optional_) Using proxy id for account checking. See [Proxy Settings](#proxy-settings) to get or edit proxy list
 * `random_proxy` (_optional_) Pass 1, if you get "steam_captcha" in previous response



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
- Escape from Tarkov (id 18)

Response:

        {
            "status": "ok",
            "item": {
                "item_id": (int),
                "item_state": "awaiting",
                ...
            },
            "system_info": {
                "visitor_id": (int),
                "time": (unix timestamp in seconds)
            }
        }


### GET `/:itemId/goods/add`
Get info about not published item. For categories, which required temporary email (Steam, Social Club), you will get temporary email in response.

Parameters:
 * `resell_item_id` (_optional_) Put item id, if you are trying to resell item. This is useful to pass temporary email from reselling item to new item. You will get same temporary email from reselling account.

Response:

        {
            "status": "ok",
            "item": {
                "item_id": (int),
                "item_state": "awaiting",
                ...
            },
            "system_info": {
                "visitor_id": (int),
                "time": (unix timestamp in seconds)
            "temp_email": (string),
            "sessionLoginData": [],
            "ignoreCookieUpload": (boolean),

            }
        }

### POST `/:itemId/goods/check`
Check account on validity. If account is valid, account will be published on the market.

Parameters:
 * `login` (_optional_) Account login (or email)
 * `password` (_optional_) Account password
 * `login_password` (_optional_) Account login data format login:password
 * `close_item` (_optional_) If set, the item will be closed `item_state = closed`
 * `extra` (_optional_) (Array) Extra params for account checking. E.g. you need to put cookies to `extra[cookies]` if you want to upload Fortnite/Epic Games account
 * `resell_item_id` Put if you are trying to resell an account.
 * `random_proxy` (_optional_) Pass 1, if you get "steam_captcha" in previous response

Response:

    {
        "status": (string),
        "message": (string),
        "system_info": {
            "visitor_id": (int),
            "time": (unix timestamp in seconds)
        }
    }

### GET `/сategory`
Display category list

Parameters:
 * `top_queries` (_optional_) (Boolean) Display top queries for per category

Response:

    {
        "category_id": (int),
        "sub_category_id": (int),
        "category_order": (int),
        "category_title": (string),
        "category_name": (string),
        "category_url": (string),
        "category_description_html": (string),
        "category_login_url": (string),
        ...
    }
    ...

## Accounts managing

### GET `/:itemId/email-code`
Gets confirmation code or link.

Parameters:
 * `email` (__required__) Account email

Response:

    {
        "item": {
        (account)
        },
        "codeData": {
            "code": (sring),
            "date": (unix timestamp in seconds),
            "textPlain": (string)
        }
    }
    
### GET `/:itemId/mafile`
Returns mafile in JSON.

Response:

    {
        "maFile": {
        }
    }
 


### POST `/:itemId/refuse-guarantee`
Cancel guarantee of account. It can be useful for account reselling.

Parameters:
 * N/A

Response:

    {
        "status": "ok",
        "message": "Changes Saved"
    }

### POST `/:itemId/change-password`
Changes password of account.

Parameters:
 * `_cancel` (_optional_) Cancel change password recommendation. It will be helpful, if you don't want to change password and get login data

Response:

    {
        "status": "ok",
        "message": "Changes Saved"
        "new_password": (string)
    }
    
    
### GET `/:itemId/temp-email-password`
Gets password from temp email of account. After calling of this method, the warranty will be cancelled and you cannot automatically resell account.

Parameters:
 * N/A

Response:

    {
        "item": {
        (account)
        },
    }

### POST `/:itemId/change-password`
Changes password of account.

Parameters:
 * `_cancel` (_optional_) Cancel change password recommendation. It will be helpful, if you don't want to change password and get login data

Response:

    {
        "status": "ok",
        "message": "Changes Saved"
        "new_password": (string)
    }   

### PUT `/:itemId/edit`
Edits any details of account.

Parameters:
* `key` (_optional) Key to edit (key list you can see below). E.g. price.
* `value` (_optional) Value to edit
* `key_values` (_optional_) Key-values to edit (Array). E.g. key_values[title]=Account&key_values[price]=100
* `currency` (__required__) Currency of account price. Required if you are trying to change `price` field. Allowed values: `cny` `usd` `rub` `eur` `uah` `kzt` `byn` `gbp`

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

Response:

    {
        "status": "ok",
        "message": "Changes Saved"
    }

### DELETE `/:itemId`
Deletes your account from public search. Deletetion type is soft. You can restore account after deletetion if you want. 

Parameters:
* `reason` (__requred__) Delete reason

Response:

    {
        "status": "ok",
        "message": (string),
        "system_info": {
            "visitor_id": (int),
            "time": (unix timestamp in seconds)
        }
    }

### POST `/:itemId/tag`
Adds tag for the account

Parameters:
 * `tag_id` (__required__) Tag id (Tag list is available via GET `/me`)

Response:

    {
        "itemId": (int),
        "tag": {
            "tag_id": (int),
            "title": (string),
            "isDefault": (boolean),
            "forOwnedAccountsOnly": (boolean),
            "bc": (string)
        },
        "addedTagId": (int),
        "deleteTags": [
            (int)
        ],
        "system_info": {
            "visitor_id": (int),
            "time": (unix timestamp in seconds)
        }
    }

### DELETE `/:itemId/tag`
Deletes tag for the account

Parameters:
 * `tag_id` (__required__) Tag id

Response:

    {
        "itemId": (int),
        "deleteTags": [
            (int)
        ],
        "system_info": {
            "visitor_id": (int),
            "time": (unix timestamp in seconds)
        }
    }

### POST `/:itemId/bump`
Bumps account in the search

Parameters:
 * N/A

Response:

    {
        "status": "ok",
        "message": (string),
        "system_info": {
            "visitor_id": (int),
            "time": (unix timestamp in seconds)
        }
    }

### POST `/:itemId/star`
Adds account from favourites

Parameters:
 * N/A

Response:

    {
        "status": "ok",
        "message": (string),
        "system_info": {
            "visitor_id": (int),
            "time": (unix timestamp in seconds)
        }
    }

### DELETE `/:itemId/star`
Deletes account from favourites

Parameters:
 * N/A

Response:

    {
        "status": "ok",
        "message": "Changes Saved"
    }

### POST `/:itemId/stick`
Stick account in the top of search

Parameters:
 * N/A

Response:

    {
        "status": "ok",
        "message": (string)
    }

### DELETE `/:itemId/stick`
Unstick account of the top of search

Parameters:
 * N/A

Response:


    {
        "status": "ok",
        "message": "Changes Saved"
    }

### POST `/:itemId/change-owner`
Change of account owner

Parameters:
 * `username` (__required__) The username of the new account owner
 * `secret_answer` (__required__) Secret answer of your account

Response:


    {
        "status": "ok",
        "message": "Changes Saved"
    }

## Market profile settings

### GET `/me`
Displays info about your profile

Parameters:

 * N/A

Response:

    {
            "user": {
                "user_id": (int),
                "username": (string),
                "user_message_count": (int),
                "user_register_date": (unix timestamp in seconds),
                "user_like_count": (int),
                "short_link": (string),
                "user_email": (string),
                "user_unread_notification_count": (int),
                "user_dob_day": (int),
                "user_dob_month": (int),
                "user_dob_year": (int),
                "user_title": (string),
                "user_last_seen_date": (unix timestamp in seconds),
                "balance": (int),
                "hold": (int),
                ...
                "system_info": {
                    "visitor_id": (int),
                    "time": (unix timestamp in seconds)
                ...
            }
        }

### PUT `/me`
Change settings about your profile on the market

Parameters:

 * `disable_steam_guard` (_optional_) (Boolean) Disable Steam Guard on account purchase moment
 * `user_allow_ask_discount` (_optional_) (Boolean) Allow users ask discount for your accounts
 * `max_discount_percent` (_optional_) (UInt) Maximum discount percents for your accounts
 * `allow_accept_accounts` (_optional_) (String) Usernames who can transfer market accounts to you. Separate values with a comma.
 * `hide_favourites` (_optional_) (Boolean) Hide your profile info when you add an account to favorites
 
 Response:
 
     {
        "status": "ok",
        "message": "Changes Saved"
    }

 

### Proxy settings
#### GET `/proxy`
Gets your proxy list

Parameters:

 * N/A

Response:

    {
        "proxies": {
            (string): {
                "proxy_id": (int),
                "user_id": (int),
                "proxy_ip": (string),
                "proxy_port": (int),
                "proxy_user": (string),
                "proxy_pass": (string),
                "proxyString": (string)
            },
        "system_info": {
            "visitor_id": (int),
            "time": (unix timestamp in seconds)
        }
    }

#### POST `/proxy`
Add single proxy or proxy list

__To add single proxy use this parameters__:
 * `proxy_ip` (__required__) Proxy ip or host
 * `proxy_port` (__required__) Proxy port
 * `proxy_user` (_optional_) Proxy username
 * `proxy_pass` (_optional_) Proxy password

__To add proxy list use this parameters__:
* `proxy_row` (__required__) Proxy list in String format ip:port:user:pass. Each proxy must be start with new line (use \r\n separator)

Response:

    {
        "status": "ok",
        "message": "Changes Saved",
    }

#### DELETE `/proxy`
Delete single or all proxies

Parameters:
 * `proxy_id` (_optional_) Proxy id
 * `delete_all` (_optional_) Set boolean if you want to delete all proxy

Response:

    {
        "status": "ok",
        "message": "Changes Saved",
    }
