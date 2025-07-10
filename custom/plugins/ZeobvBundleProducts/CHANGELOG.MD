# 2.4.23

-   The fix quantity selection in category listings with zenitPlatformGravity theme.

# 2.4.22

-   The fix Discount calculation with bundles issue, fixed by setting the cart processor order in cart.xml to 4950 instead of 4850

# 2.4.21

-   The fix ensures that the list price is now correctly calculated and assigned during price aggregation.
-   Prevents Call to a member function getExtension() on null by ensuring $product is valid before accessing its methods.

# 2.4.20

-   Added configuration to set bundle child item price to 0.01 in DHL shipping label.

# 2.4.19

-  Enhanced the bundle plugin could not be deactivated at the sales channel level.
-  Added "Plugin Guide" and "FAQ" buttons to the plugin configuration page.

# 2.4.18

-  Fixed an issue where the bundle plugin and the subscription article plugin conflicted due to both decorating the CartItemAddRoute.

# 2.4.17

-  Added support for api orders by enriching the order with the bundle child line items for bundle products via channels like channable.

# 2.4.16

- Resolved the issue where bundle items were incorrectly receiving discounts when using the "Sum prices of products in bundle to calculate new bundle price" pricing mode.

# 2.4.15

- Resolved the issue where the bundle was being calculated twice in the frontend, which caused incorrect pricing when using the Custom Product plugin.

# 2.4.14

- Updated compatibility with line item factory

# 2.4.13

-   Resolved an issue in MailServiceDecorator where array_filter() caused a TypeError when lineItems was null. Added checks to ensure lineItems defaults to an empty array if unset or null and validated its type before processing.
-   Resolved TypeError in DeliveryInformation due to null for freeDelivery ensured getShippingFree() returns a boolean.

# 2.4.12

-   Removed the compatibility with SwagCustomisedProducts.

# 2.4.11

-   Added missing deliveryInformation to bundle child product items.

# 2.4.10

-   Fixed prevents critical frontend errors during price calculation for product bundles.

# 2.4.9

-   Fixed the issue with custom fields not displaying correctly for bundle items in the shopping cart.

# 2.4.8

-   Added closeout field to bundle availability section to gain insight into the backorder status of a bundle product. Note that this field is automatically set based on the items in the bundle and a bundle product will only be set to closeout if it is unavailable due to the availability and backorder status of its items.
-   Updating is_closeout field in the database of bundle products when the availability of it changes to accurately reflect the backorder status of a bundle product and have it play better with certain Shopware features that directly query the database.

# 2.4.7

-   Re-established compatibility with SwagCustomisedProducts.
-   Fixed an issue where the available stock of an article was reduced by 2 instead of 1 immediately after an order was placed.

# 2.4.6

-   Added new command to sync the calculated stock for bundle products to the database.
-   The new command will disable pickware stock management for all bundle products to avoid shipping issues.
-   Added listener to product safe event in administration to disable the pickware stock management for bundle products.
-   Added new fields to the bundle configuration to give insight in the actual stock and available stock of a bundle product since Pickware removes the stock fields when pickware stock management is disabled.

# 2.4.5

-   Fixed the available flag not being updated for bundle products.

# 2.4.4

-   Modified the picklist document to correctly display the line items after a change by Pickware.

# 2.4.3

-   Fixed the warning for an undefined array key 'salesChannelId'.

# 2.4.2

-   Fixed configuration option to hide items inside a bundle form the order confirmation mail.

# 2.4.1
  
-   Added extra option to the item selection configuration to make some products non-optional.

# 2.4.0

-   Added option to disallow quantity selection for specific bundle items in the storefront.

# 2.3.3

-   Fixed compatibility issue with the Back in Stock extension.

# 2.3.2

-   Resolved the issue with creating invoices or documents

# 2.3.1

-   Resolved the issue of hiding child items of type product in the invoice.

# 2.3.0

-   Added option to present the bundles in which a product is included on the product detail page as a cross selling carousel. Tab label and presentation can be configured in the extension configuration.

# 2.2.2

-   Fixed issue where the line item type was not supported in the cart.

# 2.2.1

-   Fixed issue where CbaxOrderConverterDecorator had a dependency on a non-existent service.

# 2.2.0

-   Fixed dependency injection issue
-   Added ability to set price calculation mode on a per-bundle basis
-   Fixed error in price calculation when adding certain bundle configurations to the cart from the listing page.

# 2.1.3

-   Added 6.6.3 compatibility

# 2.1.2

-   Now hiding bundle items from cart and checkout when 'Show products in bundle on storefront' is disabled.

# 2.1.1

-   Fixed cart error when for bundle products with 'Show products in bundle on storefront' disabled

# 2.1.0

-   **ATTENTION** - Major update
-   Added new feature to allow the customer to select which items they want in the bundle on the storefront. Only available for the "SUM prices" calculation mode.
-   Added new feature to allow the customer to change the quantity of items in the bundle on the storefront. Only available for the "SUM prices" calculation mode.
-   Added feature to allow the customer to switch between variants of a bundle item by assigning the parent product of the variants to the bundle. Only available for the "SUM prices" calculation mode.
-   Added feature to set a discount on product level in the bundle. Only available for the "SUM prices" calculation mode.
-   Improved UX of bundle configuration in the administration.
-   Changed structure of the bundle item overview, some classes have been refactored and the overview has been moved to the buy-widget-form.html.twig templates now using sw_include to include our zeobv-bundle-products/components/buy-widget-form.html.twig to avoid code duplication.
-   Performed some refactoring to improve code complexity and readability. Split up the bundle product collector and processor to reduce the complexity of these classes.
-   Improved style of bundle reference on bundle item product page
-   Improved snippet of bundle reference on bundle item product page
-   Advanced prices are now disabled for bundle products in combination with the SUM price mode to prevent unwanted discounts on discounts.

# 1.7.8

-   Resolved the issue of displaying digital product delivery information.

# 1.7.7

-   Resolved the issue of Bin Location unknown in the picklist.

# 1.7.6

-   Added a configuration option to hide the quantity of bundle items in the product detail page table presentation.
-   Added a configuration option to change the background and border color in the main product special card.

# 1.7.5

-   Resolved the responsive template image is missing, and it displays a blank space instead.

# 1.7.4

-   Resolved the error of TaxDetector.

# 1.7.3

-   Added seo url in bundle product link on product detail pages.

# 1.7.2

-   Resolved the picklist pdf not generated.

# 1.7.1

-   Added missing title tag in bundle info.
-   Added configuration option to hide bundle child line items on order confirmation mail.

# 1.7.0

-   Many structural changes to the plugin to improve performance and stability
-   Improved some snippets to be more clear in the configuration and on the storefront
-   Added optional bundle support for Shopping Experience templates
-   Improved design of optional bundle checkbox
-   Moved some configuration options around to make more sense

# 1.6.6

-   Fixed bug with optional bundle configuration in some cases where the bundle would not be added to the cart
-   Improved performance for large scale google feeds and listings by reducing the amount of queries needed to fetch the bundle product information

# 1.6.5

-   Fixed bug where the bundle items would cause a template error if no cover image was set for the product

# 1.6.4

-   Add option to offer bundle optionally

# 1.6.3

-   Fixed VAT calculation issue when using net prices in combination with advanced pricing and higher quantities

# 1.6.2

-   Fixed VAT calculation issue when using net prices in combination with advanced pricing

# 1.6.1

-   Show variant detail at product detail page if bundle child item is variant product.

# 1.6.0

-   Responsive design update for bundle child product table at product detail page.

# 1.5.13

-   Added configuration option to show bundle product items detail in the pop-up.
-   Added configuration option to mark bundle product unavailable if one of its children is unavailable.
-   Added configuration option to hide bundle line item prices on invoice if price is 0.0.
-   Added configuration option to hide bundle child line items on invoice documents.

# 1.5.12

-   Resolved delete Existing Bundle Connections time deletes all entries from the product.
-   Added Custom filed data in the cart.

# 1.5.11

-   Added compatibility patch for Marketplace eBay order import plugin.

# 1.5.10

-   Revised the bundle product fit price calculation to be more accurate
-   Added option to disable the reference to the bundle product from the product detail page of products inside a bundle

# 1.5.9

-   Removed . in template

# 1.5.8

-   Patch for performance hit on shops with a large amount of products

# 1.5.7

-   Added a new configuration option which allows you to limit the amount of references to the bundles a product is part of to protect the performance of the product detail page. Default is set to 10.

# 1.5.6

-   Significantly improved the performance of bundle collection.
-   Changed synchronised stock updates during order processing to be performed asynchronously via the message queue.

# 1.5.5

-   Resolved bundle child item removed from the order when using Product Configurator extension with the bundle when adding to the cart product with a different configuration.

# 1.5.4

-   Resolved Unknown "pickware_erp_product_name" filter on picklist with CogiPickLists extensions..

# 1.5.3

-   Added configuration option to disable Purchase price override.

# 1.5.2

-   Added link to bundle product from product detail pages of products that are inside a bundle

# 1.5.1

-   Resolved bundle product link on when visibilities is selected Hide in Listings and search.

# 1.5.0

-   Introduced compatibility for Shopware 6.5.0

# 1.4.13

-   Casting maxPurchase to int to avoid type error

# 1.4.12

-   Product detail bundle table display advance pricing bug fixed.
-   Added configuration option to disable Max. order quanity override.
-   Fixed weight calculation issue for bundle items with a quantity higher than 1

# 1.4.11

-   Make bundle product inherit the lowest "Max. order quantity" value from products in the bundle.
-   Resolved price calculation bug in administration product listing

# 1.4.10

-   Changed style of product number in bundle line item table
-   Edited config option label to provide more clarity
-   Fixed bug where the manufacturer would not be included anymore

# 1.4.9

-   Added product number to bundle product table when enabled in plugin config.
-   Added Bundle product quantity to cart and checkout items when enabled in plugin config.
-   Added hide bundle information in shopping cart and checkout when enabled in plugin config.
-   Fixed display hide/show price in shopping cart, order finish, user account order.

# 1.4.8

-   Fixed the design issue in Storefront.

# 1.4.7

-   Performed some fine-tuning in the sorting of line items of the Pickware picklist template override
-   Fixed an issue where the bundle product would fall apart during reorder via the Storefront
-   Fixed a rare exception in combination with advanced prices during bundle generation

# 1.4.6

-   Fixed missing cover image in bundle item list
-   Fixed error in cart collector when using the Shopware custom product plugin

# 1.4.5

-   Fixed error occuring when a customer would be excempted from taxes

# 1.4.4

-   Fixed small bug in advanced price selection in the bundle product overview on the product detail page

# 1.4.3

-   Fixed sub product item recalculation when adding a credit item in the order
-   Fixed issue where bundle products wouldn't always get saved
-   Added fix for duplicate number in bundle item positions
-   Fixed sorting of bundle line items on the storefront.
-   Bundle products are now considering inheritance for variants within a bundle, fixing the price of a bundle product containing variants in the admin

# 1.4.2

-   Fixed order on picklist

# 1.4.1

-   Fixed division by zero issue

# 1.4.0

-   Added advanced prices support
-   Added price calculation optimisations
-   Added option to hide total price in bundle price list

# 1.3.3

-   Updated weight override to be more configurable

# 1.3.2

-   Added weight override

# 1.3.1

-   Improved presentation of bundle products in picklist document
-   Product variant swatches now correctly present the availability of a variant bundle product

# 1.3.0

-   Added partial support with Pickware ERP Starter. Warning, this update is experimental and can alter the function of the plugin on certain installations. Disable the newly added backwards compatibility config option for this new compatibility to take effect.
-   New configuration option added to create backwards compatibility with older installations. To improve compatibility with Pickware ERP starter disable the new option "Use a custom line item type instead of the default 'Product' line item type" option.
-   Deprecating the stock management configuration setting. When line items of type product are used Shopware will now manage the stock accordingly.
-   Added async hard "stock" and "available stock" updates via the message queue.
-   Fixed rounding precision errors originating from cart collector

# 1.2.6

-   Added partial compatibility with Shopware's Customized Products plugin. Some anomalies still occur

# 1.2.5

-   Fixed error in admin when adding a new product to the bundle

# 1.2.4

-   Added an option to divide the stock by the quantity of the product inside the bundle

# 1.2.3

-   Fixed error during add to cart in a Tax Free context

# 1.2.2

-   Added position support for pre-1.2.0 bundle products.

# 1.2.1

-   Added block to the buy-widget override called buy_widget_buy_container_bundle_list to improve extendability

# 1.2.0

-   Added manual sorting feature for products inside a bundle.

# 1.1.27

-   Patch for issue introduced in 1.1.26, causing an error for products without a manufacturer.

# 1.1.26

-   Added manufacturer to bundle item payload for use in documents and mail templates
-   Added manufacturer name to cart and checkout items when enabled in plugin config

# 1.1.25

-   Fixed german translation error

# 1.1.24

-   Fixed tax calculation issue when using the "Set the price of products inside a bundle to 0.00" option.

# 1.1.23

-   Changed "Adjust the product prices to fit within the bundle price" calculation to attempt providing more representative prices.

# 1.1.22

-   The multi vat calculation is disabled in combination with the "Don't perform any calculations" setting found in the plugin settings

# 1.1.21

-   Added multi vat support

# 1.1.20

-   Added link to product detail page of products within a bundle on the storefront
-   Added option to show the cover image of products inside the bundle as a thumbnail on the storefront
-   Added option to show the availability of products inside the bundle on the storefront
-   Added import/export support for bundle product connections

# 1.1.19

-   Fixed bug of bundle connections not refreshing when switching to another product using the "Show product" function.

# 1.1.18

-   Increased bundle connection limit from 25 to 100
-   Rebuild administration JS files under v6.4.5.0 to fix issue https://github.com/shopware/platform/issues/2420

# 1.1.17

-   Patched bundle connection save issue occurring in rare cases.

# 1.1.16

-   Fixed issue where product layouts would conflict with new bundle product price calculation

# 1.1.15

-   Added option to override the "Stock" field of the product with the stock of the product in the bundle with the lowest available stock.

# 1.1.14

-   Add bundle purchase price calculation based on purchase prices of products inside the bundle

# 1.1.13

-   Now calculating weight of bundle product based on weight of products inside the bundle.
-   Price shown on product detail page of products inside the bundle now based on calculated price. Unfortunately it's not yet possible to included advanced prices in this price calculation

# 1.1.12

-   Backwards compatibility patch with v.6.4.1.0

# 1.1.11

-   Made some adjustment to the stock inheritance system in order to improve the stock and availability determination

# 1.1.10

-   Added delivery time inheritance from the product with the longest delivery time

# 1.1.9

-   Added extra configuration option to select a price calculation mode for both bundle product and items within a bundle product
-   Added extra configuration option to set the price of all line items within a bundle product to 0.00.

# 1.1.8

-   Added option to activate stock management for products inside a bundle.
-   Added product number to bundle product select results to make identifying variants easier.

# 1.1.7

-   Fixed order of bundle line items in documents

# 1.1.6

-   Refactored bundle relation assignment resulting in a significant performance improvement.
-   Improved UX of bundle product management in the administration
-   Added configuration option to disable automatic recalculation of prices of products in a bundle.

# 1.1.5

-   Added extra options for bundle item storefront presentation
-   Fixed some small visualisation issues

# 1.1.4

-   Fixed errors in bundle line item price calculation

# 1.1.3

-   Fixed line item integrity constraint error when adding/editing an order with a bundle line item via the admin
-   Fixed quantity validation error when adding/editing an order with a bundle line item containing a stock > 1

# 1.1.2

-   Added compatibility patch for cart recovery plugin
-   Fixed decimal precision issue occurring when cart is empty.

# 1.1.1

-   Fixed uninstall error
-   Added reference version fields to avoid error during dal:schema:create command
-   Improved available stock determination by including bundle item quantity in the equation

# 1.1.0

-   Added compatibility for v6.4

# 1.0.5

-   Hotfix addressing error in checkout

# 1.0.4

-   Fixed order of bundle line items in documents

# 1.0.3

-   Improved performance.
-   Order delivery positions created for cart child items.
-   Moved bundle information from payload info to cart child items.
-   Code refactor, removed dead code.

# 1.0.2

-   Replaced getPayloadValue by hasPayloadValue to prevent an exception from being thrown in older Shopware versions

# 1.0.1

-   Fixed price calculation of bundle product child items
-   Added better bundle item price insights in order details
-   Improved styling of bundle product item grid on product detail page
-   Fixed bug where "Show products in bundle on storefront" toggle would be hidden
-   Fixed bug where bundle items would still be visible on order finish screen
-   Fixed bug where bundle items would still be visible in account order details
-   Fixed bug where the unit price of bundle item would be presented as "FREE:" in the account order details

# 1.0.0

-   First version of the Zeo Bundle Products for Shopware 6
