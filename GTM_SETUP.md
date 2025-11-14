# Google Tag Manager Setup

This project is configured to use Google Tag Manager (GTM) for tracking and analytics.

**No additional packages needed!** Vite/VitePress automatically loads `.env` files during build.

## Configuration

GTM is configured via environment variables, keeping your container ID secure and allowing different IDs for local development and production.

### Local Development

1. **Copy the example env file:**
   ```bash
   cp .env.example .env
   ```

2. **Edit `.env` and add your GTM ID:**
   ```bash
   VITE_GTM_ID=GTM-XXXXXXX
   ```

3. **Start the dev server:**
   ```bash
   npm run docs:dev
   ```

### Production Setup

1. **Create `.env` file on the server:**
   ```bash
   cd /home/djson/public_html
   nano .env
   ```

2. **Add your production GTM ID:**
   ```env
   VITE_GTM_ID=GTM-XXXXXXX
   ```

3. **Build the site with GTM:**
   ```bash
   npm run docs:build
   ```

4. **Update the systemd service (if needed):**
   The service file already includes `EnvironmentFile=/home/djson/public_html/.env`

   ```bash
   sudo systemctl daemon-reload
   sudo systemctl restart djson-docs
   ```

## How It Works

This implementation follows **Google's official GTM installation guide** with both head and body tags:

### Implementation Details:

1. **Head Script** (`<head>` section):
   - GTM library loader with dataLayer initialization
   - Configured via VitePress `head` array

2. **Body Noscript** (`<body>` section):
   - Fallback iframe for users with JavaScript disabled
   - Injected using VitePress `transformHtml` hook

### Build Process:

- **Vite automatically loads `.env` files** during build and dev commands
- Environment variables prefixed with `VITE_` are embedded into the build at **build time**
- The GTM script is **baked into the static HTML** during `npm run docs:build`
- If no GTM ID is configured, the site builds without tracking (useful for local development)
- The `.env` file is gitignored to keep your GTM ID secure
- **Important**: The serve command just serves pre-built files, env is loaded during build only

## Verification

After building and deploying:

1. **Check the HTML source for both GTM tags:**
   ```bash
   # Check head script
   grep -r "gtm.js" docs/.vitepress/dist/index.html

   # Check body noscript
   grep -r "ns.html" docs/.vitepress/dist/index.html
   ```

   Or view page source and verify:
   - `<head>` contains: `gtm.js?id=GTM-XXXXXXX`
   - `<body>` contains: `<noscript><iframe src="...ns.html?id=GTM-XXXXXXX"`

2. **Use GTM Preview Mode:**
   - Go to your GTM container
   - Click "Preview"
   - Enter your site URL
   - Verify tags are firing correctly

3. **Check browser console:**
   ```javascript
   // Should log your GTM container info
   console.log(window.google_tag_manager)
   console.log(dataLayer)
   ```

## Multiple Environments

You can use different GTM containers for different environments:

**.env.development** (local):
```env
VITE_GTM_ID=GTM-DEV1234
```

**.env.production** (server):
```env
VITE_GTM_ID=GTM-PROD5678
```

## Troubleshooting

**GTM script not appearing:**
- Verify `.env` file exists and contains `VITE_GTM_ID`
- Rebuild the site: `npm run docs:build`
- Check the build output for any errors
- Verify the environment variable is loaded: `echo $VITE_GTM_ID`

**Tags not firing:**
- Open GTM Preview mode
- Check browser console for errors
- Verify the GTM container is published
- Ensure triggers are configured correctly in GTM

**GTM not loading after deployment:**
- Remember: .env is loaded during **build**, not during serve
- Rebuild the site: `npm run docs:build`
- Verify GTM is in the built HTML: `grep -r "googletagmanager" docs/.vitepress/dist/`
- Restart service: `sudo systemctl restart djson-docs`
- Check logs: `sudo journalctl -u djson-docs -n 50`

## Technical Implementation

This project uses a proper GTM implementation that works around VitePress limitations:

**The Challenge:** VitePress only supports head injection natively, but GTM requires both head and body tags.

**The Solution:**
- **Head script**: Uses VitePress `head` config array
- **Body noscript**: Uses `transformHtml` hook to inject after `<body>` tag

**Code reference:** See `docs/.vitepress/config.js:9-22`

This approach:
- ✅ Follows Google's official GTM installation guide
- ✅ Provides fallback tracking for no-JS users
- ✅ Works with VitePress build system
- ✅ Keeps configuration DRY (GTM ID defined once)

## Security Notes

- ✅ `.env` is gitignored - your GTM ID stays private
- ✅ `.env.example` is committed - team knows what to configure
- ✅ GTM ID is not a secret credential, but keeping it private prevents unauthorized tag injection attempts
- ⚠️  Never commit `.env` to the repository
