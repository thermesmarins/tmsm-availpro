TMSM Availpro
=================

Display Availpro daily prices (best price) in a calendar view (requires an Availpro API access).
Or simply display the best price overall, of a room or of a rate.

Features
-----------

* Shortcode `[tmsm-availpro-calendar]` to display calendar
* Shortcode `[tmsm-availpro-bestprice-year]` to display best year overall price, since 1.0.7
* Shortcode `[tmsm-availpro-bestprice-year roomid="xxx"]` to display best year price of a room, since 1.0.8
* Shortcode `[tmsm-availpro-bestprice-year rateid="xxx"]` to display best year price of a room, since 1.0.8
* Prices are cached in wp_options
* Hourly cron refreshes prices
* User can select begin date and end date in the calendar, this calculates the total price of the stay
* Compare price with OTA
* Customizer: customize calendar colors
* Polylang/WPML compatibility for admin texts
* Compatibility with themes:
    * StormBringer
    * OceanWP