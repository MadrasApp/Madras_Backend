# Upload Configuration Debug Guide

This guide helps you debug and test large file uploads (30MB+) in your Docker container.

## Changes Made

### 1. Enhanced Upload Controller (`application/controllers/admin/Upload.php`)
- Added detailed error handling and logging
- Added configuration logging
- Added test endpoint at `/admin/upload/test`

### 2. Updated Dockerfile
- Added PHP configuration for large file uploads
- Added Apache configuration for large file uploads
- Added health check script
- Fixed temp directory permissions

### 3. Updated Configuration Files
- Enhanced `index.php` with additional PHP settings
- Updated `.htaccess` for multiple PHP versions

## Testing Steps

### Step 1: Rebuild and Restart Docker Container
```bash
# Rebuild the Docker image
docker build -t your-app-name .

# Run the container
docker run -p 80:80 -p 6379:6379 your-app-name
```

### Step 2: Test PHP Configuration Inside Container
```bash
# Access the container
docker exec -it <container_id> bash

# Run the health check script
/usr/local/bin/health-check.sh

# Or run the PHP test script
php /var/www/html/test-upload-config.php
```

### Step 3: Test Upload Configuration via Web
1. Log into your application
2. Visit: `http://your-domain/admin/upload/test`
3. Check the JSON response for configuration details

### Step 4: Test Actual File Upload
1. Try uploading a 30MB file
2. Check the application logs for detailed error messages
3. Look for specific error codes and messages

## Common Issues and Solutions

### Issue: "No file data received in request"
**Cause**: File upload failed before reaching PHP
**Solution**: Check Apache configuration and file size limits

### Issue: "File upload error: The uploaded file exceeds the upload_max_filesize directive"
**Cause**: PHP upload limit is too low
**Solution**: Verify PHP configuration in container

### Issue: "File upload error: The uploaded file was only partially uploaded"
**Cause**: Upload timed out or connection dropped
**Solution**: Increase timeout settings

### Issue: "Temporary file is not accessible"
**Cause**: Temp directory permissions or space issues
**Solution**: Check temp directory permissions and disk space

## Configuration Values

The Docker container is configured with:
- `upload_max_filesize = 1000M`
- `post_max_size = 1000M`
- `max_execution_time = 5000`
- `max_input_time = 5000`
- `memory_limit = 1000M`
- Apache `LimitRequestBody = 1GB`
- Apache `Timeout = 300`

## Log Locations

- **Application logs**: Check your CodeIgniter log files
- **Apache logs**: `/var/log/apache2/` inside container
- **PHP error logs**: Check Apache error logs

## Troubleshooting Commands

```bash
# Check disk space
df -h

# Check temp directory
ls -la /tmp

# Check PHP configuration
php -i | grep -E "(upload|post|memory|timeout)"

# Check Apache configuration
apache2ctl -M | grep -E "(php|upload)"

# Monitor uploads in real-time
tail -f /var/log/apache2/error.log
```

## Expected Behavior

After implementing these changes:
1. ✅ 30MB files should upload successfully
2. ✅ Detailed error messages for failed uploads
3. ✅ Configuration logging for debugging
4. ✅ Proper timeout handling for large files

If you're still experiencing issues, check the logs for specific error messages and verify that all configuration changes have been applied correctly. 