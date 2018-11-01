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