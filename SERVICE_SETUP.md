# DJSON Documentation Service Setup

## Installation Steps

### 1. Copy the service file to systemd directory
```bash
sudo cp djson-docs.service /etc/systemd/system/
```

### 2. Create log files
```bash
sudo touch /var/log/djson-docs.log
sudo touch /var/log/djson-docs-error.log
sudo chown djson:djson /var/log/djson-docs.log
sudo chown djson:djson /var/log/djson-docs-error.log
```

### 3. Reload systemd daemon
```bash
sudo systemctl daemon-reload
```

### 4. Enable the service (start on boot)
```bash
sudo systemctl enable djson-docs
```

### 5. Start the service
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

- **Build first**: Always run `npm run docs:build` before starting the service
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
