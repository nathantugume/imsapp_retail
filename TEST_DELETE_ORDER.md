# Testing Delete Order Functionality

## Quick Test Steps

1. **Start the server** (if not already running):
   ```bash
   # Option 1: Using the start script
   ./desktop/start_ims.sh
   
   # Option 2: Manual PHP server
   php -S localhost:8080
   
   # Option 3: If using Apache/XAMPP, ensure it's running
   ```

2. **Access the Orders Page**:
   - Navigate to: `http://localhost/imsapp/order.php` (or your configured domain)
   - Or: `http://localhost:8080/order.php` (if using PHP built-in server)

3. **Verify Delete Button is Visible**:
   - Look for a red "Delete" button with trash icon in the Action column
   - It should be next to "Invoice" and "View" buttons
   - Each order row should have 3 action buttons

4. **Test Delete Functionality**:
   - Click the "Delete" button on any order
   - You should see a confirmation dialog: "Are you sure you want to delete this order? This action cannot be undone and will restore product stock."
   - Click "OK" to confirm
   - The order should be deleted and the table should refresh
   - A success message should appear at the top

5. **Check Browser Console** (F12):
   - Open Developer Tools (F12)
   - Go to Console tab
   - Look for any JavaScript errors
   - Check Network tab to see if the AJAX request to `orders/delete.php` is successful

## Expected Behavior

✅ **Success Case:**
- Delete button appears in the table
- Confirmation dialog shows
- Order is deleted from database
- Product stock is restored
- Table refreshes automatically
- Success message displays

❌ **If Issues Occur:**
- Check browser console for JavaScript errors
- Check Network tab for failed AJAX requests
- Verify PHP error logs
- Ensure database connection is working

## Manual Verification

You can also verify the code is loaded by:
1. Right-click on the Delete button → Inspect
2. Check if the button has class `del-order-btn` and attribute `del-id`
3. In Console, type: `$('.del-order-btn').length` - should return number of orders

## Troubleshooting

**Delete button not visible:**
- Clear browser cache (Ctrl+Shift+Delete)
- Hard refresh (Ctrl+F5)
- Check if orders exist in the database

**Delete not working:**
- Check browser console for errors
- Verify `orders/delete.php` is accessible
- Check PHP error logs
- Verify database permissions

**AJAX errors:**
- Check if jQuery is loaded
- Verify the domain path matches your setup
- Check CORS if using different domain

