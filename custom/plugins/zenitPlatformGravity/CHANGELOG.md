# 4.4.1
- Optimization: Improved arrangement of content in the cookie widget.
- Optimization: Image placement for hero section. 
- Optimization: Fix contrast issue for video element loader backdrop. 
- Bugfix: Fix sticky aside in checkout.
- Bugfix: Fix 'Category-Navigation' "Active path only" option.
- Bugfix: Fix properties table contrast color issue.

# 4.4.0
- Feature: Added feature to import demo configuration presets to the theme, child themes and duplicates. | Pro Version
- Feature: Added new CMS-Element "Category-Navigation". | Pro Version
- Feature: New icons added to product feature configuration.
- Feature: Media type 'video' support for category "Hero Section".
- Feature: Media type 'video' support for CMS-Element "Text Banner". | Pro Version
- Feature: Media type 'video' support for CMS-Element "Teaser". | Pro Version
- Feature: Media type 'video' support for CMS-Element "Search Banner". | Pro Version
- Feature: Media type 'video' support for CMS-Element "Image Text Slider". | Pro Version
- Feature: Added new configuration to CMS-Element "Divider" to add H-Tag to text. | Pro Version
- Feature: Added new configuration to CMS-Element "Category listing" to select custom category as entry point. | Pro Version
- Feature: Added new configuration option to disable flyout-navigation animation. 
- Compatibility: Fixes compatibility with Acris Search Pro.
- Compatibility: Implement Shopware 6.6.5.0 changes.
- Compatibility: Fixes compatibility with Netzperfekt Blog Premium plugin.
- Optimization: Improvement of the theme configuration.
- Optimization: Twig can now be used for text of the marketing text slider, if twig syntax is allowed.
- Optimization: Added default value for `product.purchaseSteps` because of Pickware allows creating products with null value.
- Optimization: Prevents a text cutoff on product boxes in some circumstances.
- Optimization: Better appearance of the PayPal buttons in the buybox to match the add to cart button.
- Optimization: Accordion tabs will now scroll into view after collapsing to prevent wrong scroll position with long content.
- Optimization: Fixed a z-index issue with quantity selection in offcanvas cart.
- Optimization: Improved scroll to calculation after page switch in product listings.
- Optimization: Buybox tax link optimized.
- Optimization: Changed parameters in a more logical order for mixins `layout-container-horizontal-padding`, `layout-container-negative-horizontal-margin`, `layout-container-horizontal-margin`.
- Optimization: Storefront optimizations.
- Optimization: Refactored floating labels.
- Bugfix: Fixes z-index issue in section offsets.
- Bugfix: The offcanvas cart did not refresh correctly when the offcanvas add to cart by number feature was used.
- Bugfix: Fixes not working scroll animations in some circumstances.
- Bugfix: Fixes double links in the slider.

# 4.3.5
- Optimization: Improves the indentation of multi-line product feature texts on `list-item`.
- Bugfix: Fix body-color;

# 4.3.4
- Optimization: Add logic for product listing action button 'select options' on expanded variants without property assignment.
- Optimization: Improve contrast of color in account dropdown menu.
- Optimization: Footer links underline colors.
- Bugfix: Fixes gallery slider mobile height issue because of shopware !important styles.

# 4.3.3
- Optimization: The text color of badges in the flyout navigation will fallback to `$body-bg` if `$zen-sidebar-navigation-bg` is transparent.
- Optimization: Prevent full-width sections modal breakout on boxed theme-layouts.
- Optimization: Improve code smell from code review.
- Optimization: Improvement of cols for CMS-Layout 'Advanced layout grid' and 'Advanced layout grid reversed'. | Pro Version
- Optimization: Harmonize top bar buttons.
- Bugfix: Prevent side-scrolling on mobile devices when using gallery-scroller oder gallery-grid.
- Bugfix: Fix object-position for images in contain-mode for CMS-Element "Category listing" | Pro Version

# 4.3.2
- Compatibility: Implement Shopware 6.6.3.0 changes.
- Optimization: Improvement of the theme configuration.
- Optimization: Set missing color for `.category-navigation-toggle .icon`.
- Bugfix: Fix image `min-height` for `.gallery-slider-single-image`.
- Bugfix: Fix category navigation toggle for active element.

# 4.3.1
- Optimization: Optimize footer bottom social media icons vertical alignment.
- Optimization: Fix flyout navigation padding.
- Bugfix: Fix footer link color.

# 4.3.0
- Feature: Added CMS-Element "Category listing" | Pro Version
- Feature: Added new configurations to add product listing action button 'select options'. | Pro-Version
- Compatibility: Fixes compatibility with Custom Products plugin.
- Compatibility: Fixes compatibility with neonlines Product Configurator
- Compatibility: Fixes compatibility with Atloss Product Configurator
- Optimization: Refactored product listing actions button.
- Optimization: Removed 'ZenBuyWidgetQuantityInputPlugin'.
- Optimization: Load Javascript after HTML content in CMS-Element "Code". | Pro-Version
- Optimization: Refactored sticky header and renamed `js-sticky` into `js-header-sticky` for sticky header contexts.
- Optimization: Refactored sticky elements with sticky header height.
- Optimization: Offcanvas headers now have consistent titles.
- Optimization: Removed `$zen-product-gallery-slider-min-height-desktop`, because this will be set in `cms-element-image-gallery` only from Shopware 6.6.
- Optimization: Changed breakpoint for `zen-product-listing-card-actions-mobile` from `lg` to `xl`.
- Optimization: Changed the language flag from `div` to `span` because children of button should be inline-elements.
- Optimization: Optimizes some contrast issues with dark layout.
- Optimization: Optimizes jumping footer accordions.
- Optimization: Display of overlapping checkboxes in offcanvas cookie consent for safari mobile.
- Optimization: Harmonizes misalignment of mobile review badge.
- Optimization: Optimizes transparent quantity input on product cards.
- Optimization: Changed block layout margins from `auto` to `20px` to be in line with the Shopware standard.
- Bugfix: Fixed a z-index issue of the "Search banner" element.
- Bugfix: Fixed overlapping sticky buybox.
- Bugfix: Fixed product rating wraps on mobile safari.
- Bugfix: Fix previewSource in CMS elements media selection.

# 4.2.0
- Feature: Added new configurations to prevent the offcanvas cart to open automatically when a product is added to the cart.
- Feature: Added CMS-Block "Image gallery and buy box with description" | Pro & Cloud Version
- Feature: Added new configurations to make the checkout summary/sidebar sticky.
- Optimization: Increased line-height für headings.
- Optimization: Google fonts loader, allow double quotes in config field.
- Optimization: Edge case sticky functionality in combination with buybox tabs and tabs mode scroller.
- Optimization: Refactored checkout aside background.
- Optimization: Added ACL rules to settings items and dashboard route. | Pro Version
- Optimization: Refactored footer newsletter column.
- Optimization: The product number input in the offcanvas cart will no longer redirect to cart after the product has been added.
- Optimization: Floating labels and Autofill.
- Compatibility: Fixes compatibility with BuI Hinsche Product Configurator plugin.
- Bugfix: Animated product boxes on the guest wishlist were not displayed.
- Bugfix: CMS search banner `width` css invalid. | Pro Version
- Bugfix: CMS features element added missing icon. | Pro Version
- Bugfix: Fix undefined `$parent.block` in cms. | Pro-Version
- Bugfix: Fix some display issues because of Shopware 'ADR to make more use of Bootstrap tooling'.
- Bugfix: Fixed overlapping sticky buybox.

# 4.1.0
- Feature: Added CMS-Block "Image gallery with description and buy box with product name & manufacturer logo" | Pro & Cloud Version
- Feature: Added new configurations to add borders to shop-navigations.
- Feature: Added new configuration to fill flyout-navigation columns dynamically.
- Feature: Added new configuration to set flyout-navigation max-height.
- Feature: Added new configuration to set tab navigation text-transform.
- Feature: Added new configuration to set tab navigation margins.
- Feature: Added new configuration to set the size of the offcanvas logo.
- Feature: Added new configuration to set sidebar navigation toggler icon.
- Feature: Added new configuration option to set filter toggler icon +/-.
- Feature: Added new configuration option to set full width header & footer for basic layout.
- Optimization: Changed sidebar categories line height to fit with filter line height.
- Optimization: Refactored container-main and breadcrumb container spacings.
- Optimization: Fixed a display error where the mobile zoom modal was not displayed in full height.
- Optimization: Trusted Shops badges will now be hidden if a modal is open.
- Optimization: Removed unnecessary feature `zen-sidebar-navigation-arrow`.
- Optimization: Refactored cms-grid vertical gutters to margin-bottom.
- Optimization: Bind header-tooltips on header container.
- Optimization: Refactored custom-fields helper.
- Bugfix: lazy loading is now working for teaser elements.
- Bugfix: Fixed missing cms default media in storefront.

# 4.0.0
- Compatibility: Established compatibility with Shopware 6.6.
- Compatibility: VUE 3 upgrade for dashboard and cms elements.
- Compatibility: Storefront async JavaScript and all.js removal.
- Compatibility: Removed unused theme-configurations for non-cms product detail page.
- Compatibility: Get rid of deprecated 'page/product-detail/' templates.
- Compatibility: Added new configuration to position product name in quickview.
- Compatibility: Moved `zen-custom-global-tab.html.twig`, `zen-custom-tab1.html.twig`, `zen-custom-tab2.html.twig`, `zen-datasheets.html.twig`, `zen-manufacturer.html.twig`, `zen-properties-tab.html.twig`, `zen-video-tab.html.twig` and `zen-video-embed.html.twig` into 'component/product/' directory.
- Feature: Added CMS-Block "Image gallery and buy box with product name & manufacturer logo" | Cloud Version
- Feature: Added CMS-Block "Image gallery and buy box with product name & manufacturer logo & description" | Cloud Version
- Feature: Added CMS-Block "Footer navigation"
- Feature: Added new configuration to always display the product number input 
- Feature: Added new configuration to display the product number input in the offcanvas cart
- Optimization: Fixed previewSource of cms elements.
- Optimization: Updated YouTube Social-Icon.

# 3.8.0
- Feature: Added Telegram to social media icons.
- Feature: Added new configuration to set `text-transform` for footer headings.
- Feature: Added navigation badge into offcanvas navigation. 
- Feature: Added compatibility with spatial commerce / AR and 3D features
- Feature: Added new configuration to change the appearance of the product detail description heading.
- Feature: Added new configuration to display the product description tabs as linear content.
- Feature: Added new configuration in cms code element to change truncate character length.
- Feature: Added cms element 'zenit-features' icon preview.
- Feature: Added new icons to cms element 'zenit-features'.
- Optimization: Prevent jumping header when js-sticky is removed.
- Optimization: Quantity-input `border-end-radius` on container query.
- Optimization: Sticky header overlays sticky reviews.
- Optimization: Footer column logo.
- Optimization: Reduce product card vertical empty spacings.
- Optimization: Adjust dropdown width on offcanvas tob bar.
- Optimization: Improves the indentation of multi-line product feature texts.
- Optimization: Advanced prices table background on detail pages.
- Optimization: Improved `form-select-indicator` arrow color.
- Bugfix: Fixed horizontal scrolling on offcanvas top bar on safari.
- Bugfix: Added missing blocks `thumbnail_utility` and `thumbnail_utility_img`.
- Bugfix: Fixed an issue for product cms layouts when an assignment for a productbox was removed.

# 3.7.2
- Optimization: Improved display and usability of the theme configuration. | Pro Version
- Optimization: Quantity-input `border-end-radius` on container query.
- Optimization: Removed admin dashboard changelog.
- Optimization: Add positionIdentifier.
- Bugfix: `input&` causes error in watcher mode.

# 3.7.1
- Compatibility: Added compatibility with Shopware 6.5.8.2 because of shopware 'NEXT-31820 - Revert Update NPM packages' due to compatibility issues.
- Feature: Added new configuration to change scroll-up-button color.
- Optimization: Contrast color of list-price-price.

# 3.7.0
- Compatibility: Added compatibility with Shopware 6.5.8.0.
- Optimization: Ensures the price is always at the bottom of the box, even if other boxes are higher.
- Bugfix: Mobile cover-switch touchevent prevents click event.

# 3.6.6
- Bugfix: Gallery-Slider thumbnails wrap

# 3.6.5
- Feature: Added new configuration to display the shipping details collapse in initially open state.
- Feature: Added new configurations for positioning the scroll-up button.
- Optimization: Cart ordernumber input and shipping details collapse element styled.
- Optimization: Header border-bottom in combination with border-radius.
- Optimization: Offcanvas margin.
- Optimization: Header border-radius on two-lined-header.
- Optimization: Header box-shadow on transparent header.
- Optimization: Breadcrumb margin-bottom.
- Optimization: Prevents wrap thumbnail-navigation of gallery slider.
- Optimization: Checkout shipping / payment method image svg fix for Safari.
- Bugfix: Custom fields product per row.
- Bugfix: Mobile single line header logo centering.
- Bugfix: Main-Navigation centered position.
- Bugfix: Theme-Dashboard changelog order.
- Bugfix: Fix division by zero if flyout columns are set to 1.

# 3.6.4
- Optimization: Prevent flickering dropdown on main-navigation.
- Optimization: Added missing container padding on boxed layout.
- Optimization: Refactored main-navigation centered alignment because of nav-scroller.
- Optimization: Offcanvas main-navigation paddings.
- Optimization: Offcanvas headlines.
- Optimization: Changed quantity input classes for better compatibility.
- Bugfix: Correct gallery slider contain.
- Bugfix: Simple layout 4/8.
- Bugfix: Add missing custom field for image mode. | Cloud Version

# 3.6.3
- Bugfix: missing app rewrites

# 3.6.2
- Optimization: Account sidebar navigation underline.
- Optimization: Changed breakpoint for three column elements from `lg` to `md` viewport.
- Feature: Added new configuration to set badges color in shop-navigation.
- Bugfix: Nav Scroller on centered navigation.
- Bugfix: Floating labels select fields color.

# 3.6.1
- Bugfix: App script empty media entity check.

# 3.6.0
- Feature: Added logged-in indicator;
- Feature: Added new configuration option to display language id in langauge-switcher.
- Feature: Added new configuration to minimize shop navigation badges.
- Feature: Added new configuration options for border-radius.
- Feature: Added new configuration to set `text-transform` for breadcrumbs.
- Feature: Added new custom field to set category image mode.
- Feature: Added new configuration to set font-size and font-weight fot top bar and shop-navigation.
- Feature: Added new configuration to set offcanvas width.
- Feature: Added new configuration to switch text-editor to code-editor in CMS Elements Search Banner and Text Banner to prevent sanitizing. | Pro Version
- Feature: Add cover switch to cloud. | Cloud Version
- Optimization: Changed behaviour of breadcrumbs `margin-bottom` to reflect container layout spacing.
- Optimization: Removed unnecessary woff-files, because IE 11 support was dropped by shopware 6.5.
- Optimization: Extend compatibility to different extensions.
- Optimization: Header actions btns increase on mouse-over.
- Optimization: Improved sticky buybox and sticky gallery on product details page. 
- Optimization: Add `navigationTree` query to prevent empty category-navigation.
- Optimization: Floating labels improvements.
- Optimization: Gallery slider thumbnails arrows.
- Optimization: Lighthouse audit improvements.
- Optimization: Removed unload-listener.
- Optimization: Sidebar navigation active / hover status.
- Optimization: Refactoring footer newsletter.
- Optimization: Prevent hero section `clip-path` from cutting overflows.
- Optimization: Reduce gap between sidebar and main-content on auto layout spacings.
- Optimization: Improve listing scroll animation.
- Optimization: Styled filter scrollbars.
- Optimization: Added line-item image background color and background overlay color.
- Optimization: Checkout header.
- Optimization: Reset btns to bootstrap default.
- Optimization: Optimized review stylings.
- Optimization: Change naming of category image function to hero section.
- Optimization: Twig code style.
- Optimization: Improve CMS elements height in higher cols.
- Optimization: Add touch events to hover animations.
- Optimization: Resolve variable mapping in cms elements.
- Optimization: Renamed `sizes` into `breakpoints` because sizes-attributes of thumbnails got no values.
- Bugfix: Added `webkit-prefix` to `backdrop-filters`.
- Bugfix: Search Banner suggest-search z-index;
- Bugfix: Prevent calculating wrong thumbnails in listings when listing gutter is `0`.
- Bugfix: Fix zoom-modal close icon on dark backgrounds.
- Bugfix: Vimeo video tab not showing.

# 3.5.5
- Feature: Added new configuration to CMS-Elements to set background blur.
- Feature: Added new configuration to CMS-Element image text slider to set number of slides for each viewport.
- Optimization: CMS-Element overflows.
- Optimization: Prevents product slider controls from being cut off.
- Optimization: Configuration to disable service navigation on two-line header and offcanvas top bar.
- Optimization: Remote click on .product-detail-reviews-link.
- Bugfix: Product listing scroll animation was not initialized on certain pages.
- Bugfix: Fixed CMS-Element Divider font-weight.

# 3.5.4
- Bugfix: Fix icons packs. 

# 3.5.3
- Breaking change: Changed default value of `$zen-product-gallery-img-bg`. Using variables as defaults is problematic because the loading order of variables can be random. This results in undefined variables.
- Breaking change: Added new configuration `$zen-product-gallery-img-color-overlay` to enable background color overlay on gallery images. 
- Breaking change: Removed configuration `$zen-product-gallery-img-display-bg`. 
- Feature: Added new configuration to set number of cols in navigation flyout.
- Feature: Added new configuration to set offcanvas margin.
- Feature: Added new configurations to set backdrop colors.
- Feature: Added new configurations to set backdrop blur.
- Optimization: Improves gallery grid initialization.
- Optimization: Keep Javascript event listeners when initializing product detail description accordions.
- Optimization: Breadcrumbs active element hover.
- Optimization: Accordion properties width on cms elements.
- Optimization: Changed "Heroicons" stack-icon.
- Optimization: Improve product listing animation.
- Optimization: Prevent flickering dropdown on main-navigation.
- Optimization: Make accordion items stay open when another item is opened on mobile.
- Bugfix: CMS-Element Divider not showing up.
- Bugfix: Gallery initialization with element alignment.

# 3.5.2
- Optimization: Improvement of the theme configuration.
- Optimization: Improve pointer events on CMS-Elements with links.
- Optimization: Add query to manufacturer link to open only in new tab if url contains '://'. 
- Optimization: Add new option to CMS-Element "Features" to not use an icon.
- Optimization: Alignment CMS-Element "Features".
- Bugfix: Fix wrong for-attribute on guest-auth zip-code field to make floating labels work.
- Bugfix: Removed `overflow:hidden` from CMS-Element "Search Banner".
- Bugfix: Search field styling in CMS-Element "Search Banner".
- Bugfix: Horizontal image alignment in CMS-Elements.

# 3.5.1
- Bugfix: Border-bottom on sticky header.

# 3.5.0
- Feature: Added CMS-Element "Search Banner". | Pro Version
- Feature: Added CMS-Element "Text Banner". | Pro Version
- Feature: Added CMS-Block "Image gallery and buy box with product name & manufacturer logo & description" | Pro Version
- Feature: Added CMS-Grid Two row layout - 5/5/5/5/5, Two row layout - 2/2/2/2/2. | Pro Version
- Feature: Added new configurations to set horizontal alignment to teaser and image text slider cms elements. | Pro Version
- Feature: Added new configuration to display the product description in buybox.
- Feature: Added new configuration to display the product description tabs as accordions.
- Feature: Added new configurations to enable/disable header shadows on hover.
- Feature: Added new configurations to enable/disable header shadows on sticky.
- Feature: Added new configuration to set background color to gallery images on product details page.
- Feature: Added new configuration to specify the number of lines for the product name in listings.
- Feature: Added new configuration to specify position of the product name on product details pages.
- Feature: Added new configuration to specify a header height for sticky header.
- Feature: Added new configuration to enable/disable gallery sticky functionality.
- Optimization: Refactored CMS-Element "Teaser" and "Text-Image-Slider".
- Optimization: Added responsive font size to teaser element.
- Optimization: Added element border radius value to gallery images on product details page.
- Optimization: Improved storefront styling.
- Optimization: Avoid transparent body bg color due to contrast functions.
- Optimization: Changed twitter icon to X, lol.
- Optimization: Set breadcrumbs to full width on full-width-header mode.
- Optimization: Scrollable offcanvas cart items only without amazon checkout and paypal express buttons.
- Optimization: Made product custom fields available, if cms buy-box is placed on landing, listing or shop page layouts.
- Optimization: Added `Plugin` to Javascript plugin class names to fit shopware naming convention.
- Bugfix: Prevent Division-By-Zero Error because of missing `purchaseSteps`, if product is created by api.
- Bugfix: Sidebar mouseover.
- Bugfix: Search default on contrast headers.
- Bugfix: Main navigation alignment on multi lined headers.
- Bugfix: Product image border breaks product image ratio.
- Bugfix: Modal close button on dark theme mode.
- Bugfix: Icon alignment of CMS-Element "Features".

# 3.4.0
- Feature: Added new cms configuration to specify spacings in our cms grids. | Pro Version
- Feature: Added new configuration to set cover image to 100% on product detail gallery in grid mode.
- Feature: Added new configuration to dynamically set the grid cols on product detail gallery in grid mode.
- Feature: Added new configuration to hide filter options which provide no results.
- Feature: Added new configuration to change the limit of directly visible payment/shipping methods in checkout.
- Feature: Added new configuration to change payment/shipping description length in checkout.
- Feature: Added new configuration to add vimeo videos to video tab on product detail pages.
- Feature: Added new configuration to enable/disable service-navigation in top bar.
- Feature: Added new configuration to enable/disable product description title.
- Feature: Added new configuration to enable/disable buybox sticky functionality.
- Optimization: Fixes misrepresentations related to sticky and overflow hidden.
- Optimization: Improvement of the theme configuration.
- Optimization: Removed a-tag from manufacturer if url is empty. 
- Optimization: Keep the quotes for font-families in interpolation.
- Optimization: Expanded product slider.
- Optimization: Mobile content scroller headlines.
- Optimization: Removes dependency on full width option of breadcrumbs for vertical spacing.
- Optimization: Refactored footer newsletter column.
- Optimization: Refactored sticky sidebar functionality.
- Compatibility: Added compatibility with NenoProductDetailConfigurator.
- Bugfix: Category image and category fallback image position configuration.
- Bugfix: Text image slider initialization on vertical axis and lazy image loading.
- Bugfix: `display-x` classes in sliders.
- Bugfix: border-radius main-navigation.
- Bugfix: Remove `only` attribute on sw_include for twigStringLoaderExtension template.

# 3.3.0
- Feature: Added new configurations to set product layout type for search result page, wishlist and cross-selling.
- Feature: Added new configurations to animate product boxes on listing pages.
- Feature: Save consent for datasheets on product details datasheets tab when using Google Docs viewer in a cookie in the cookie consent manager.
- Feature: Added new configurations `loop` and `rewind` to image text slider element. | Pro Version
- Optimization: Add styling to CMS-Grid two row layouts.
- Optimization: Added max sizes to menu-scroller arrows.
- Optimization: Initialization of the `js-nav-scroller`.
- Optimization: `js-nav-scroller` class names have been changed to better match its elements.
- Optimization: Renamed class `.main-navigation-item` to `main-navigation-dropdown-item` to better match its functionality.
- Optimization: Improves expanded product slider feature.
- Optimization: Renamed cms element `zenit-breadcrumb` into `zen-breadcrumb` to fit naming conventions.
- Optimization: Renamed cms element `zenit-features` into `zen-features` to fit naming conventions.
- Bugfix: Ionicons stack icon.
- Bugfix: Fixes the display of checkboxes in the theme configuration.
- Bugfix: Prevents horizontal scrolling on cross-selling with extra large product box gutters.
- Bugfix: js-nav-scroller broke alignment of main-navigation.
- Bugfix: Added missing header contrast colors to top bar elements in two line headers.
- Bugfix: Fixes NEXT-29203 | CMS page: css classes are not displayed anymore.

# 3.2.0
- Feature: Added CMS-Element "Code" for writing HTML/Twig, CSS and JS. | Pro Version
- Feature: Added Image Text Slider images sorting with Drag & Drop. | Pro Version
- Feature: Added CMS-Element "Divider". | Pro Version
- Feature: Added Javascript navigation scroller for main-navigation. 
- Feature: Added new configuration which lets you set an absolute positioned header with different contrast colors.
- Feature: Added new configuration to set mobile tabs display mode to content scroller on product detail page.
- Feature: Added consent for datasheets on product details datasheets tab when using google docs viewer.
- Feature: Added CMS-Layouts Simple layout - 4/8, Simple layout - 8/4, Simple layout reversed - 4/8 and Simple layout reversed - 8/4. | Pro Version
- Feature: Added CMS-Layout Advanced layout grid, Advanced layout grid reversed. | Pro Version
- Feature: Added CMS-Grid Two row layout - 6/6, Two row layout - 4/4/4, Two row layout - 3/3/3/3. | Pro Version
- Feature: Made CMS-Element Placeholder available in elements modal. | Pro Version
- Compatibility: Added compatibility with Shopware 6.5.1.0.
- Optimization: Image Text Slider improvements. | Pro Version
- Optimization: Added new configuration to switch text-editor to code-editor in CMS Element Image Text Slider to prevent sanitizing. | Pro Version
- Optimization: Added new prop `enableTransparentBackground` to text editor.
- Optimization: Improves disabled quantity selections for digital products.
- Optimization: Changed type of variable `zen-layout-container-width` to int.
- Optimization: Breadcrumbs you are here text schema markup.
- Optimization: Improvement of the theme configuration.
- Bugfix: Image slider border-radius and zoom on hover overflow issue.
- Bugfix: Hide preloader when Back-Forward Cache is used.
- Bugfix: Fixed section visibility on product detail pages.
- Bugfix: Fixed display of category image thumbnails when using section background image fallback. 

# 3.1.0
- Feature: Added new configurations to add payment & shipping logos columns.
- Feature: Added new configurations to add custom column 2 and custom column 3.
- Feature: Added new configuration to set header bottom.
- Feature: Added new configurations to change slider controls visibility on mouseover.
- Feature: Added sticky checkout buttons on offcanvas cart.
- Feature: Added new configuration to remove product features headline.
- Feature: Added new configuration to set the position of the product features.
- Feature: Added dynamic url fields to cms image slider and teaser element.
- Optimization: Use thumbnails for displaying category image.
- Optimization: Remove deprecated config field `zen-contrast-threshold`.
- Optimization: Improvement of the theme configuration.
- Optimization: Storefront optimizations.
- Optimization: Remove fixed position from flyout navigation.
- Optimization: Improve transition of parallax mousemove animation. 
- Optimization: Remove unnecessary title tags from navigation.
- Optimization: Centering top bar marketing slider if logged in.
- Optimization: Show top bar marketing slider on mobile.
- Bugfix: horizontal scrolling because of account-drop-down.
- Bugfix: Size of the video embed in video tabs.
- Bugfix: Filter panel item icon.
- Bugfix: Thumbnails slider in quickview.
- Bugfix: Added xxl viewport to image slider carousel mode.

# 3.0.0
- Feature: Added new configuration to change checkout layouts.
- Feature: Added new configuration to activate/deactivate checkout steps.
- Feature: Added new configuration to change checkout terms of service position.
- Feature: Added new configurations to change configurator colors.
- Feature: Added new configurations to style configurator non-combinable properties.
- Feature: Added new configurations to change scroll-animation delay and scroll-animation duration.
- Feature: Added sticky sidebar functionality.
- Feature: Added new configuration to change global link color.
- Feature: Added new configuration to change delivery status indicator appearance.
- Compatibility: Established compatibility with Shopware 6.5.0.0.
- Compatibility: SW6.5 - Removal of old icons in administration.
- Compatibility: New selector to open ajax modal.
- Compatibility: Add xxl viewport as inheritance of xl viewport on cms grid of five.
- Compatibility: Add deprecations.
- Compatibility: Maintain quantity select.
- Compatibility: Maintain service menu in top bar.
- Compatibility: Fix dispatch event in ZenBuyWidgetQuantityInput.
- Compatibility: Fix text slider in new offcanvas.
- Compatibility: Moved class `.has-product-actions` from product-box to body to prevent overwriting.
- Optimization: Namespaced data attributes to help distinguish own functionality from shopware core and third parties. E.g. `data-sticky` renamed to `data-zen-sticky`.
- Optimization: Revision of backdrop loader colors.
- Optimization: Prevent flickering of CMS animations. 
- Optimization: Unified product image display for layout type 'big image'.
- Optimization: Unified product image display for display mode 'contain'.
- Optimization: Reduces cumulative layout shift on product details page.
- Optimization: Reduces height of placeholder in gallery.
- Optimization: Improves the positioning of the dropdown when using the menu scroller.
- Optimization: Configuration to set number of products per row is now available in search and wishlist pages.
- Optimization: Improves form-text color in footer newsletter column.
- Optimization: Added missing id for aria-labelledby on dropdown menu.
- Optimization: Ensure content fills remaining height of the page on checkout.
- Optimization: Improves the selection of the appropriate input field even if label and input field are not adjacent.
- Optimization: Add floating labels on textareas.
- Optimization: Improvement of the theme configuration.
- Optimization: Elements above the header are now taken into account on the sticky header.
- Optimization: Improve tooltip title of collapse top bar.
- Optimization: Lighthouse improvements.
- Optimization: Changed a-Element to button-Element on basic-captcha-content-refresh.
- Bugfix: Missing background on quantity input buttons in listings image overlay option.
- Bugfix: Prevents division by zero twig warning on product box srcset calculation.
- Bugfix: Custom tab titles.
- Bugfix: Fix sticky header viewport selection.
- Bugfix: Checkout additional comment breaks layout of finish page.

# 2.13.2
- Feature: Added new configuration to activate/deactivate product configurator tooltips.
- Optimization: Prevent flickering of CMS animations.
- Optimization: Display of the product images aspect ratio revised.
- Optimization: Refactored `navigationMaxDepth` for flyout navigation and dropdown navigation.
- Optimization: Fix edge cases for product overlay actions.
- Optimization: Improvement of the display of product actions.
- Optimization: Improvement of the theme configuration.
- Optimization: Display buy-button for articles with advanced prices in quickview.
- Optimization: Set cms element styles by class instead of id so that they can be overwritten more easily.
- Optimization: Optimized cms animation delay and duration.
- Optimization: Removed class `.small-gutters`, because it's not used anywhere.
- Optimization: Gallery slider display mode contain now behaves like object-fit contain.
- Compatibility: Added compatibility with BstCatalogMode6 on quickview.
- Bugfix: Pagination radius if hide disabled option is active.
- Bugfix: Prevents division by zero twig warning on product box srcset calculation.
- Bugfix: Custom tab titles.
- Bugfix: openThemeConfig function in dashboard.
- Bugfix: Fix alignment of main navigation when dropdown navigation is configured.

# 2.13.1
- Feature: Added label before product number in listing.
- Optimization: Added checkout aside sticky functionality.
- Optimization: Added button margin for stacked buttons on image-slider content.
- Optimization: Removed shadow and border-bottom on absolute positioned header.
- Optimization: Changed `page.product.stock` to `page.product.stock`.
- Optimization: Optimized Maintenance page.
- Compatibility: Specify configurator-option-label sizes to product-detail page.
- Bugfix: Breadcrumb pretext color on cms hero section.
- Bugfix: Checkout-additional breaks checkout layout.
- Bugfix: Fix responsive font-size in image slider.
- Bugfix: Quantity input width on mozilla firefox.

# 2.13.0
- Feature: Revision of the shopping experience feature element with new settings. | Pro Version
- Feature: Styled cart and checkout.
- Feature: Added checkout steps.
- Feature: Added expandable sidebar-navigation.
- Feature: Add CSS root-variables in storefront.
- Feature: Added new configuration to add rating placeholder in listings and details page.
- Feature: Added new configuration to set dropdown navigation font-weight and font-size.
- Feature: Added new configuration to change filter font size, font weight and font weight on active.
- Feature: Added new icon sets `fluent-outline`, `fluent-solid`, `heroicons-outline`, `heroicons-solid`, `iconic`, `ionicons-outline`, `ionicons-solid`.
- Feature: Added new configuration to switch home-entry active status.
- Feature: Added new configuration to control display of product actions on mobile viewports.
- Feature: Added new configuration to change colors of light buttons.
- Feature: Added new configuration to change colors of dark buttons.
- Feature: Added pack-unit to quantity input fields.
- Feature: Added new configuration to add pre-text to breadcrumbs.
- Compatibility: SW6.5 - backward-compatibility code - Added variable `dataBsToggleAttr` to replace `data-toggle` with `data-bs-toggle`.
- Compatibility: SW6.5 - backward-compatibility code - Added variable `dataBsTargetAttr` to replace `data-target` with `data-bs-target`.
- Compatibility: SW6.5 - backward-compatibility code - Added variable `formSelectClass` to replace `custom-select` with `form-select`.
- Compatibility: SW6.5 - backward-compatibility code - Added variable `gridNoGuttersClass` to replace `no-gutters` with `g-0`.
- Compatibility: SW6.5 - backward-compatibility code - Added variable `formRowClass` to replace `form-row` with `row g-2`.
- Compatibility: SW6.5 - backward-compatibility code - Added variable `marginStartClass` to replace `.ml-*` to `.ms-*`.
- Compatibility: SW6.5 - backward-compatibility code - Added variable `marginEndClass` to replace `.mr-*` to `.me-*.`.
- Compatibility: SW6.5 - backward-compatibility code - Replaced `data-placement` with `data-bs-placement`.
- Compatibility: SW6.5 - backward-compatibility code - Replaced `header-gutters` with `gx-1`.
- Compatibility: SW6.5 - backward-compatibility code - Replaced `btn-block` class with `d-grid` wrapper instead.
- Compatibility: SW6.5 - backward-compatibility code - Removed `input-group-append` wrapper and uses elements as direct children of `input-group`.
- Compatibility: SW6.5 - backward-compatibility code - Used the breakpoint itself instead of the next breakpoint in `media-breakpoint-down()`.
- Compatibility: SW6.5 - backward-compatibility code - Used the breakpoint itself instead of the next breakpoint in `media-breakpoint-between()` for second parameter.
- Compatibility: SW6.5 - backward-compatibility code - Renamed `color-yiq()` function and related variables to `color-contrast()` as it’s no longer related to YIQ colorspace.
- Compatibility: SW6.5 - backward-compatibility code - Renamed `$custom-select-background` into `$form-select-bg`.
- Compatibility: SW6.5 - backward-compatibility code - Renamed `$custom-select-light-background` into `$form-select-light-bg`.
- Compatibility: SW6.5 - backward-compatibility code - Removed `$form-select-light-bg`.
- Compatibility: SW6.5 - backward-compatibility code - Replaced `$custom-select-*` variable with `$form-select-*`.
- Compatibility: SW6.5 - backward-compatibility code - Replaced `border-right-radius` mixin with `border-end-radius`.
- Compatibility: SW6.5 - backward-compatibility code - Replaced `border-left-radius` mixin with `border-start-radius`.
- Compatibility: SW6.5 - backward-compatibility code - Replaced `sr-only` with `visually-hidden` in SCSS.
- Compatibility: SW6.5 - backward-compatibility code - Added additional `order-`classes in bootstraps utility API.
- Compatibility: SW6.5 - backward-compatibility code - Added new configuration `zen-contrast-threshold`.
- Compatibility: SW6.5 - backward-compatibility code - Deprecated configuration `zen-min-contrast-ratio`.
- Compatibility: SW6.5 - backward-compatibility code - Migrated all jQuery implementations to Vanilla JavaScript in all JavaScript plugins.
- Compatibility: SW6.5 - backward-compatibility code - Added `xxl` viewport to viewport detection in all JavaScript plugins.
- Compatibility: SW6.5 - backward-compatibility code - Renamed embed helpers to ratio helpers.
- Compatibility: SW6.5 - change tooltip instances to prevent multiple instances per element.
- Compatibility: SW6.4.19.0 - removed quantity selection for digital products.
- Compatibility: Remove override of cookiePermission plugin to increase compatibility.
- Optimization: Improved btn paddings.
- Optimization: Wishlist Button always visible on wishlist added products.
- Optimization: Removed content from cms grid placeholder element, so that it could be used as column spacer by default. 
- Optimization: Namespaced data attributes to help distinguish own functionality from shopware core and third parties. E.g. `data-sticky` renamed to `data-zen-sticky`.
- Optimization: Footer link hover underlined.
- Optimization: Add footer color to h-tags.
- Optimization: Decrease the offset of the cms content animations to decrease the delay.
- Optimization: Improvement of the theme configuration.
- Optimization: Decrease footer bottom default font-size and font-weight.
- Optimization: Improves the initial loading of the text slider with `document.fonts.ready`.
- Optimization: Added footer payment shipping logos check.
- Optimization: Improved footer newsletter input.
- Optimization: Add compatibility between menu scroller and dropdown navigation.
- Optimization: Consider portrait images in thumbnails src-sets, because the width of their thumbnails is less than the calculated thumbnail specification.
- Optimization: Force hide the preloader if there is any javascript error.
- Optimization: Optimized cms grid breakpoint classes.
- Optimization: Set default vertical gutter for cms grids, because this is the most common use case. Overwrite gutters with gutter classes, which are shipped with the theme.
- Optimization: Changed sticky header animation from css-transform to margins because of nested position fixed stacking context.
- Optimization: Removed cms element placeholder text in selects to prevent empty values.
- Optimization: Many styling improvements.
- Optimization: Removed deprecated theme-configuration fields `zen-layout-container-main-padding-top`, `zen-layout-container-main-padding-bottom`, `zen-category-filter-seperator`, `zen-container-color`, `zen-product-listing-img-width`, `zen-product-listing-img-height`, `zen-sidebar-navigation-max-depth`.
- Optimization: Prefixing custom scss variables.
- Optimization: Refactored gallery grid and gallery scroller.
- Bugfix: Gallery slider placeholder.
- Bugfix: Alignment of product box reviews on default position.
- Bugfix: Prevents subsequent slides from overlaying during initial rotation on text-slider.
- Bugfix: Sticky Header on overlaying headers.
- Bugfix: Hide manufacturer on cms product details page.
- Bugfix: Fix NEXT-24201 for slider element. | Pro Version
- Bugfix: Fix footer configurations.

# 2.12.2
- Bugfix: Reset using iconSets.key in theme.json, because it does not work properly on shopware cloud.

# 2.12.1
- Bugfix: Fix iconSets paths in theme.json.

# 2.12.0
- Feature: Added new configuration to hide disabled-buttons on pagination.
- Feature: Added new configuration to set footer content font family.
- Feature: Added new configuration to set footer bottom font size and font weight.
- Feature: Added new configuration to set product details name font family.
- Feature: Added new configuration to set product details price font family.
- Feature: Added new configuration to set main navigation border top to full width.
- Feature: Added new configuration to display gallery thumbnail navigation with product aspect ratio.
- Feature: Added new configuration to choose between different gallery thumbnail navigation styles.
- Feature: Added new configuration to add home-link to breadcrumbs.
- Feature: Added new configuration to set gallery zoom factor.
- Feature: Added new configuration to set manufacturer position on product detail and quickview.
- Feature: Added new icon sets `iconly bulk`, `iconly broken`, `iconly light-outline`
- Feature: Added new configuration to set top bar border bottom.
- Feature: Using bootstrap responsive font size mixin in scss.
- Optimization: Prepared icons for icon cache.
- Optimization: Remove breadcrumbs item active state.
- Optimization: Added layout container width for thumbnail utility.
- Optimization: Improvement of the theme configuration.
- Optimization: Improves the center alignment of category navigation when they wrap into new line.
- Optimization: Added columns on blocks for thumbnails sizes.
- Optimization: Added sticky header auto-hide option on multi-lined headers on mobile devices.
- Optimization: Removed search button hover state.
- Optimization: Floating labels compatibility.
- Bugfix: Removed duplicate itemprops.
- Bugfix: Fixes the duplicate display of themes in the dashboard.
- Bugfix: Tooltips delay on card actions.
- Bugfix: Renamed icon `eye` into `eye-open` to fix quickview icon.

# 2.11.0
- Feature: Added new theme fonts 'Comfortaa', 'Frank Ruhl Libre', 'Montserrat', 'Open Sans', 'Playfair Display', 'Raleway', 'Signika', 'Vidaloka'.
- Feature: Set default value for 'Load fonts from Google' to `false` and implement fonts as theme fonts.
- Feature: Added new configuration to set ordernumber `color`, `font-size`, `weight` and `text-align` in cards.
- Feature: Added new configuration to set variant characteristics `color`, `font-size`, `weight` and `text-align` in cards.
- Feature: Added new configuration to set cheapest price `color`, `font-size`, `weight` and `text-align` in cards.
- Feature: Added new configuration to set price unit `color`, `font-size`, `weight` and `text-align` in cards.
- Feature: Added expiration date to optional google fonts cookie.
- Feature: Added ability to close overlay search and expandable search by escape key.
- Feature: Footer newsletter recaptcha fields visibility changes on newsletter input value.
- Optimization: Image aspect ratio included in the calculation of best srcset-sizes for portrait product card images based on different theme settings.
- Optimization: Improvement of the theme configuration.
- Optimization: Added additional query for displaying manufacturer tab on product detail pages.
- Optimization: Set horizontal auto spacings for locked cms product_detail default page.
- Optimization: Set horizontal auto spacings for locked cms product_list default pages.
- Optimization: Prevents issue NEXT-9276 | shopping experiences slider imprecise.
- Optimization: Loading product detail features icons.
- Optimization: Added custom component to display config alerts.
- Optimization: Reinitialisation of tooltip utilities on product slider initialization.
- Bugfix: Improve floating labels to prevent them from overlaying payment-inputs.
- Bugfix: Prevents the cms crossselling slider from expanding the page horizontally if no horizontal block spacing is specified. 
- Bugfix: Missing end-div on slider vertical alignment.
- Bugfix: Footer captcha border radius right.

# 2.10.3
- Compatibility: Shopware 6.4.17

# 2.10.2
- Optimization: Add `ProductCrossSellingStreamCriteriaEvent` to enable cover switch on crossselling stream.
- Bugfix: Prevent recreating bootstrap grid for grid of five.

# 2.10.1
- Optimization: Top bar dropdown active status color on buttons.
- Optimization: Representation of the switch-fields in theme configuration. | Pro Version
- Bugfix: CMS teaser element could not be positioned. | Pro Version

# 2.10.0
- Feature: Added new configuration option to set product listings with grid of five.
- Feature: Added new configuration to set border-radius on each item for image text slider element. | Pro Version
- Feature: Added new configuration to set border-radius for image text slider element. | Pro Version
- Feature: Added new configuration to set breadcrumbs vertical spacing.
- Compatibility: Shopware 6.4.15 - Fix twig source path for icons.
- Compatibility: Moved overlay-search close button from `layout_header_search_input_group` to `layout_header_search` to establish compatibility with SasBlogModule.
- Compatibility: Added compatibility with NetiNextAccessManager on quickview.
- Optimization: Gallery grid margins.
- Optimization: Improvement of the theme configuration.
- Optimization: CMS Teaser config translation.
- Optimization: Added input focus placeholder color.
- Optimization: Empty check for service navigation footer bottom position.
- Optimization: Bind gallery scroller and gallery grid to gallery-slider element instead of cms block.
- Optimization: Sticky header hide animation.
- Optimization: Added additional query for footer social icons.
- Optimization: Refactored sw_includes and sw_extends `@Storefront` of new template files.
- Optimization: Checkout minimal header layout logo position.
- Optimization: Passing current slide to gallery scroller zoom modal. 
- Optimization: Optimized logo vertical position on mobile single lined header and default search.
- Optimization: Added Fallback for `zen-layout-container-width` in `box.html.twig`.
- Optimization: Improve cover switch image calculation.
- Bugfix: Gallery scroller thumbnail navigations.
- Bugfix: Fonts are now loaded even if they are not specified in quotes.
- Bugfix: Product card name text transform.
- Bugfix: Crossselling on CMS pages causes horizontal scrolling under certain circumstances.

# 2.9.2
- Compatibility: Shopware 6.4.15 - CMS element data resolving mismatch | Pro version
- Compatibility: Shopware 6.4.15 - Add twig blocks for image to the product box card template
- Compatibility: Shopware 6.4.15 - Main variant visualisation NEXT-17544
- Bugfix: Fixed gallery scroller and gallery grid.

# 2.9.1
- Feature: Allows Twig-Syntax for variables and snippets in custom global tab | Pro version
- Bugfix: Fixed `Unknown "template_from_string" function` error. | Cloud version

# 2.9.0
- Feature: Added Quickview variant switch. | Pro version
- Feature: Allows Twig-Syntax for variables and snippets in own theme custom fields and own shopping experience elements. | Pro version
- Feature: Added new configuration to show / hide youtube video placeholder thumbnails.
- Optimization: Allows HTML-Syntax in shopping experience Teaser text.
- Optimization: Refactored quickview.
- Optimization: Close quickview on open offcanvas cart event.
- Optimization: Improves the animation when opening the Quickview modal.
- Optimization: Floating labels on focus event for better usability.
- Optimization: Optimized pagination pills at larger base font size.
- Optimization: Set Product card price unit font size to description font size.
- Optimization: Added header padding to minimal header in checkout.
- Optimization: Cover switch hover.
- Optimization: SCSS Code reformatting.
- Optimization: Button hover improvements.
- Optimization: Product listing actions.
- Optimization: Refactored sw_includes of new template files.
- Bugfix: Fixed font size of product numbers in listings.
- Bugfix: Check for filter panel item in open filter plugin.

# 2.8.0
- Feature: Added new configuration to set mobile header to single line.
- Feature: Added new configuration to add custom javascript in head.
- Feature: Added new configuration to set tabs display mode to content scroller on product detail page.
- Feature: Added new configuration to set custom srcset-sizes for product card images.
- Feature: Calculating best srcset-sizes for product card images based on different theme settings.
- Feature: Refactored listing product actions.
- Feature: Added new configuration to set wishlist button position over gallery.
- Feature: Added new configuration to ask for consent for videos on product details video tab.
- Feature: Added new configuration to change image gallery fallback alt- and title-tags.
- Optimization: Improved sticky header offset to start sticky header without any offset.
- Optimization: Improved the output of custom CSS and JS code.
- Optimization: Refactored buttons styles.
- Optimization: Lazy loading made available for sliders in cover mode only, otherwise there may be problems with the height calculation.
- Optimization: Styling of thumbnail controls of the image gallery.
- Optimization: Improves styling of footer newsletter logo.
- Optimization: Improves styling of sidebar navigation.
- Optimization: Improves gallery slider subpixels.
- Optimization: Extend `base_body_script` instead of `base_script_hmr_mode`
- Optimization: Styling account menu dropdown.
- Optimization: Added text-decoration underline to cms hero section light-text and dark-text.
- Optimization: Display hover background-color for main navigation items on transparent header.
- Optimization: Moved block `zen_base_webfont` to meta.html.twig to load webfont loader earlier and avoid FLOUT.
- Optimization: Improved spacings on product detail page.
- Optimization: Improved styling of offcanvas menu actions.
- Optimization: Set CMS section sidebar row to `no-gutters`.
- Optimization: Improved product slider animation on mobile.
- Optimization: Improved various representations in the storefront.
- Bugfix: Fixes rounded corners in the dropdown menu.
- Bugfix: Fixes breadcrumbs position under hero-section.
- Bugfix: Fixes styling of structure category breadcrumb item.
- Bugfix: Fixes a javascript error with the expanded display of filters.
- Bugfix: Set dynamic line-height for headings.
- Bugfix: Displaying Gallery & Buybox element outside a product page.

# 2.7.2
- Feature: Added new configuration to set max items to show slider dots.
- Optimization: Adjusted horizontal spacing in footer to keep content aligned.
- Optimization: Make footer grid based on layout-container spacings.
- Optimization: Account aside navigation.
- Optimization: Adjustment of account colors for dark layouts.
- Optimization: Prevent scroll-up button overlaying footer bottom navigation.
- Compatibility: Changed `margin` to `padding` in `cms-section-block-container.html.twig`.

# 2.7.1
- Compatibility: Establish compatibility with NetiNextEasyCoupon.
- Optimization: Changed deprecated `@elseif` into `@else if` in SCSS.
- Bugfix: Changed Fallback Value for SCSS-Variable `$zen-product-listing-card-big-img-height`.

# 2.7.0
- Compatibility: Shopware 6.4.13 - Change variable `maxPurchaseThreshold` to `selectQuantityThreshold`.
- Compatibility: Shopware 6.4.13 - Set `selectQuantityThreshold` value to 100.
- Compatibility: Shopware 6.4.13 - Merge Issue NEXT-9423 "Switch input type on high quantity" with own input type functionality.
- Feature: Added new configuration to set product details spacings.
- Feature: Added "Sen" as new theme font.
- Feature: Added new configuration to activate/deactivate product image background-color in listing pages.
- Feature: Added new configuration to activate/deactivate product image background-color on product details page.
- Optimization: Various CSS styling improvements.
- Optimization: Descenders in the product name were partially cut off.
- Optimization: Improvement of the dropdown-menu appearance.
- Optimization: Improvement of designations in the theme.json.
- Optimization: Added total review link to reviews in headline area position.
- Optimization: Optimizes product details properties table word-break.
- Optimization: Optimized display of the footer bottom.
- Bugfix: Fixes a display problem of the CMS product detail page in connection with the sticky-buybox.
- Bugfix: Fixes the problem when opening the cookie settings offcanvas from another offcanvas.

# 2.6.2
- Feature: Added new configuration to set dropdown navigation max depth.
- Optimization: Added position constants for CMS slots. | Pro version
- Optimization: Added additional node check in overlay search javascript plugin.
- Bugfix: Change route for dashboard update links | Pro version

# 2.6.1
- Bugfix: Add custom fields values for products per row on listing render event.

# 2.6.0
- Feature: Added gallery grid as new display mode for product details gallery.
- Feature: Added new configuration to show subnavigation as dropdown menu.
- Feature: Added new configuration to style dropdown menu.
- Feature: Added new custom fields to add custom css-classes per category.
- Feature: Added new custom fields to add navigation badges.
- Feature: Added new custom fields to change products per row per category.
- Feature: Added new configuration to show or hide quantity selection.
- Feature: Added new configuration to show or hide service navigation in footer bottom.
- Feature: Added new configuration to change footer columns header collapse icon.
- Feature: Added new configuration to change base text and headings font weights.
- Feature: Added new configuration to change max image height of product box layout big image.
- Feature: Improves the address display in the checkout.
- Optimization: Google Fonts Cookie consent manager integration. | Pro-Version
- Optimization: Improvement of the manufacturer tab query.
- Optimization: Improvement of designations in the theme.json.
- Optimization: Moved block `base_noscript` as direct child of body.
- Optimization: Added space between breadcrumbs and buybox.
- Optimization: Added breadcrumb divider color.
- Optimization: Base slider dot navigation hidden on mobile devices.
- Optimization: Removed unused classes `img-darken` and `img-lighten`.
- Optimization: Changed Cross-Selling `displayMode` from `minimal` to `standard`.
- Optimization: Changed Cross-Selling `boxLayout` from `standard` to `minimal`.
- Optimization: Added extra space between listing and pagination when space between product boxes is small.
- Optimization: Improved position of badges and ratings in the product box.
- Optimization: Added disabled styles for disabled filter options.
- Optimization: Menu scroller is now clickable even when the flyout menu is open.
- Optimization: Improves the display of the quantity input in the listing.
- Bugfix: Re-register floating labels on checkout change billing/shipping address pseudo modals
- Bugfix: Fixed gallery scroller on cms block "Image gallery and buy box with product name & manufacturer logo" | Pro Version
- Bugfix: Set Footer Newsletter Basic Captcha max-width.
- Bugfix: Extends the header opacity query to display correctly for inherit value.
- Bugfix: Extends the navigation opacity query to display correctly for inherit value.

# 2.5.4
- Compatibility: Compatibility with Shopware 6.4.11
- Feature: Provide default Media for new CMS Blocks | Pro version
- Optimization: Removed new configuration `zen-sidebar-navigation-max-depth`o set sidebar navigation max depth. Instead, the configuration from the sales channel is used.
- Bugfix: Fix `is--sidebar` class when displaying open filters.

# 2.5.3
- Optimization: Improvement of the preloader Javascript initialization
- Optimization: Add device detection for the `load`-event to use iOS supported `pageshow`-event
- Optimization: Add device detection for the `unload`-event to use iOS supported `pagehide`-event
- Bugfix: Backward compatibility for strict comparison in twig

# 2.5.2
- Info: Child-theme update available (optional) - Visit https://childtheme-update.zenit.design to learn more about child theme updates.
- Feature: Added shopping experience grid gutter classes.
- Feature: Added new configuration to enable or disable product basic price on product boxes.
- Feature: Added new configuration to enable or disable product cheapest price on product boxes.
- Feature: Added new configuration to display product price discount on product boxes on different positions.
- Optimization: Revised display of prices in the product boxes.
- Optimization: Added blocks to `meta.html`.
- Optimization: Refactored `meta.html`.
- Optimization: Changed `textMarginRight` and `textMarginLeft` default value to 10%.
- Optimization: Corrected the Font weight `Black - 900` Option label in the shopping experience teaser element configuration.
- Optimization: Improvement of the shopping experience teaser element overflow in administration.
- Optimization: Improvement of the shopping experience image text slider element dot outside in administration.
- Optimization: Allow html-output in shopping experience feature element.
- Optimization: Revised overlays of certain predefined blocks on header overlay.
- Optimization: Revision of the styles for product detail page and cms product detail page.
- Optimization: Increase scaling of responsive-font-scaling for image text slider element display-classes.
- Optimization: Overhaul of Zoom Modal background color.
- Optimization: Removes bottom margin of breadcrumbs container.
- Optimization: Improvement of the product rating styles in listings.
- Optimization: Improvement of the theme configuration.
- Optimization: Improvement of the flyout menu.
- Bugfix: Fixed display of the product image placeholder in Firefox.
- Bugfix: Fixes the display issue in the Zoom Gallery Modal.
- Bugfix: Corrects a query to display paddings in the product box card body.

# 2.5.1
- Optimization: Added additional check to render a category image header hero section.
- Optimization: Improvement of the main navigation menu-scroller fadeout area.
- Optimization: Improvement of image text slider gap if multiple items are displayed. | Pro Version
- Optimization: Improvements to the teaser element display. | Pro Version
- Optimization: Improvements to the teaser element hover animations if no url is set. | Pro Version
- Optimization: Improvement of the `has-bg-opcaity`-class condition.
- Bugfix: Fixed image text slider element default values on image upload. | Pro Version

# 2.5.0
- Info: Child-theme update available (optional) - Visit https://childtheme-update.zenit.design to learn more about child theme updates.
- Feature: Added CMS-Element "Image Text Slider" | Pro Version
- Feature: Added CMS-Element "Image gallery and buy box with product name & manufacturer logo" | Pro Version
- Feature: Added CMS-Element "Teaser" | Pro Version
- Feature: Added CMS-Block "Teaser - One column" | Pro Version
- Feature: Added CMS-Block "Teaser - Two column" | Pro Version
- Feature: Added CMS-Block "Teaser - Three column" | Pro Version
- Feature: Added CMS-Block "Teaser - Four column" | Pro Version
- Feature: Added CMS-Block Grid "One column"
- Feature: Added CMS-Block Grid "Two columns"
- Feature: Added CMS-Block Grid "Three columns"
- Feature: Added CMS-Block Grid "Four columns"
- Feature: Added CMS-Block Grid "Five columns"
- Feature: Added CMS-Block Grid "Six columns"
- Feature: Added CMS-Block Grid "Two columns - 9/3"
- Feature: Added CMS-Block Grid "Two columns - 3/9"
- Feature: Added CMS-Block Grid "Two columns - 8/4"
- Feature: Added CMS-Block Grid "Two columns - 4/8"
- Feature: Added CMS-Block Grid "Two columns - 7/5"
- Feature: Added CMS-Block Grid "Two columns - 5/7"
- Feature: Added CMS-Block Grid "Three columns - 6/3/3"
- Feature: Added CMS-Block Grid "Three columns - 3/3/6"
- Feature: Added CMS-Block Grid "Three columns - 3/6/3"
- Feature: Added CMS-Block Grid "Three columns - 2/8/2"
- Feature: Added CMS-Block Layout "Simple layout" | Pro Version
- Feature: Added CMS-Block Layout "Simple layout reversed" | Pro Version
- Feature: Added CMS-Block Layout "Simple teaser layout" | Pro Version
- Feature: Added CMS-Block Layout "Simple teaser layout reversed" | Pro Version
- Feature: Added notification for child theme updates | Pro Version
- Feature: Added custom fields for landingpages | Pro Version.
- Feature: Added new configuration to show TikTok icon as social media icons.
- Feature: Added new configuration to set sidebar navigation max depth.
- Feature: Added new configuration to set flyout navigation max depth.
- Feature: Display variant characteristics in quickview template.
- Feature: Main Navigation Menu-Scroller.
- Optimization: Added theme dashboard to settings extensions menu.
- Optimization: Added theme settings to settings extensions menu.
- Optimization: Moved configuration of product image overlay to tab general.
- Optimization: Optimized product image color-overlay to better prevent pixel inaccuracies.
- Optimization: Added background-color for magnifier zoom image due to auf transparent pngs.
- Optimization: Exclude download links from preloader animation.
- Optimization: Added newsletter privacy `_key` variables by passing them into the include.
- Optimization: Optimized display mode standard for gallery slider.
- Optimization: Prevents product slider controls from being cut off.
- Optimization: Prevents product slider controls blue outline on mobile browsers.
- Compatibility: According to Telecommunications Telemedia Data Protection Act, the buttons of a cookie banner may only be displayed in the same colour and size.
- Compatibility: Fixes compatibility with features-element and Moorl foundation plugin.
- Bugfix: Fixed CMS Breadcrumbs horizontal align.
- Bugfix: Force hides the preloader animation if we get stuck in beforeunload event.

# 2.4.7
- Optimization: Prevent filter text colors being affected by hero section text contrast settings.
- Optimization: Adds a new query that switches the display from selection select to selection inputs in listings if maximum purchase is set to value greater than 1000.
- Optimization: Set copyright year to twig date year function.
- Bugfix: Fixes a query to override the "Display category image" setting from theme configuration.
- Bugfix: Fix incompatible units px and rem in a specific configuration.
- Bugfix: Revision of CMS Element Resolver decorator | Pro Version.

# 2.4.6
- Feature: Added new configuration to display product features in product details page.
- Optimization: Set input readonly and input disabled colors.
- Optimization: Removed class `mr-1` on search
- Optimization: Shop-Navigation Tooltip initialization.
- Optimization: Shop-Navigation and Top Bar transitions.
- Optimization: Headline color contrast wishlist.
- Optimization: Improves positioning of product-slider controls.
- Bugfix: Shopnavigation Position in Header.

# 2.4.5
- Feature: Add cover switch functionality for CMS elements.
- Feature: Added the ability to translate texts from theme configuration.
- Feature: Added new configuration field for additional css files to theme plugin configuration.
- Feature: Added new configuration field for filter border active color.
- Optimization: Add `$css-relative-bundles-path` to bundles Assets-Paths in SCSS, because `$css-relative-bundles-path` is used to be the domain, which the administration is invoked which in turn can be a different domain than the sales channel domain.
- Optimization: Improvement in the administration  of the theme | Pro Version.
- Optimization: Improves the theme configuration which is broken after update 6.4.8.0 until shopware will fix it by themselves. | Pro Version
- Optimization: Improves the theme configuration blocks title which are missing after update 6.4.8.0 until shopware will fix it by themselves - NEXT-20077
- Optimization: Reduce gallery slider min heights to default value of 430px.
- Optimization: Improvement of designations in the theme.json.
- Optimization: Prevents product card border from the left inactive element shines into visible part of slider.
- Optimization: Improves the text contrast of the payment methods description in the checkout.
- Optimization: Added small gutters to header grid cols.
- Optimization: Added button border radius to main navigation hover-0.
- Optimization: Hide default breadcrumbs on CMS product pages if custom zenit-breadcrumb is used.
- Optimization: Optimizes header icon-only buttons width.
- Bugfix: Added layout-container-negative-vertical-margin mobile margins.
- Bugfix: Fixes Pagination colors.
- Bugfix: Displaying placeholder in gallery slider properly.
- Bugfix: Fixes a display problem of the floating labels with the Custom Products plugin.
- Compatibility: Prevents product slider controls and items from being cut off due to Shopware update 6.4.8.0

# 2.4.4
- Child-Theme Update available (required): When using one of our child theme template sets, we also offer an update of the child theme to version 2.4.4. This child-theme update package is cumulative and includes compatibility updates to Shopware 6.4.8.0. You can download our child themes here for free: https://themes.zenit.design Please refer to the update guide if you have made individual programming in the child theme: https://themedocs-sw6.zenit.design/vorlagen#updates
- Compatibility: Compatibility with Shopware 6.4.8.0

# 2.4.3
- Feature: Added new configuration to add external link icon to main-navigation and flyout-navigation.
- Feature: Added new configuration to add meta description as product description in listing product layout type standard.
- Feature: Added close icon to expandable search.
- Feature: Added body click close feature for expandable search.
- Optimization: Sets cursor to default for structuring elements in main-navigation.
- Optimization: Improved the display of CMS elements on header category images.
- Optimization: Improved floating labels select fields on header category images.
- Optimization: Improved floating labels select fields.
- Optimization: Improved hover-1 brackets color.
- Optimization: Adjustments for bootstrap responsive tables.
- Optimization: Improved the display of the product actions buttons.
- Optimization: Improved main navigation hovers.
- Optimization: Improved the display of the manufacturer logo.
- Optimization: Improved the display of horizontal paddings of rounded buttons and rounded input fields.
- Optimization: Improved header background opacity option for colors which have already transparency.
- Optimization: Revision of the Subscriber for image switch function. | Pro Version
- Optimization: Removed a function to load the plugin name in sw-theme-manager-detail.
- Optimization: Prevents a problem when fitting the product image of the product box when a product image border has been defined.
- Bugfix: Improved the display of rounded corners in textareas.
- Bugfix: Improved the preloader on page history back.

# 2.4.2
- Feature: Added new configuration for keep gallery image ratio zoom, which is new in SW 6.7.
- Feature: Added new configuration to display details button on variant products in listings due to SW Ticket NEXT-19146.
- Feature: Added new configuration to change preloader timeout.
- Feature: Added new configuration to change contrast threshold.
- Optimization: Renamed file `preloader.html.twig` into `zen-preloader.html.twig`.
- Optimization: Removed unnecessary ID in quantity input.
- Optimization: Improvement of the code quality based on the code quality analysis.
- Optimization: Set quantity input default-value to min purchase value.
- Optimization: Added btn icon transitions.
- Optimization: Added header container layout-spacer-paddings on full-width-header layouts.
- Optimization: Removed footer container layout-spacer-paddings.
- Optimization: Improvement of designations in the theme.json.
- Optimization: Improved marketing slider build on webfonts loaded event. 
- Optimization: Improved the stacking context of the buy box with sticky option.
- Optimization: Prevents the products from being displayed one below the other until the product Slider Javascript is loaded and the slider is initialized.
- Optimization: Revision of the CustomFields definitions.
- Optimization: Improves the display of the quantity input in listings when the product image is overlaid.
- Optimization: Equalize the padding/margins of CMS Layouts between CMS Editor and CMS Storefront.
- Optimization: Optimizes the presentation of the data protection notice in the footer newsletter - notice only appears when entered a value and prevents the footer from jumping

# 2.4.1
- Feature: Added new configuration option for preloader.
- Optimization: Improves the preloader on page changing.
- Optimization: Improvement of various contrast functions in product details tabs content.
- Optimization: Set contrast color function for gift number input.
- Optimization: top bar icon alignment.
- Optimization: Styling account-menu-offcanvas on dark offcanvas backgrounds.
- Optimization: Add `#{$sw-asset-theme-url}` to bundles Assets-Paths in SCSS.
- Optimization: Using Twig-block function to display untouched parent-blocks.
- Optimization: Removed unnecessary defaultValue-elements in config.xml.
- Bugfix: Prevents horizontal scrolling on mobile due to grid gutter min-width.
- Bugfix: Fixes customisable links in categories.

# 2.4.0
- Feature: Added new configurations for pagination colors.
- Feature: Added new configurations for product slider controls.
- Feature: Added new configuration for breadcrumbs font size.
- Feature: Added new configuration for main navigation active font weight.
- Feature: Added new configurations for language switcher.
- Feature: Added position sticky to cms buy box.
- Feature: Added gallery scroller as new display mode for gallery.
- Feature: Added new configuration for meta theme-color.
- Optimization: Optimization of the sticky header offset for the unstick event when scrolling up.
- Optimization: Improves positioning of input labels in floating labels mode.
- Optimization: Added ellipsis for floating labels to prevent multiple lined labels.
- Optimization: Footer column margins.
- Optimization: New update method to update base_config of theme duplicates due to Issue NEXT-10994
- Optimization: Product slider inner paddings.
- Optimization: Set cookie deny-button to btn-link for better usability.
- Optimization: Improvement of the theme configuration Footer Columns widths selection sorting.
- Optimization: Improve theme inheritance in theme.json.
- Optimization: Added horizontal padding to full-width header.
- Bugfix: Fixes the display of the quantity input on small product cards.
- Bugfix: Fixes the display of the quantity entry on small mobile devices in checkout tables.

# 2.3.3
- Feature: Added new configuration to display details button in listings, even if the option Display buy buttons in listings in Settings > Produkt is disabled.
- Optimization: Adding own service-menu-widget.html.twig file to keep the service menu in future versions.
- Optimization: Reduce size of help-icon in top bar.
- Optimization: Prevent quickview product title link from opening in target blank.
- Optimization: Added light-text-muted color to offcanvas cart elements.
- Optimization: Improved quantity input btn text colors on different elements.
- Optimization: Improvement of the theme configuration labels.
- Optimization: Optimization of the sticky header offset for the unstick event when scrolling up.
- Optimization: Optimizes a sidebar navigation query for transparent `zen-sidebar-navigation-active-bg` variable.
- Optimization: Renamed `Normal - 400` into `Regular - 400` to standardize the naming in theme.json.
- Optimization: Removed unnecessary `footer-contact` classes in footer columns.
- Optimization: Set fallback colors for zoom modal background, if `$zen-product-listing-card-img-bg` is transparent.
- Optimization: Set the cms section sidebar main content margin left to 0 so that there is a more even gap between the sidebar and the main content.
- Compatibility: Remove override of `$app-css-relative-asset-path` due to shopware NEXT-10560 from 2.1.0, because Shopware solved it itself now.
- Compatibility: Fixes a problem due to the new feature in Issue NEXT-16879 whereby assets () use the Symfony Flysystem version strategy and the PDF-Viewer could no longer compose the URL correctly.
- Bugfix: Fixes the error message `Argument 1 passed to Shopware\\Core\\Framework\\Uuid\\Uuid::isValid() must be of the type string` for orphaned config values.
- Bugfix: Fixes the display of the quantity entry on small mobile devices.

# 2.3.2
- Feature: Adding new configuration for container spacings.
- Optimization: Revision of the container spacing.
- Optimization: Revision of the container spacing mixins parameters to include two separate values.
- Optimization: Improves logo placement when the height is larger than 100%
- Optimization: Improves logo placement with `objectfit` to ensure correctly display of svg without initial dimensions.
- Optimization: Renamed class-name in breadcrumb administration cms-element.
- Optimization: Added space between `.product-detail-tab-navigation` and `.product-detail-tabs-content`, if there is background color and tabs appearance is not set to tabs 
- Optimization: Improvement of the theme configuration labels.
- Optimization: Improvement of the theme configuration helptexts.
- Optimization: Adds a query to display the Properties tab.
- Optimization: Exclude buttons from footer link styling.
- Optimization: Added space between header and sticky buybox.
- Optimization: Added new table accent color for dark themes.
- Optimization: Improved Zoom Modal Layout.
- Optimization: Breadcrumbs overlaying an image must not have any background.
- Optimization: Added some font-weight variables which bootstrap does not supply.
- Optimization: Changed main-navigation active font-weight to `semi-bold`.
- Optimization: Changed breadcrumbs active font-weight to configuration value.
- Optimization: Added some IE11 fixes for sticky elements and floating labels.
- Optimization: Added top spacing to sticky `.checkout-aside-container` because of sticky header.
- Optimization: Improved floating labels select fields if no option is selected.
- Optimization: Changed hero section default background color to `transparent`.
- Optimization: Renamed classes with `--` to `-` because auf of code style.
- Optimization: Improved Preloader Logo animation and logo display with `object fit`.
- Optimization: Improved Offcanvas Logo display with `object-fit` and header logo height configuration.
- Optimization: Improved Footer Logo display with `object-fit` and header logo height configuration.
- Bugfix: Added usage of var `$zen-footer-custom-column-color`, because it was never used.
- Bugfix: Revision of a query to display the top bar close button.

# 2.3.1
- Bugfix: Fixes the display of the options of custom selects when using the floating labels function.

# 2.3.0
- Feature: Floating Labels for Form-Elements
- Feature: Added configuration for Zoom Magnifier over gallery
- Feature: Added new configuration value for product card action positions
- Feature: Added configuration for product card attention animation
- Feature: Added minified pdf-viewer/-renderer.
- Feature: Added new configuration for products per row for small smartphones
- Feature: Added new configuration for content main container vertical paddings
- Feature: Added new configuration to set breadcrumbs to full width mode
- Feature: Added close icon to collapsible and expandable top bar
- Optimization: Sticky Buybox checks if window height is greater than buybox height
- Optimization: Avoids flashing through the background on product-images with background overlay due to pixel inaccuracies on elements with border-radius.
- Optimization: Improvement of card body paddings on different background-color combinations
- Optimization: Improvement of the theme configuration texts.
- Optimization: Set gallery slider gutter to zero, because of zen-product-listing-card-img-bg which show through the gutter.  
- Optimization: Removed additional condition on `product.childCount` for buy button in listings, so that expanded variants also display the buy button.
- Optimization: Optimization of the help text for the font settings.
- Optimization: Prevents box-shadows from being cut off in product boxes in product sliders
- Optimization: Removes quotation marks on @if statements in SCSS
- Optimization: Ensures that the gallery image slider configuration for product pages only applies to image sliders of the product page
- Optimization: Moved settings for the aspect ratio of product images into the General Tab
- Optimization: Improvement of the initialization of the Javascript plugin which scrolls to invalid form elements.
- Optimization: Improvement of the overlay search btn loader background color
- Bugfix: Quantity Change in Checkout Quickview Modal
- Bugfix: Representation of the preview view of the breadcrumb CMS element
- Bugfix: Fixes the problem of the non-editable border color for Product boxes
- Bugfix: Correction of zen-product-gallery-slider-display-mode standard value

# 2.2.2
- Optimization: Revision of the image change in the listing so that on mouseover the cover image disappears behind the second image. 
- Optimization: Corrects the display of breadcrumb backgrounds so that they only extend over the width of the breadcrumbs.
- Optimization: Improvement of the animation Hover 5 of the main navigation.  
- Bugfix: Correction of text-align of product prices in listings
- Bugfix: Correction of Quickview input-style query
- Bugfix: Correction of maxPurchase value from maxQuantity to calculatedMaxPurchase

# 2.2.1
- Optimization: Representation of the quantity selection in product listings
- Optimization: Transparent header on mobile devices
- Optimization: Correction of a wrong translation in the theme.json
- Optimization: Improvement of designations in the theme.json
- Optimization: Gallery slider width merged with height configuration
- Bugfix: Correction of the product slider style query

# 2.2.0
- Feature: Added configuration to display product number in listings
- Feature: Added configuration to change style of quantity select to -/+ input
- Feature: Integrated Browser-level image lazy-loading
- Feature: Added configuration for product slider layout to choose between cropped (standard) and expanded mode. Expanded mode shows the overflowed items. 
- Feature: Added configuration for product details image gallery width
- Feature: Added configuration for product details image gallery display mode
- Feature: Added configuration for product details image gallery arrow navigation position
- Feature: Added configuration for product details image gallery dots navigation position
- Feature: Added configuration for product details image gallery preview navigation position
- Feature: Added configuration for product details image gallery zoom
- Feature: Added configuration for product details image gallery fullscreen gallery
- Feature: Added configuration for product details background color
- Feature: Added configuration for product details tabs background color
- Feature: Added configuration for product details tabs content background color
- Feature: Added configuration for product details name text transform  
- Feature: Added configuration for product listing cards name text transform
- Feature: Added configuration for newsletter position above footer  
- Feature: Added configuration for footer paddings
- Feature: Added configuration for footer border
- Feature: Added configuration for header border
- Feature: Added configuration for listing actions border
- Feature: Added configuration for base slider navigation colors for mode outside, which is new in SW 6.4  
- Feature: Added position sticky to buy box
- Optimization: Refactored footer configuration in theme.json  
- Optimization: Removed titles in SVG-Images
- Optimization: Corrects the display of the slider arrows of product sliders if an alternative display of slider arrows was selected.
- Optimization: Renamed `Font Align` into `Text align` in theme.json.
- Optimization: Renamed `Settings` into `Einstellungen` in Administration.
- Optimization: Changed color for `payment-method-description` and `shipping-method-description` due to problematic coloring in the Shopware Default Theme 
- Optimization: Refactored search layout expanded to not using overflow-x hidden on page-wrapper anymore due to get position sticky working.
- Optimization: New check if product has crossselling
- Optimization: Refactored Subscriber for product listing media 
- Optimization: Refactored an if-clause for category-navigation
- Optimization: icon-style-square get now button border radius from configuration
- Optimization: Navigation item is now active again if it is in the active path  
- Bugfix: Fixes Text transform value on filter panels  
- Bugfix: Fixes an incorrect variable that prevents the feature element from being displayed.

# 2.1.2
- Feature: Added configuration for variant characteristics to display on product detail page.
- Optimization: Changed some configuration `"type": "switch"` to `"type": "bool"` in theme.json.
- Optimization: Changed configuration `"type": "checkbox"` to `"type": "bool"` in theme.json.
- Optimization: Display of the marketing text slider even with single-line content.
- Optimization: Renamed `Mindesthöhe` to `Minimale Höhe` in theme.json.
- Optimization: Improvement of CMS Breadcrumbs border bottom.
- Optimization: Fixes the disappearance of the arrows in select fields on mouseover.
- Optimization: Improvement of the display of the cart button in listings with deactivated quantity selection.
- Optimization: Improvement of the inner spacing of product boxes with activated shadow.
- Optimization: Improvement of the inner spacing of product actions with inactive shadows or borders.
- Optimization: Improvement of the display of the note icon in product boxes with inactive shadows or borders.
- Optimization: Changed Service/Help Icon für Feather Old Icon set.
- Optimization: Improvement of the display of the border-radius in the sidebar if the active path has the same background color as the shop background or container background.  
- Optimization: Prevents shadows from being cut off from product boxes.
- Optimization: Input placeholder reduced to 50% visibility.
- Bugfix: Prevent Twig Error "Division by zero" if CMS Features-Element is used but never configured.
- Bugfix: Fixes the incorrect display of the gallery slider if the configuration is missing in outdated child theme sets by setting a fallback value.

# 2.1.1
- Optimization: Removed unnecessary references to SnippetFiles, due to auto-registration.
- Optimization: Spelling and revision of the theme configuration.
- Optimization: Improvement of the calculation of the maximum width of the marketing slider.
- Optimization: Renamed custom CMS element filenames from  `cms-xyz-zenit-xyz.html.twig` into `cms-xyz-zen-xyz.html.twig`.
- Optimization: Renamed cms elements `zenit` into `zen` for standardization.
- Optimization: Moved theme configurations 'Sidebar Navigation' from tab 'Category' to tab 'Components' due to the possible uses on different types of pages.
- Optimization: Added gallery slider `minHeight` `sliderConfig` from configuration field `zen-product-gallery-slider-min-height-desktop`.
- Optimization: Improvement of the Custom Select Hover colors.
- Bugfix: Fixes a bug where the text snippets in the additional CMS elements were not loaded.
- Bugfix: Fixes a bug when sending the search in the overlay search mode.
- Bugfix: Update of the Mozilla PDF viewer to version v2.9.359 due to a display error in Mozilla Firefox.

# 2.1.0
- Feature: Added configuration for global font sizes (base, h1-h6).
- Feature: Added configuration for product details gallery-slider height.
- Feature: Added configuration option for top-bar hidden.
- Feature: Added configuration option for dots navigation appearance `Expanding`.
- Feature: Added pdf-viewer for displaying datasheets/pdf implemented.
- Feature: Added configuration to choose between two different pdf-viewer/-renderer.
- Feature: Added icon set `entypo`.
- Feature: Added cms element `features` (not available in shopware cloud).
- Feature: The height of the gallery slider on product detail pages is now influenced by the set product aspect ratio, unless it is overwritten in the new configuration option for manual setting.
- Feature: Slider Controls Positioning `alternative` available for details gallery-sliders. 
- Optimization: Subscribed to Listing Render Event to re-init javascript.
- Optimization: Fixes a Shopware bug where the buy button is also displayed in the listing for variants.
- Optimization: Improvement of the presentation of the marketing slider so that the text and button are closer to each other.
- Optimization: Avoids flashing through the background on product-images with background overlay due to pixel inaccuracies on elements with percentage widths.
- Optimization: Spelling and revision of the theme configuration. 
- Compatibility: Override `$app-css-relative-asset-path` due to shopware NEXT-10560 (Resolve fonts relative to the theme app.css) to resolve fonts by using bundle/storefront assets.
- Bugfix: Renamed variable `zen-category-filter-seperator` to `zen-category-filter-separator`.

# 2.0.1
- Optimization: Added SCSS function strip-units to improve the fault tolerance of certain configuration fields.
- Optimization: Changed field type of the product-image-width and -height configuration to improve the fault tolerance.
- Optimization: Adding min / max values in the theme configuration to improve fault tolerance.
- Optimization: Improvement of the display of individual elements of the theme configuration.
- Optimization: Improvement of theme configuration help texts.
- Optimization: Removed the optional background color from breadcrumbs if they are displayed in the category image in the header area.
- Optimization: Renamed class name `ZenitOverrideCookiePermissionPlugin` to `ZenOverrideCookiePermissionPlugin`
- Optimization: Quoting the values of url() to prevent display problems on URLs with special characters.
- Optimization: Adjust the color of the wish list link on the detail page for dark backgrounds.
- Optimization: Improvement of the display of the clearance sale label on the detail page.
- Compatibility: Removed the SnippetFile PHP-files, as they are no longer needed due to the auto-load function.
- Bugfix: Prevent Dropdown overflow-boundary affect sidebar filters.

# 2.0.0
Compatibility with Shopware 6.4.0.0
__Storefront__
- Compatibility: Refactored Logo size and positioning
- Compatibility: Refactored Breadcrumbs
- Compatibility: Added wishlist template header
- Compatibility: Added wishlist template product box
- Compatibility: Added wishlist icon
- Compatibility: Removed `zentheme` variable, use `theme_config('my_config_key')` instead
- Compatibility: Removed `zenconfig` variable, use `config('my_config_key')` instead
- Compatibility: Removed `shopware.theme.breakpoint`, use specific size variable instead `theme_config('breakpoint.sm')`
- Optimization: Removed `filter-rating.html.twig` because it is no longer used
- Optimization: Refactored top bar width on sticky header
- Optimization: Refactored of template variables declared in base.html.twig
- Optimization: Renamed `zenOpenFilterOptions` into `openFilterOptions`
- Optimization: Added block `zen_component_login_register`
- Optimization: Added block `zen_component_text_slider`
- Optimization: Added block `zen_component_social_icons`
- Optimization: Refactored twig-templates for improved use of `{{ parent() }}`
- Optimization: Added prefix for over 190 twig-blocks to increase compatibility
- Optimization: Refactored `zen-footer-newsletter.html.twig` to extend the `newsletter-form.html.twig`
- Optimization: Renamed file `action-buy-widget-container.html.twig` into `zen-action-buy-widget-container.html.twig`
- Optimization: Renamed file `action-quickview-toggle.html.twig` into `zen-action-quickview-toggle.html.twig`
- Optimization: Renamed file `cover-switch.html.twig` into `zen-cover-switch.html.twig`
- Optimization: Renamed file `login-register.html.twig` into `zen-zen-login-register.html.twig`
- Optimization: Renamed file `text-slider.html.twig` into `zen-text-slider.html.twig`
- Optimization: Renamed file `top-bar-offcanvas.html.twig` into `zen-top-bar-offcanvas.html.twig`
- Optimization: Renamed file `custom-global-tab.html.twig` into `zen-custom-global-tab.html.twig`
- Optimization: Renamed file `custom-tab1.html.twig` into `zen-custom-tab1.html.twig`
- Optimization: Renamed file `custom-tab2.html.twig` into `zen-custom-tab2.html.twig`
- Optimization: Renamed file `datasheets.html.twig` into `zen-datasheets.html.twig`
- Optimization: Renamed file `manufacturer.html.twig` into `zen-manufacturer.html.twig`
- Optimization: Renamed file `properties-tab.html.twig` into `zen-properties-tab.html.twig`
- Optimization: Renamed file `video.html.twig` into `zen-video.html.twig`
- Optimization: Renamed file `zen-social.html.twig` into `zen-social-icons.html.twig`
- Bugfix: sticky header on multiple lined headers prevents clicks if nav main is hidden
- Bugfix: prevents filter dropdowns from cut off by setting custom boundary HTMLElement

__CMS__
- Compatibility: Added product pages cms templates
- Compatibility: Added cms breadcrumbs into product page cms
- Compatibility: Added `src/Resources/views/storefront/element/cms-element-product-description-reviews.html.twig` to display `product-description-reviews` cms element.
- Compatibility: Added `src/Resources/views/storefront/component/product/descrption.html.twig` to change display of properties.
- Compatibility: Added `src/Resources/views/storefront/block/cms-block-product-heading.html.twig` to implement product short-description.
- Compatibility: Added `src/Resources/views/storefront/element/cms-element-product-name.html.twig` for review positioning.
- Optimization: Added auto margins fallback to Shopping Experience Margins

__SCSS__
- Optimization: Increase code quality by codequality analysis

__Administration__
- Compatibility: Refactored Administration Modul settings link

__CustomFields__
- Optimization: Removed `zenCustomFieldsCategory` variable, use `zenCustomFields.cagtegory` instead
- Optimization: Added `zenCustomFields.product` variable for `page.product.translated.customFields`
- Optimization: Refactored custom fields key call in twig-templates

__Subscriber__
- Optimization: Removed `generateConfigId` method
- Compatibility: Refactored `StorefrontRenderSubscriber` theme config access

# 1.2.4
- Optimization: Improvements for the display of data sheets on mobile devices
- Optimization: Improvement of the display of the manufacturer link if no link is set in the manufacturer
- Optimization: Refactoring of the plugin bootstrap
- Optimization: Refactoring of the plugin configuration ID for compatibility with the Shopware Cloud
- Optimization: Refactoring of breadcrumb and category image management for compatibility with the Shopware Cloud

# 1.2.3
- Feature: store data sheets in the product and display them as a tab
- Optimization: Improves the display of the scroll navigation with a dark body background using the CMS Extensions plugin
- Optimization: Small improvements of the custom fields
- Optimization: Minor fixes in the theme configuration
- Optimization: Refactoring of the variable zenplugin to zenconfig based on its semantic meaning
- Optimization: Preparation for wishlist implementation
- Bugfix: Prevents the tab navigation from being covered by the negative margin of the product slider

# 1.2.2
- Bugfix: Number of products per row was lost when paging
- Optimization: Corrects the display of the vertical alignment of cross-selling product sliders

# 1.2.1
- Optimization: Improvement of the representation of the search by input groups
- Optimization: Improvement of the product box shadow for product sliders
- Optimization: Corrects the configuration for the vertical alignment of shopping experience elements
- Compatibility: Compatibility with our Font Change Plugin
- Bugfix: Optimized the display of the newsletter input in larger fonts
- Bugfix: Configurator option label text color with dark shop background color

# 1.2.0
- Feature: Implementation of a selection of Google fonts as local theme fonts for easy data protection-compliant use without external embedding.
- Feature: New configuration to select theme-fonts.
- Feature: New configuration to toggle the collapsible element to be open by default on mobile devices.
- Feature: New configuration to set number of products per row in listings for each viewport/device.
- Feature: New configuration to enable or disable variant characteristics on product boxes.
- Optimization: Improvements in the theme.json 
- Optimization: Added some twig-blocks in base.html.twig for better customization.  
- Optimization: Adjust line-height of product-box product-description
- Optimization: Revision of the help texts for custom code

# 1.1.4
- Feature: Code Editor in Plugin configuration for custom code fields
- Optimization: Positioning of centered breadcrumbs if header absolute and full width header mode
- Optimization: Disabled button contrast
- Optimization: Appearance product configurator option label with rounded borders
- Optimization: Revision of the product card image display mode image
- Optimization: Revision of the category image configuration

# 1.1.3
- Bugfix: Display of breadcrumbs on the detail page at SW 6.3.4.0

# 1.1.2
- Optimization: Positioning of the Suggest search results
- Optimization: Prevents elements from being superimposed on the product image
- Optimization: Calculation of additional body paddings for cookie banners only with default ad
- Optimization: Administration module injects repositoryFactory instead of theme-mixin
- Compatibility: Improvement of the compatibility of the icon.html.twig
- Compatibility: Hide thumbs and dots when having too many product images - SW 6.3.4.0

# 1.1.1
- Optimization: Revision of the event to hide the preloader
- Optimization: Revision of the product card image display modes in listings
- Optimization: Prevents jumping of product-actions between transition-states in listings
- Optimization: Prevents the background color of the article images from flashing on detail pages
- Optimization: moved DOM .page-wrapper
- Optimization: Width of Flyout Menu improved
- Bugfix: Quickview Cart Button - PageController can't be requested via XmlHttpRequest.

# 1.1.0
- Feature: New configuration for controlling the footer columns on tablet and desktop devices
- Optimization: Improvement of the display in certain combinations of settings
- Optimization: Width of the extended menu in boxed mode
- Optimization: Product Slider Gutter can be set by configuring the card spacing. This gives all grids the same spacing.
- Optimization: Revision of the header combinations with regard to the display of the Offcanvas Navigation Button
- Optimization: Vertical alignment of text and icon in the header shopping cart button
- Optimization: Revision of the sticky header function
- Optimization: Suggest search results scrollable
- Bugfix: Horizontal scrolling through expandable search in relation to header mode full-width-boxed fixed
- Bugfix: Deaktivieren der Tooltips auf Geräten mit Toucheingabe

# 1.0.5
- Optimization: Improvement Lighthouse Report: font-display: swap implemented for font embedding
- Optimization: Further improvements of the accessibility of the Lighthouse Report
- Optimization: Improved display of the images in cross-selling
- Bugfix: Close button of the overlay search carried out the search after entering a search string, if the Nutter decided not to carry out a search.
- Bugfix: header-main in sticky mode in combination with the hide option covered elements

# 1.0.4
- Optimization: Revision of the header display for single header, standard search, center logo and off-canvas main navigation XS-MD and XS-LG
- Optimization: Positioning of the centered logo when displaying elements above the header, e.g. Conversion banner
- Optimization: Improvement of the sticky header with absolute positioning
- Optimization: Improvement of the sticky header animation with single-line header and hide option
- Optimization: Improvement of the sticky header calculation in Javascript in connection with the hide option
- Compatibility: Absolutely positioned headers no longer cover conversion bars and other elements above the header
- Bugfix: Prevent escaping html on quickview description and manufacturer description in manufacturer tab

# 1.0.3
- Optimization: Revision of footer paddings on boxed layouts
- Optimization: Revision of header overlay mode on shopping experiences
- Optimization: Revision of expandable search on different layout styles
- Feature: Header overlay mode on shopping experiences on background-color independent of a category image
- Bugfix: paddings on breadcrumbs on Boxed Layout
- Bugfix: Quickview price align
- Bugfix: Product actions not visible after sorting

# 1.0.2
- Optimization: Revision of the Main Navigation Hovers
- Optimierung - Revision of the quickview icon
- Optimierung - Revision of header with multi lined, logo left, search default
- Bugfix: Details page mobile tab-menu
- Bugfix: Collapsible top bar initial state

# 1.0.1
- Feature: added content animations for shopping experiences (more about this in the documentation).
- Compatibility: Improved compatibility when placing the flyout menu in fullwidth mode
- Optimization: Smaller optimizations of designations in the configuration

# 1.0.0
- Initial plugin release
