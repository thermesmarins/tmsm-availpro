### 2.0.2: March 26th, 2025
* Fix minimum stay variable didn't work

### 2.0.1: August 12th,2024
* Fix best-year-price shortcode not working

### 2.0: August 12th,2024
* Update to the new version of API availpro

### 1.2.9: June 6th, 2024
* comment line 202 in tmsm-availpro-public.css file, to not show the green color on the price
  
### 1.2.8: August 30th, 2022
* Oauth library default $code variable if it is not found

### 1.2.7: August 25th, 2022
* Update moment library to 2.29.4

### 1.2.6: August 25th, 2022
* Fix money_format function is deprecated

### 1.2.5: August 25th, 2022
* Fix "Array and string offset access syntax with curly braces is deprecated" depreciation in oauth library

### 1.2.4: April 11th, 2022
* Update Moment to 2.29.2

### 1.2.3: December 28th, 2021
* WP Rocket: exclude inline JS from minification

### 1.2.2: September 14th, 2021
* Fix minified public javascript causing javascript errors

### 1.2.1: June 29th, 2021
* Allow use of shortcode in Elementor Popup

### 1.2.0: January 30th, 2020
* Make use of shortcode [tmsm-availpro-bestprice-year] possible even if no calendar is set

### 1.1.9: January 6th, 2020
* **Tweak** - Better click zone for next/prev month arrow buttons

### 1.1.8: December 5th, 2019
* **New** - Health Check for cron schedule
* **New** - Notify admin if cron is not scheduled

### 1.1.7: June 13th, 2019
* **Tweak** - Deregistrer Moment from WP core
* **Tweak** - Update Moment from 2.22.2 to 2.24
* **Tweak** - Remove error_log

### 1.1.6: April 12th, 2019
* **Fix** - For new rooms, was not getting prices
* **Fix** - Price maximum fractions : 2 instead of 0

### 1.1.5: January 14th, 2019
* **Fix** - Fix days of the week switching language after clicking next month

### 1.1.4: January 4th, 2019
* **Revert** - Change for button with link to allow better Google Tag Manager tracking

### 1.1.3: December 17th, 2018
* **Fix** - Best Price not updated when date has passed

### 1.1.2: November 1st, 2018
* **Tweak** - Outro behaviour
* **Tweak** - Handle ZH-CN Moment locale
* **Tweak** - Empty default dates
* **Tweak** - Align Outro on mobile
* **Tweak** - Change for button with link to allow better Google Tag Manager tracking

### 1.1.1: August 22nd, 2018
* **Tweak** - Outro alignment

### 1.1.0: August 14th, 2018
* **Tweak** - Show calendar selected dates only when begin/end dates are selected and valid
* **Tweak** - Removed deprecated setting 'rateids'

### 1.0.9: July 23th, 2018
* **Fix** - Better handling of prices when xml values 'property' are not formatted the same
* **Fix** - Remove best year price when date is passed
* **Tweak** - Execute cron when visiting the options page

### 1.0.8: July 23th, 2018
* **New** - Shortcode displaying the best year price now with parameters:
    * Best price of a room with `[tmsm-availpro-bestprice-year roomid="xxx"]`
    * Best price of a rate with `[tmsm-availpro-bestprice-year rateid="xxx"]`
    * Be sure to set the room and rate ids in the settings of the plugin

### 1.0.7: July 20th, 2018
* **New** - Calculated data: tmsm-availpro-bestprice-year which is the best price of the next 12 months
* **New** - Shortcode displaying the best year price `[tmsm-availpro-bestprice-year]`
* **New** - Calculate OTA prices in stayplanning
* **Fix** - Empty year best price when rechecking month with year best price

### 1.0.6: July 19th, 2018
* Change cron from hourly to 5 minutes
* Check calendar existence
* Fix legend CSS

### 1.0.5: July 18th, 2018
* Optimizations on mobile

### 1.0.4: July 18th, 2018
* Fix error with options

### 1.0.3: July 18th, 2018
* New option "Book button label"
* Fix Polylang/WPML compatibility

### 1.0.2: July 17th, 2018
* Remove JS debug

### 1.0.1: July 17th, 2018
* Change style

### 1.0.0: April 20th, 2018
* Plugin boilerplate