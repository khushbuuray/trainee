# ICT Data Cleaner Pro

A comprehensive data cleanup plugin for Shopware 6.6+ that helps maintain a clean and efficient e-commerce database.

## Features

- **Comprehensive Cleanup Rules**: Clean up products, customers, orders, carts, categories, media, and more
- **Safe Operations**: Dry-run mode, confirmation dialogs, and detailed logging
- **Automation**: Scheduled cleanup tasks with configurable frequency
- **Admin Interface**: Intuitive settings module with live preview
- **CLI Support**: Command-line interface for automated workflows
- **Extensible**: Modular architecture with clear extension points

## Installation

1. Download and extract the plugin to `custom/plugins/IctDataCleanerPro`
2. Install and activate via CLI:
\`\`\`bash
bin/console plugin:refresh
bin/console plugin:install --activate IctDataCleanerPro
\`\`\`

## Configuration

Navigate to **Settings > Data Cleaner** in your Shopware admin panel to configure:

- General settings (dry-run mode, scheduling, notifications)
- Cleanup rules for each entity type
- Retention periods and conditions

## Usage

### Admin Interface

1. Go to **Settings > Data Cleaner**
2. Configure your cleanup rules
3. Click **Preview Cleanup** to see what will be deleted
4. Click **Run Cleanup** to execute the cleanup

### CLI Command

\`\`\`bash
# Preview cleanup (dry-run)
bin/console ict:cleanup:run --dry-run

# Execute cleanup
bin/console ict:cleanup:run

# Run as scheduled task
bin/console ict:cleanup:run --schedule
\`\`\`

### Scheduled Cleanup

Enable scheduled cleanup in the settings and choose your frequency:
- Daily
- Weekly  
- Monthly

## Cleanup Rules

### Products & Variants
- Remove products not sold in X months
- Delete products never sold
- Remove inactive products older than X months

### Customers & Accounts
- Remove guest accounts with no orders after X months
- Remove inactive customers with no login/orders

### Orders & Carts
- Remove abandoned carts after X days
- Remove cancelled/failed orders after X months
- Clean up old transactions

### Categories
- Remove empty categories
- Remove categories with no sales

### Media
- Remove orphaned media files
- Delete unused thumbnails

### System Data
- Clean up old logs
- Flush cache
- Remove orphaned custom fields

## Safety Features

- **Dry-Run Mode**: Preview all deletions before executing
- **Confirmation Dialogs**: Confirm before deleting data
- **Detailed Logging**: Track all cleanup operations
- **Batch Processing**: Configurable batch sizes to prevent timeouts
- **Transaction Safety**: All operations are wrapped in database transactions

## Requirements

- Shopware 6.6.0 or higher
- PHP 8.1 or higher

## Support

For support, please contact support@ict.com

## License

Proprietary - see LICENSE file for details
