# Tingg extension for Magento eCommerce v2

This extension allows you to use Tingg as payment gateway in your Magento 2 store.


## Magento Version Compatibility
- Magento (CE) 2.0.x - 2.1.x
- PHP 5.5.22 or above

## Dependencies
- None


## Installation
1. Upload the included **Tinggmagento** directory to “app/code/” of your Magento root directory.
2. On your server, install the module by running `bin/magento setup:upgrade` in your magento root directory.
3. Sign in to your Magento admin.
4. Flush your Magento cache under **System**->**Cache Management** and reindex all templates under **System**->**Index Management**.
5. Navigate to Payment Methods under **Stores**->**Configuration**->**Sales**->**Payment Methods** expand **Tingg Payment** and make sure that it is enabled.
6. Select **No** under **Sandbox**. _(Unless you are testing in the extension)_
7. Enter your **Vendor ID**. _(Tingg issued Vendor ID)_
8. Enter your **Hash Key** _(Tingg issued Hash Key)_
9. Save your changes.


## Merchant requirements
- The **Vendor ID** and **Hash Key** acquisition process is found on [Tingg Website](https://Tinggafrica.com/for-business/ )