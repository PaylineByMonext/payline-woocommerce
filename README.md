Payline extension for wooCommerce
====================================

This plugin allows the merchant to connect his store to Payline payment gateway.
Payline offers many payment means (Visa, Mastercard, American Express, Paypal, JCB,...) and a powerfull anti-fraud system.

Docs
====

All information available on https://support.payline.com/hc/fr/articles/201084116-modules-Payline-pour-WooCommerce-1-x-et-2-x


About
=====

Requirements
------------

This extension requires at least wooCommerce 2.6.
It's tested up to WooCommerce 3.3.0.


Author
------

* Fabien SUAREZ - <fabien.suarez@payline.com>
* Nicolas MOLLET (https://github.com/thermesmarins)
* Timothe BORDIGA (https://github.com/roux1max)


License
-------

Payline is licensed under the LGPL-3.0+ License

Changelog
-------

* 1.4
     Add widget integration

* 1.3.7 - 2020/12/01  
     Feature - WooCommerce 3.x compatibility (not compatible anymore with WooCommerce versions below 2.6)
     Transaction id compatibility, Translation files
     Fix on token get data (versus paylinetoken)

* 1.3.6 - 2018/01/02  

     Fix - Truncate buyer data before send it to Payline.
     
* 1.3.5 - 2017/04/04  
     Feature - send buyer info mandatoty for Cetelem 3x / 4x
     
 * 1.3.4 - 2017/02/27  
     Fix - languages files

* 1.3.3 - 2016/08/26  
     Feature - order/token association. Prevents conflicts between payment sessions.

* 1.3.2 - 2016/08/04  
     Fix - Truncate order details product name to 50 characters before send it to Payline.

* 1.3.1 - 2015/12/09  
     Feature - compliance with Payline PHP library v4.43

* 1.3 - 2015/02/27  
     Feature - compliance with wc 2.3 and over
