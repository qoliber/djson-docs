# DJSON Documentation Service Setup

## Installation Steps

### 1. Create environment file and build (optional - for GTM tracking)
```bash
cd /home/djson/public_html
cp .env.example .env
nano .env  # Add: VITE_GTM_ID=GTM-XXXXXXX
npm run docs:build  # Vite loads .env automatically during build
```
Note: The .env file is read during `npm run docs:build`, not at serve time.

### 2. Copy the service file to systemd directory
```bash
sudo cp djson-docs.service /etc/systemd/system/
```

### 3. Create log files
```bash
sudo touch /var/log/djson-docs.log
sudo touch /var/log/djson-docs-error.log
sudo chown djson:djson /var/log/djson-docs.log
sudo chown djson:djson /var/log/djson-docs-error.log
```

### 4. Reload systemd daemon
```bash
sudo systemctl daemon-reload
```

### 5. Enable the service (start on boot)
```bash
sudo systemctl enable djson-docs
```

### 6. Start the service
```bash
sudo systemctl start djson-docs
```

## Service Management Commands

```bash
# Check service status
sudo systemctl status djson-docs

# Stop the service
sudo systemctl stop djson-docs

# Restart the service
sudo systemctl restart djson-docs

# View logs
sudo journalctl -u djson-docs -f

# View log files
tail -f /var/log/djson-docs.log
tail -f /var/log/djson-docs-error.log
```

## Important Notes

- **Environment file**: Create `.env` with `VITE_GTM_ID` for GTM tracking (optional, loaded during build by Vite)
- **Build first**: Always run `npm run docs:build` before starting the service (this is when .env is loaded)
- **Port**: The service runs on port 3000
- **User**: Running as user `djson`
- **Working Directory**: `/home/djson/public_html`
- **Auto-restart**: Service will automatically restart on failure after 10 seconds

## Troubleshooting

If the service fails to start:

1. Check if npm is in the correct path:
   ```bash
   which npm
   ```
   If different from `/usr/bin/npm`, update the `ExecStart` line in the service file

2. Verify the user and working directory exist:
   ```bash
   ls -la /home/djson/public_html
   ```

3. Check if node_modules are installed:
   ```bash
   cd /home/djson/public_html
   npm install
   ```

4. Ensure the build was completed:
   ```bash
   cd /home/djson/public_html
   npm run docs:build
   ```

5. Check logs for errors:
   ```bash
   sudo journalctl -u djson-docs -n 50
   ```

6. If GTM not appearing, rebuild with .env file:
   ```bash
   cd /home/djson/public_html
   ls -la .env  # Verify .env exists
   cat .env     # Check VITE_GTM_ID is set
   npm run docs:build  # Rebuild to embed GTM
   sudo systemctl restart djson-docs
   ```

For more information about Google Tag Manager setup, see `GTM_SETUP.md`
