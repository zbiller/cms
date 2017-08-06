<?php

return [

    /**
     * Price settings.
     */
    'price' => [

        /**
         * The default currency to be used in all price operations inside the entire shop component.
         *
         * All of the accepted values can be found inside the "/database/seeds/CurrenciesSeeder.php" file.
         * More exactly, all the "array keys" from the "$currencies" protected property inside the class.
         */
        'default_currency' => 'USD'
    ],

    /**
     * Cart settings.
     */
    'cart' => [

        /**
         * This option accepts an integer, representing the number of days.
         *
         * When the "clean cart" operation is fired, records older than the number of days specified here will be deleted.
         *
         * If set to "null" or "0", no past carts will be deleted whatsoever.
         */
        'delete_records_older_than' => 30,

        /**
         * This option accepts an integer, representing the number of days.
         *
         * When the "send reminders" operation is fired, only users having created their cart earlier that this value will be notified.
         *
         * If set to "null" or "0", every user with an ongoing shopping cart will be notified.
         */
        'remind_only_older_than' => null,
    ],

];