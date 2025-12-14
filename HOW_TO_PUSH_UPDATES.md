# 📤 How to Push Updates to GitHub

## Overview

The auto-update system **pulls** updates from GitHub. You need to **push** your changes to GitHub manually using Git commands. This document explains the complete workflow.

## 🔄 Update Workflow

### 1. **Make Changes Locally**
   - Edit files in your local project
   - Test changes thoroughly
   - Example: You just added the delete order button feature

### 2. **Commit Changes to Git**
   ```bash
   cd /home/nathan/shop_mgt/latest_retail
   
   # Check what files changed
   git status
   
   # Add changed files
   git add .
   # OR add specific files:
   git add orders/Order.php orders/delete.php orders/index.php js/order.js
   
   # Commit with descriptive message
   git commit -m "Add delete order functionality for admin"
   
   # Optional: Include version in commit message for better tracking
   git commit -m "v2025.10.14 - Add delete order button with stock restoration"
   ```

### 3. **Push to GitHub**
   ```bash
   # Push to main branch (this is what the update system checks)
   git push origin main
   
   # If main branch doesn't exist or you need to set upstream:
   git push -u origin main
   ```

### 4. **Update System Detects Changes**
   - The update system checks: `https://api.github.com/repos/nathantugume/imsapp_retail/commits/main`
   - Compares your local `version.json` commit hash with GitHub's latest commit
   - If different → Update available!

### 5. **Update version.json (Optional but Recommended)**
   After pushing, you can update `version.json` locally to match:
   ```bash
   # Get latest commit hash
   git rev-parse --short HEAD
   
   # Update version.json manually or let the update system do it
   ```

## 📋 Step-by-Step: Pushing Your Delete Order Feature

### Current Status
- ✅ Code is complete and tested locally
- ❌ Not yet pushed to GitHub
- ❌ Users won't see update until you push

### Steps to Push:

```bash
# 1. Navigate to project
cd /home/nathan/shop_mgt/latest_retail

# 2. Check Git status
git status

# 3. Add the new files/changes
git add orders/Order.php
git add orders/delete.php
git add orders/index.php
git add js/order.js

# 4. Commit with message
git commit -m "Add delete order functionality - Admin can delete orders with automatic stock restoration"

# 5. Push to GitHub
git push origin main
```

## 🔍 How the Update System Works

### Detection Process:
1. **GitHub API Call**: 
   - URL: `https://api.github.com/repos/nathantugume/imsapp_retail/commits/main`
   - Gets latest commit info (hash, message, date, author)

2. **Version Comparison**:
   - Reads local `version.json`:
     ```json
     {
         "version": "2025.10.13",
         "commit": "00203ca",  // ← This is compared
         "date": "2025-10-13 12:10:45",
         "branch": "main"
     }
     ```
   - Compares `commit` hash with GitHub's latest commit hash
   - If different → Update available!

3. **Download Process**:
   - Downloads ZIP from: `https://github.com/nathantugume/imsapp_retail/archive/refs/heads/main.zip`
   - Extracts and applies files
   - Updates `version.json` with new commit hash

## 🎯 Best Practices

### 1. **Commit Messages**
   Use descriptive commit messages:
   ```bash
   # Good
   git commit -m "Add delete order button with stock restoration"
   git commit -m "v2025.10.14 - Fix order deletion bug"
   
   # Bad
   git commit -m "update"
   git commit -m "fix"
   ```

### 2. **Version in Commit Message**
   The system extracts version from commit message if present:
   - Pattern: `v1.0.0` or `version 1.0.0`
   - If not found, uses date: `2025.10.14`

### 3. **Test Before Pushing**
   ```bash
   # Test locally first
   php -S localhost:8080
   # Test the delete button functionality
   
   # Then push
   git push origin main
   ```

### 4. **Update version.json After Push**
   ```bash
   # Get the commit hash you just pushed
   git rev-parse --short HEAD
   
   # Update version.json manually or let update system do it
   ```

## 🔐 GitHub Repository Configuration

### Current Setup:
- **Repository**: `nathantugume/imsapp_retail`
- **Branch Tracked**: `main`
- **Update URL**: `https://github.com/nathantugume/imsapp_retail/archive/refs/heads/main.zip`

### To Change Repository:
Edit `system/UpdateChecker.php`:
```php
private $github_user = 'nathantugume';
private $github_repo = 'imsapp_retail';
```

## 🚨 Troubleshooting

### "Failed to push to GitHub"
**Problem**: Authentication or permission issue  
**Solution**:
```bash
# Check remote URL
git remote -v

# Set up authentication (if needed)
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# Use SSH instead of HTTPS (more secure)
git remote set-url origin git@github.com:nathantugume/imsapp_retail.git
```

### "Update not detected after push"
**Problem**: Cache or timing issue  
**Solution**:
1. Wait a few seconds (GitHub API may have slight delay)
2. Clear browser cache
3. Check commit hash matches:
   ```bash
   # Local
   git rev-parse --short HEAD
   
   # Check on GitHub web interface
   ```

### "Update system shows wrong version"
**Problem**: `version.json` not updated  
**Solution**:
- The update system will update it automatically when update is installed
- Or update manually after push:
  ```bash
  git rev-parse --short HEAD > /tmp/commit_hash
  # Then update version.json with that hash
  ```

## 📝 Quick Reference

### Push New Feature:
```bash
git add .
git commit -m "Description of changes"
git push origin main
```

### Check Current Commit:
```bash
git rev-parse --short HEAD
```

### View Commit History:
```bash
git log --oneline -10
```

### Check Remote Status:
```bash
git status
git remote -v
```

## 🎯 For Your Delete Order Feature

**To push your delete order changes right now:**

```bash
cd /home/nathan/shop_mgt/latest_retail

# Check what needs to be committed
git status

# Add the delete order files
git add orders/Order.php orders/delete.php orders/index.php js/order.js

# Commit
git commit -m "Add delete order functionality - Admin can delete orders with automatic stock restoration"

# Push to GitHub
git push origin main
```

After pushing, the update system will detect the new commit and users can update!

---

**Note**: The update system only **pulls** from GitHub. You must **push** changes manually using Git commands.

