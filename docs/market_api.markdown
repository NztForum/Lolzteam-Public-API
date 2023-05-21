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
`api.lzt.market`

### Rate limit
20 requests per minute (3 seconds delay between per request)
If you exceed the limit, the response code 429 will be returned to you.

### Search limit
10 requests per minute (6 seconds delay between per request)
If you exceed the limit, you will receive message error

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
      "items": {
        (account)
      },
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
 * `parse_same_items` (_optional_): If yes, API will return account history in results
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
 * `26` `spotify` - Spotify
 * `27` `war-thunder` - War Thunder

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

### POST `/item/add`
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
    
    
### GET `/:itemId/guard-code`
Gets confirmation code from MaFile (Only for Steam accounts)

Response:

    {
        "item": {
        (account)
        },
        "codeData": {
            "code": (sring),
            "date": (unix timestamp in seconds),
        }
    }
    
### GET `/:itemId/mafile`
Returns mafile in JSON. Warning: this action is cancelling active account guarantee.

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


### Categories
* [Steam](#-team)
* [Fortnite](#-ortnite)
* [VKontakte](#-ontakte)
* [Genshin Impact](#-enshin-mpact)
* [Valorant](#-alorant)
* [Telegram](#-elegram)
* [Diamond RP](#-iamond-)
* [Supercell](#-upercell)
* [Origin](#-rigin)
* [World of Tanks](#-orld-of-anks)
* [World of Tanks Blitz](#-orld-of-anks-litz)
* [Epic Games](#-pic-ames)
* [Escape from Tarkov](#-scape-from-arkov)
* [Social Club](#-ocial-lub)
* [Twitter](#-witter)
* [Uplay](#-play)
* [War Thunder](#-ar-hunder)
* [Discord](#-iscord)
* [TikTok](#-ik-ok)
* [Instagram](#-nstagram)
* [Battle.net](#-attle-net)
* [VPN](#-)
* [Streaming media services](#-treaming-media-services)
* [Spotify](#-potify)
* [Warface](#-arface)
* [YouTube](#-ou-ube)
* [Minecraft](#-inecraft)

## Steam
`game[]` (`array`) - List of games

`hours_played` (`array`) - List of minimum hours played by game

`hours_played_max` (`array`) - List of maximum hours played by game

`vac` (`number`) - List of VAC bans by game

`rt` (`boolean`) - Has red sign

`prime` (`boolean`) - Has Prime in CS:GO

`daybreak` (`number`) - Number of days the account has been offline

`limit` (`boolean`) - Has 5$ limit

`mafile` (`boolean`) - Has .mafile

`reg` (`number`) - How old is the account

`reg_period` (`string`) - In what notation is time measured

`lmin` (`number`) - Minimum level

`lmax` (`number`) - Maximum level

`rmin` (`number`) - Minimum rank in CS:GO Matchmaking

`rmax` (`number`) - Maximum rank in CS:GO Matchmaking

`wingman_rmin` (`number`) - Minimum rank in CS:GO Wingman

`wingman_rmax` (`number`) - Maximum rank in CS:GO Wingman

`no_vac` (`boolean`) - Has no VAC ban

`mm_ban` (`boolean`) - Has CS:GO Matchmaking ban

`balance_min` (`number`) - Minimum balance

`balance_max` (`number`) - Maximum balance

`inv_game` (`number`) - Game ID to check inventory price

`inv_min` (`number`) - Minimum inventory price for game

`inv_max` (`number`) - Maximum inventory price for game

`friend_min` (`number`) - Minimum number of friends

`friend_max` (`number`) - Maximum number of friends

`gmin` (`number`) - Minimum number of games

`gmax` (`number`) - Maximum number of games

`win_count_min` (`number`) - Minimum number of wins

`win_count_max` (`number`) - Maximum number of wins

`medal[]` (`array`) - List of medal names

`medal_id[]` (`array`) - List of medal IDs

`medal_min` (`number`) - Minimum number of medals

`medal_max` (`number`) - Maximum number of medals

`gift[]` (`array`) - List of gifts

`gift_min` (`number`) - Minimum number of gifts

`gift_max` (`number`) - Maximum number of gifts

`recently_hours_min` (`number`) - Minimum number of recently played hours

`recently_hours_max` (`number`) - Maximum number of recently played hours

`country[]` (`array`) - List of allowed countries

`not_country[]` (`array`) - List of disallowed countries

`csgo_profile_rank` (`string`) - CS:GO rank (>=)

`csgo_profile_rank_min` (`number`) - Minimum CS:GO rank

`csgo_profile_rank_max` (`number`) - Maximum CS:GO rank

`solommr_min` (`number`) - Minimum number of Dota 2 MMR

`solommr_max` (`number`) - Maximum number of Dota 2 MMR

`d2_game_count_min` (`number`) - Minimum number of Dota 2 games

`d2_game_count_max` (`number`) - Maximum number of Dota 2 games

`d2_win_count_min` (`number`) - Minimum number of Dota 2 wins

`d2_win_count_max` (`number`) - Maximum number of Dota 2 wins

`d2_behavior_min` (`number`) - Minimum number of Dota 2 behavior

`d2_behavior_max` (`number`) - Maximum number of Dota 2 behavior

`faceit_lvl_min` (`number`) - Minimum FACEIT level

`faceit_lvl_max` (`number`) - Maximum FACEIT level

`points_min` (`number`) - Minimum number of Steam points

`points_max` (`number`) - Maximum number of Steam points

`relevant_gmin` (`number`) - Minimum number of relevant games

`relevant_gmax` (`number`) - Maximum number of relevant games

`last_trans_date` (`number`) - How old is last transaction

`last_trans_date_period` (`string`) - In what notation is time measured

`last_trans_date_later` (`number`) - How new is last transaction

`last_trans_date_period_later` (`string`) - In what notation is time measured

`no_trans` (`boolean`) - Has no transactions

`trans` (`boolean`) - Has transactions

## Fortnite
`smin` (`number`) - Minimum number of skins

`smax` (`number`) - Maximum number of skins

`vbmin` (`number`) - Minimum number of V-Bucks

`vbmax` (`number`) - Maximum number of V-Bucks

`skin` (`array`) - Skins

`pickaxe` (`array`) - Pickaxes

`dance` (`array`) - Dances

`glider` (`array`) - Gliders

`change_email` (`boolean`) - Can change email

`platform` (`array`) - Platform

`bp` (`boolean`) - Has Battle Pass

`lmin` (`number`) - Minimum level

`lmax` (`number`) - Maximum level

`bp_lmin` (`number`) - Minimum level of Battle Pass

`bp_lmax` (`number`) - Maximum level of Battle Pass

`rl_purchases` (`boolean`) - Has Rocket League purchases

`last_trans_date` (`number`) - How old is last transaction

`last_trans_date_period` (`string`) - In what notation is time measured

`no_trans` (`boolean`) - Has no transactions

`xbox_linkable` (`boolean`) - Can be linked to Xbox

`psn_linkable` (`boolean`) - Can be linked to PSN

`daybreak` (`number`) - Number of days the account has been offline

`temp_email` (`boolean`) - Access to market temp mail

## VKontakte
`vk_country[]` (`array`) - List of allowed countries

`vk_city[]` (`array`) - List of allowed cities

`vk_friend_min` (`number`) - Minimum number of friends

`vk_friend_max` (`number`) - Maximum number of friends

`vk_follower_min` (`number`) - Minimum number of followers

`vk_follower_max` (`number`) - Maximum number of followers

`vk_vote_min` (`number`) - Minimum number of votes

`vk_vote_max` (`number`) - Maximum number of votes

`sex` (`string`) - Sex of account

`tel` (`boolean`) - Has linked mobile

`email` (`boolean`) - Has linked email

`tfa` (`boolean`) - Has enabled 2FA

`relation[]` (`boolean`) - Has linked mobile

`group_follower_min` (`number`) - Minimum number of group followers

`group_follower_max` (`number`) - Maximum number of group followers

`groups_min` (`number`) - Minimum number of groups

`groups_max` (`number`) - Maximum number of groups

`admin_level` (`string`) - Admin level

`min_age` (`number`) - Minimum age

`max_age` (`number`) - Maximum age

`dig_min` (`number`) - Minimum number of digits in ID

`dig_max` (`number`) - Maximum number of digits in ID

`conversations_min` (`number`) - Minimum number of conversations

`conversations_max` (`number`) - Maximum number of conversations

`reg` (`number`) - How old is the account

`reg_period` (`string`) - In what notation is time measured

`mcountry[]` (`array`) - List of allowed countries of phone number

`not_mcountry[]` (`array`) - List of excluded countries of phone number

`opened_profile` (`boolean`) - Opened account profile

## Genshin Impact
`email` (`boolean`) - Has linked email

`tel` (`boolean`) - Has linked mobile

`character[]` (`string`) - List of characters

`weapon[]` (`string`) - List of characters

`region` (`string`) - Region

`ea` (`boolean`) - Has linked external accounts

`legendary_min` (`number`) - Minimum number of legendary characters

`legendary_max` (`number`) - Maximum number of legendary characters

`constellation_min` (`number`) - Minimum number of constellations on legendary characters

`constellation_max` (`number`) - Maximum number of constellations on legendary characters

`legendary_weapon_min` (`number`) - Minimum number of legendary weapon characters

`legendary_weapon_max` (`number`) - Maximum number of legendary weapon characters

`char_min` (`number`) - Minimum number of characters

`char_max` (`number`) - Maximum number of characters

`level_min` (`number`) - Minimum level

`level_max` (`number`) - Maximum level

## Valorant
`weaponSkin[]` (`array`) - List of weapon skis

`buddy[]` (`array`) - List of buddies

`agent[]` (`array`) - List of agents

`country[]` (`array`) - List of allowed countries

`not_country[]` (`array`) - List of disallowed countries

`daybreak` (`number`) - Number of days the account has been offline

`level_min` (`number`) - Minimum level

`level_max` (`number`) - Maximum level

`vp_min` (`number`) - Minimum number of Valorant points

`vp_max` (`number`) - Maximum number of Valorant points

`smin` (`number`) - Minimum number of skins

`smax` (`number`) - Maximum number of skins

`rmin` (`number`) - Minimum rank (from 3 to 27)

`rmax` (`number`) - Maximum rank

`last_rmin` (`number`) - Last Minimum rank (from 3 to 27)

`last_rmax` (`number`) - Last Maximum rank

`rank_type` (`string`) - Rank type

`amin` (`number`) - Minimum amount of agents

`amax` (`number`) - Maximum amount of agents

`region[]` (`array`) - List of allowed regions

`not_region[]` (`array`) - List of disallowed regions

`email` (`boolean`) - Has linked email

`tel` (`boolean`) - Has linked mobile

`changeable_email` (`boolean`) - Can change email

## Telegram
`scam` (`boolean`) - Has a scam badge

`spam` (`boolean`) - Has a spam ban

`password` (`boolean`) - Has a cloud password

`premium` (`boolean`) - Has a premium subscription

`country[]` (`array`) - List of allowed countries

`not_country[]` (`array`) - List of disallowed countries

`daybreak` (`number`) - Number of days the account has been offline

## Supercell
`system` (`string`) - Account service

`lmin` (`number`) - Minimum level

`lmax` (`number`) - Maximum level

`cup_min` (`number`) - Minimum number of cups

`cup_max` (`number`) - Maximum number of cups

`brawlers_min` (`number`) - Minimum number of brawlers

`brawlers_max` (`number`) - Maximum number of brawlers

`brawler` (`array`) - List of brawlers

## Origin
`game` (`array`) - List of games

`country[]` (`array`) - List of allowed countries

`not_country[]` (`array`) - List of disallowed countries

`al_rank_group[]` (`array`) - List of Apex Legends rank groups

`al_level_min` (`number`) - Minimum level in Apex Legends

`al_level_max` (`number`) - Maximum level in Apex Legends

`xbox_connected` (`boolean`) - Xbox connected to account

`subscription` (`string`) - Name of subscription

`subscription_length` (`number`) - Length of subscription

`subscription_period` (`string`) - In what notation is time measured

## World of Tanks
`tel` (`boolean`) - Has linked mobile

`daybreak` (`number`) - Number of days the account has been offline

`battles_min` (`number`) - Minimum number of battles

`battles_max` (`number`) - Maximum number of battles

`gold_min` (`number`) - Minimum number of gold

`gold_max` (`number`) - Maximum number of gold

`silver_min` (`number`) - Minimum number of silver

`silver_max` (`number`) - Maximum number of silver

`top_min` (`number`) - Minimum number of top tanks

`top_max` (`number`) - Maximum number of top tanks

`prem_min` (`number`) - Minimum number of premium tanks

`prem_max` (`number`) - Maximum number of premium tanks

`top_prem_min` (`number`) - Minimum number of top premium tanks

`top_prem_max` (`number`) - Maximum number of top premium tanks

`win_pmin` (`number`) - Minimum number of wins

`win_pmax` (`number`) - Maximum number of wins

`tank` (`array`) - List of tanks

`region` (`array`) - Region

`not_region` (`array`) - Exclude region

## World of Tanks Blitz
`tel` (`boolean`) - Has linked mobile

`daybreak` (`number`) - Number of days the account has been offline

`battles_min` (`number`) - Minimum number of battles

`battles_max` (`number`) - Maximum number of battles

`gold_min` (`number`) - Minimum number of gold

`gold_max` (`number`) - Maximum number of gold

`silver_min` (`number`) - Minimum number of silver

`silver_max` (`number`) - Maximum number of silver

`top_min` (`number`) - Minimum number of top tanks

`top_max` (`number`) - Maximum number of top tanks

`prem_min` (`number`) - Minimum number of premium tanks

`prem_max` (`number`) - Maximum number of premium tanks

`top_prem_min` (`number`) - Minimum number of top premium tanks

`top_prem_max` (`number`) - Maximum number of top premium tanks

`win_pmin` (`number`) - Minimum number of wins

`win_pmax` (`number`) - Maximum number of wins

`tank` (`array`) - List of tanks

`region` (`array`) - Region

`not_region` (`array`) - Exclude region

## Epic Games
`game` (`array`) - List of games

`change_email` (`boolean`) - You can change email

`rl_purchases` (`boolean`) - Has Rocket League purchases

## Escape from Tarkov
`region` (`string`) - Region

`version[]` (`string`) - List of versions

`sc[]` (`string`) - List of secured containers

`exp_min` (`number`) - Minimum experience

`exp_max` (`number`) - Maximum experience

`level_min` (`number`) - Minimum level

`level_max` (`number`) - Maximum level

## Social Club
`rdr2` (`boolean`) - Has Red Dead Redemption 2

`gtav` (`boolean`) - Has GTA 5

`daybreak` (`number`) - Number of days the account has been offline

## Twitter
`fmin` (`number`) - Minimum number of followers

`fmax` (`number`) - Maximum number of followers

`post_min` (`number`) - Minimum number of posts

`post_max` (`number`) - Maximum number of posts

## Uplay
`game[]` (`array`) - List of games

`country[]` (`array`) - List of allowed countries

`not_country[]` (`array`) - List of disallowed countries

`daybreak` (`number`) - Number of days the account has been offline

`r6_level_min` (`number`) - Minimum level in Tom Clancy's Rainbow Six Siege

`r6_level_max` (`number`) - Maximum level in Tom Clancy's Rainbow Six Siege

## War Thunder
`daybreak` (`number`) - Number of days the account has been offline

`gold_min` (`number`) - Minimum number of gold

`gold_max` (`number`) - Maximum number of gold

`silver_min` (`number`) - Minimum number of silver

`silver_max` (`number`) - Maximum number of silver

`rank_min` (`number`) - Minimal rank

`rank_max` (`number`) - Maximum rank

`eliteUnits_min` (`number`) - Minimum number of elite units

`eliteUnits_max` (`number`) - Maximum number of elite units

`played_min` (`number`) - Minimum number of played games

`played_max` (`number`) - Maximum number of played games

`wins_min` (`number`) - Minimum number of wins

`wins_max` (`number`) - Maximum number of wins

`phone_verified` (`boolean`) - Has verified mobile

`email_verified` (`boolean`) - Has verified email

`premium` (`boolean`) - Has premium

## Discord
`tel` (`boolean`) - Has linked mobile

`nitro` (`boolean`) - Has Nitro

`billing` (`boolean`) - Has billing

`gifts` (`boolean`) - Has gifts

`quarantined` (`boolean`) - Кодер еблан, почему вообще заблокированные акки можно заливать?

`condition[]` (`array`) - List of account conditions

`chat_min` (`number`) - Minimum number of chats

`chat_max` (`number`) - Maximum number of chats

`reg` (`number`) - How old is the account

`reg_period` (`string`) - In what notation is time measured

`locale[]` (`array`) - List of regions

`not_locale[]` (`array`) - List of regions that won't be included

`badge[]` (`array`) - List of badges

## TikTok
`tel` (`boolean`) - Has linked mobile

`fmin` (`number`) - Minimum number of followers

`fmax` (`number`) - Maximum number of followers

`post_min` (`number`) - Minimum number of posts

`post_max` (`number`) - Maximum number of posts

`like_min` (`number`) - Minimum number of likes

`like_max` (`number`) - Maximum number of likes

`coins_min` (`number`) - Minimum number of coins

`coins_max` (`number`) - Maximum number of coins

`tt_country[]` (`array`) - List of allowed countries

`tt_not_country[]` (`array`) - List of disallowed countries

`cookie_login` (`boolean`) - Login by cookies

`verified` (`boolean`) - Has a verified badge

`hasLivePermission` (`boolean`) - Can start a live stream

## Instagram
`tel` (`boolean`) - Has linked mobile

`country[]` (`array`) - List of allowed countries

`not_country[]` (`array`) - List of disallowed countries

`cookies` (`boolean`) - Login by cookies

`login_without_cookies` (`boolean`) - Login without cookies

`fmin` (`number`) - Minimum number of followers

`fmax` (`number`) - Maximum number of followers

`post_min` (`number`) - Minimum number of posts

`post_max` (`number`) - Maximum number of posts

`reg` (`number`) - How old is the account

`reg_period` (`string`) - In what notation is time measured

## Battle.net
`game` (`array`) - List of games

`daybreak` (`number`) - Number of days the account has been offline

`country[]` (`array`) - List of allowed countries

`not_country[]` (`array`) - List of disallowed countries

`edit_btag` (`boolean`) - Can edit BattleTag

`changeable_fn` (`boolean`) - Can edit full name

`real_id` (`boolean`) - Read name

`tel` (`boolean`) - Has linked mobile

`parent_control` (`boolean`) - Has enabled parent control

`cookies` (`boolean`) - Login by cookies

`lmin` (`number`) - Minimum level in Overwatch

`lmax` (`number`) - Maximum level in Overwatch

`balance_min` (`number`) - Minimum balance

`balance_max` (`number`) - Maximum balance

## VPN
`service_id[]` (`array`) - List of allowed VPN services

`subscription_length` (`number`) - Length of subscription

`subscription_period` (`string`) - In what notation is time measured

## Streaming media services
`service_id[]` (`array`) - List of allowed cinema services

`subscription_length` (`number`) - Length of subscription

`subscription_period` (`string`) - In what notation is time measured

`autorenewal` (`boolean`) - Is auto renewal enabled

## Spotify
`country[]` (`array`) - List of allowed countries

`not_country[]` (`array`) - List of disallowed countries

`family` (`boolean`) - Has family subscription

`family_manager` (`boolean`) - Has family manager permissions

`family_member_count_min` (`number`) - Minimum count of members in family

`family_member_count_max` (`number`) - Maximum count of members in family

`subscription_length` (`number`) - Length of subscription

`subscription_period` (`string`) - In what notation is time measured

`recurring` (`boolean`) - Is auto renewal enabled

`trial` (`boolean`) - Trial subscription

`plan_name[]` (`array`) - List of allowed plans

## Warface
`rank_min` (`number`) - Minimum rank

`rank_max` (`number`) - Maximum rank

`bonus_rank_min` (`number`) - Minimum bonus rank

`bonus_rank_max` (`number`) - Maximum bonus rank

`tel` (`boolean`) - Has linked mobile

`daybreak` (`number`) - Number of days the account has been offline

## YouTube
`brand` (`boolean`) - Is brand account

`monetization` (`boolean`) - Has monetization

`artist` (`boolean`) - Has status artist

`verified` (`boolean`) - Has verified

`password` (`boolean`) - Has password

`subscribes_min` (`number`) - Minimum subscribes

`subscribes_max` (`number`) - Maximum subscribes

`viewcount_min` (`number`) - Minimum views count

`viewcount_max` (`number`) - Maximum views count

`videocount_min` (`number`) - Minimum video count

`videocount_max` (`number`) - Maximum video count

`reg` (`number`) - How old is the account

`reg_period` (`string`) - In what notation is time measured

`locale[]` (`array`) - List of regions

`not_locale[]` (`array`) - List of regions that won't be included

## Minecraft
`java` (`boolean`) - Has java edition

`bedrock` (`boolean`) - Has bedrock edition

`change_nickname` (`boolean`) - Can change nickname

`rank_hypixel[]` (`string`) - Rank on hypixel

`level_hypixel_min` (`number`) - Minimum number of level hypixel

`level_hypixel_max` (`number`) - Maximum number of level hypixel

`achievement_hypixel_min` (`number`) - Minimum number of achievement hypixel

`achievement_hypixel_max` (`number`) - Maximum number of achievement hypixel

`reg` (`number`) - How old is the account

`reg_period` (`string`) - In what notation is time measured

`last_login_hypixel` (`number`) - How old is the last login account

`last_login_hypixel_period` (`string`) - In what notation is time measured
