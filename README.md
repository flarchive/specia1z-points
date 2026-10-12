# Flarum Points

A configurable points and rewards system for Flarum 2.x.

## Features

- Configurable registration, discussion, reply, best answer, and like rewards.
- Optional point deduction when content is deleted or a user is suspended.
- Integration with FoF Best Answer, Flarum Likes, and Flarum Suspend.
- Admin user-list controls for adding, subtracting, or setting a user's balance.
- Transaction history for every point change.
- Five point badge styles with a live Admin preview.
- Custom currency name and Font Awesome icon.
- Chinese and English translations.

## Requirements

- Flarum `2.0.0` or newer
- PHP version supported by Flarum 2.x

FoF Best Answer, Likes, and Suspend are optional. Their point integrations are enabled automatically when those extensions are enabled.

## Installation

From the root directory of your Flarum installation:

```bash
composer require specia1z/points:"^2.0"
php flarum extension:enable acme-points
php flarum cache:clear
```

The extension settings are available in the Admin dashboard under the extension settings page.

## Development

Install the JavaScript dependencies and build the frontend assets:

```bash
cd js
npm install
npm run build
```

The generated files in `js/dist` are committed because Composer installations do not run the JavaScript build automatically.

## License

MIT. See [LICENSE](LICENSE).
