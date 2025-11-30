# Shutterstock Downloader

A PHP script to download images, videos, and audio from Shutterstock using your own cookies. Export cookies via **[Cookie Exporter](https://github.com/realSina/cookie-exporter)** extension, and use this script to get download links.

## Features
- Download photos, videos, and music tracks from Shutterstock.
- Requires exported cookies from your Shutterstock account.
- Access download links for licensed content using your subscription.

## Requirements
1. PHP installed on your server.
2. Export your cookies using **[Cookie Exporter](https://github.com/realSina/cookie-exporter)**.
3. A valid Shutterstock account with an active subscription.

## Getting Started

### 1. Export Your Cookies

To use this script, you need to provide your Shutterstock cookies. Here's how to export them:

- Install the **[Cookie Exporter](https://github.com/realSina/cookie-exporter)** browser extension.
- Log in to your Shutterstock account.
- Export the cookies as a `cookies.txt` file.

### 2. Modify the Script

- Clone or download the repository.
- Edit the `country` variable (on line 33) to reflect your country code. For example:
  
  Edit the file to update this line:

  ` $country = "US";  // Replace "US" with your country code `
  
  For example, change `"US"` to `"DE"` for Germany.

### 3. Use the Script

To use the script, simply call it with the content URL like this:
`http://yourserver.com/shutterstock-downloader.php?url=<shutterstock_content_url>`

## License

MIT License. See the [LICENSE](LICENSE) file for details.

## Disclaimer

Ensure you comply with Shutterstock's terms of service and copyright laws. This script is intended for personal use only.
