# 🔐 GitHub Push Authentication Guide

## Current Status
✅ **Commit successful**: Your changes are committed locally  
❌ **Push pending**: Need to authenticate with GitHub

**Commit Hash**: `2f6390c`  
**Commit Message**: "Add delete order functionality - Admin can delete orders with automatic stock restoration"

## 🔑 Authentication Options

### Option 1: Personal Access Token (Recommended - Easiest)

1. **Create a Personal Access Token on GitHub**:
   - Go to: https://github.com/settings/tokens
   - Click "Generate new token" → "Generate new token (classic)"
   - Name: "IMS Retail Push"
   - Expiration: Choose your preference (90 days, 1 year, or no expiration)
   - Scopes: Check `repo` (full control of private repositories)
   - Click "Generate token"
   - **Copy the token immediately** (you won't see it again!)

2. **Use the token to push**:
   ```bash
   cd /home/nathan/shop_mgt/latest_retail
   git push https://YOUR_TOKEN@github.com/nathantugume/imsapp_retail.git main
   ```
   
   Or set it up permanently:
   ```bash
   git remote set-url origin https://YOUR_TOKEN@github.com/nathantugume/imsapp_retail.git
   git push origin main
   ```

### Option 2: SSH Keys (More Secure, One-Time Setup)

1. **Check if you have SSH keys**:
   ```bash
   ls -la ~/.ssh/id_*.pub
   ```

2. **If no keys, generate them**:
   ```bash
   ssh-keygen -t ed25519 -C "your_email@example.com"
   # Press Enter to accept default location
   # Optionally set a passphrase
   ```

3. **Add SSH key to GitHub**:
   ```bash
   cat ~/.ssh/id_ed25519.pub
   # Copy the output
   ```
   - Go to: https://github.com/settings/keys
   - Click "New SSH key"
   - Paste your public key
   - Click "Add SSH key"

4. **Switch remote to SSH**:
   ```bash
   cd /home/nathan/shop_mgt/latest_retail
   git remote set-url origin git@github.com:nathantugume/imsapp_retail.git
   git push origin main
   ```

### Option 3: GitHub CLI (gh)

If you have GitHub CLI installed:
```bash
gh auth login
gh repo set-default nathantugume/imsapp_retail
git push origin main
```

## 🚀 Quick Push (Choose One Method)

### Method A: Personal Access Token (One-time)
```bash
cd /home/nathan/shop_mgt/latest_retail

# Replace YOUR_TOKEN with your actual token
git remote set-url origin https://YOUR_TOKEN@github.com/nathantugume/imsapp_retail.git
git push origin main

# After push, you can change back to regular URL (optional)
git remote set-url origin https://github.com/nathantugume/imsapp_retail.git
```

### Method B: SSH (If you have SSH keys set up)
```bash
cd /home/nathan/shop_mgt/latest_retail
git remote set-url origin git@github.com:nathantugume/imsapp_retail.git
git push origin main
```

### Method C: Manual Credential Entry
```bash
cd /home/nathan/shop_mgt/latest_retail
git push origin main
# When prompted:
# Username: nathantugume
# Password: [paste your Personal Access Token]
```

## ✅ After Successful Push

Once pushed, the update system will:
1. Detect the new commit on GitHub
2. Show "NEW" badge on Updates button
3. Allow users to install the update

**Your commit hash**: `2f6390c`  
**After push, users will see this as the latest version**

## 🔍 Verify Push Success

After pushing, verify:
```bash
# Check remote status
git status

# View commit on GitHub
# Visit: https://github.com/nathantugume/imsapp_retail/commit/2f6390c
```

## 🆘 Troubleshooting

### "Permission denied"
- Check your token has `repo` scope
- Verify SSH key is added to GitHub
- Check repository permissions

### "Repository not found"
- Verify repository name: `nathantugume/imsapp_retail`
- Check you have push access

### "Authentication failed"
- Token might be expired
- SSH key might not be added to GitHub
- Try regenerating token/SSH key

---

**Need help?** Choose one of the methods above and run the commands. The Personal Access Token method is usually the quickest!

