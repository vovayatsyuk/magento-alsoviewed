Magento Also Viewed Products
============================

Magento module that displays products, viewed by visitors, who viewed this
product. It can also show recommendations based on viewed, compared and shopping
cart products (see [widget](#widget) section).

![Also Viewed products in RWD tabs][rwd_tabs]

Please read the [wiki](https://github.com/vovayatsyuk/magento-alsoviewed/wiki)
if you are searching for installation or integration instructions.

#### How it works?
Every time the client is looking at some product, module update relations between
this and other viewed products. Day after day relations count are grows and they
become more accurate.

#### Can I control the relations?
Yes, store administrator can manually create, change relation sort order or even
disable relation to hide it from the frontend.

#### Configuration

![Configuration screenshot][configuration]

Module configuration provides ability to show Also Viewed Products in
one of the following containers:

- Left or Right column
- Product additional information

> Please note, that your theme is responsible to output the required container.

Each of the sections has options that allows to configure title, list mode,
products count, columns count (in case of grid mode) and image dimensions.

- Perfomance

    Allows to improve cron process stability by setting `Relations count to
    process per one query`. You may need to decrease this value, if you found
    the "Got a packet bigger than 'max_allowed_packet' bytes" error in Magento
    log.

- Log settings

    Allows to make module much more accurate by ignoring search crawlers and bots
    activity.

    You may also ignore specified IP addresses.

#### Widget

![Widget screenshot][widget]

Widget provides ability to recommend products on any page by additional activities:

- Recently Viewed Products
- Recently Compared Products
- Shopping Cart products

#### The main features
- 100% free and open source
- High perfomance
- Simple and clean design
- Responsive css

> Module functionality relies on cron. Make sure you configure it properly.

[rwd_tabs]: https://raw.githubusercontent.com/vovayatsyuk/magento-alsoviewed/gh-pages/images/screenshots/product-page-rwd-tabs-short.png  "Also Viewed products in RWD tabs"
[configuration]: https://raw.githubusercontent.com/vovayatsyuk/magento-alsoviewed/gh-pages/images/screenshots/configuration-short.png
[widget]: https://cloud.githubusercontent.com/assets/306080/7053529/87ad926c-de3e-11e4-846a-bd65507a5c83.png
[product_edit_page]: https://raw.githubusercontent.com/vovayatsyuk/magento-alsoviewed/gh-pages/images/screenshots/product-edit-short.png
[relations_grid]: https://raw.githubusercontent.com/vovayatsyuk/magento-alsoviewed/gh-pages/images/screenshots/relations-grid-short.png
