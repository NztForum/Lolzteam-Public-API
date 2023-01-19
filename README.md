# XenForo API eco-system

This repository includes code for:

 * [Forum API documents](https://github.com/grisha2217/Lolzteam-Public-API/blob/master/docs/api.markdown) `docs/api`
 * [Market API documents](https://github.com/grisha2217/Lolzteam-Public-API/blob/master/docs/market_api.markdown) `docs/market_api`


### API Base URIs
`api.zelenka.guru/`

Starting from 1 January, 2023 new Base URIs will be `api.lzt.market`

### Rate limit
20 requests per minute (3 seconds delay between per request)
If you exceed the limit, the response code 429 will be returned to you.
