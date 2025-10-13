# Reports Feature Documentation

## Overview
A comprehensive reporting system has been added to the Mini Price Hardware IMS application. This feature allows users to generate detailed sales and profit reports with flexible filtering options.

## Files Created/Modified

### New Files Created:
1. **`reports.php`** - Main reports page with filtering and data visualization
2. **`js/reports.js`** - JavaScript enhancements for the reports page
3. **`test-reports-connection.php`** - Test script to verify reports functionality
4. **`REPORTS_FEATURE.md`** - This documentation file

### Files Modified:
1. **`common/navbar.php`** - Added "Reports" link to navigation menu

## Features Implemented

### 1. **Multiple Report Types**
- **Daily Reports**: Filter sales data by specific date
- **Monthly Reports**: Filter sales data by month and year
- **Yearly Reports**: Filter sales data by year

### 2. **Summary Cards**
The reports page displays key metrics in visual cards:
- Total Orders
- Total Sales
- Total Paid
- Outstanding Balance
- **For Master Users Only:**
  - Total Profit
  - Total Revenue
  - Total Cost

### 3. **Detailed Data Table**
Each report includes a comprehensive DataTable with:
- Invoice Number
- Customer Name
- Subtotal
- Number of Items
- Discount
- Total Amount
- Amount Paid
- Balance Due
- Payment Method
- Order Date
- Summary row with totals

### 4. **Export Functionality**
Reports can be exported in multiple formats:
- **Excel** (.xlsx) - For spreadsheet analysis
- **PDF** - For printing and sharing
- **CSV** - For data import/export
- **Print** - Direct browser printing

### 5. **Security & Validation**
- Session authentication required
- User role verification (Master/User)
- Input validation for dates and filter types
- SQL injection prevention with prepared statements
- Error handling with logging

### 6. **Responsive Design**
- Mobile-friendly layout
- Bootstrap-based UI
- Adaptive tables for different screen sizes
- Touch-friendly controls

## How to Use

### Accessing Reports
1. Log in to the system
2. Click on **"Reports"** in the navigation menu
3. The reports page will load with today's daily report by default

### Generating Reports

#### Daily Report
1. Click the **"Daily"** filter button
2. Select a date using the date picker
3. Click **"Generate Report"**
4. View the results in the table below

#### Monthly Report
1. Click the **"Monthly"** filter button
2. Select a month and year using the month picker
3. Click **"Generate Report"**
4. View aggregated monthly data

#### Yearly Report
1. Click the **"Yearly"** filter button
2. Select a year from the dropdown
3. Click **"Generate Report"**
4. View annual sales summary

### Exporting Reports
1. Generate the desired report
2. Click one of the export buttons:
   - **Export Excel** - Download as .xlsx
   - **Export PDF** - Download as PDF
   - **Print** - Open print dialog
   - **Export CSV** - Download as .csv

### Understanding the Data

#### Summary Cards
- **Total Orders**: Number of orders in the selected period
- **Total Sales**: Sum of all net totals
- **Total Paid**: Sum of all payments received
- **Outstanding Balance**: Sum of all unpaid amounts
- **Total Profit** (Master only): Revenue minus cost
- **Total Revenue** (Master only): Total income from sales
- **Total Cost** (Master only): Total buying price of sold items

#### Table Columns
- **Invoice No**: Unique order identifier
- **Customer**: Customer name
- **Subtotal**: Order total before discounts
- **Items**: Number of products in the order
- **Discount**: Amount discounted
- **Total**: Final order amount
- **Paid**: Amount paid by customer
- **Balance**: Outstanding amount (red if unpaid)
- **Payment Method**: Cash, Credit, Mobile Money, etc.
- **Date**: Order date and time

## Technical Details

### Database Queries
The reports use optimized SQL queries with:
- JOIN operations between `orders` and `invoices` tables
- Date filtering using `STR_TO_DATE` for varchar date fields
- Aggregation functions for totals
- Grouping by invoice number

### Profit Calculations
- Uses the `ProfitCalculator` class from `functions/profit_calculator.php`
- Only includes products with valid buying prices (> 0)
- Calculates: Revenue - Cost = Profit
- Available for Master users only

### Date Handling
- Dates are validated using regex patterns
- Format: YYYY-MM-DD for daily, YYYY-MM for monthly
- Year range: 2000 to current year
- Invalid dates default to current date

### Error Handling
- Database errors are logged to error_log
- User-friendly error messages
- Graceful fallbacks for missing data
- Try-catch blocks for all database operations

## Testing

### Running the Test Script
1. Navigate to: `http://your-domain/test-reports-connection.php`
2. Review all test results
3. Ensure all tests show ✅ SUCCESS
4. If any test fails, check:
   - Database connection
   - Required tables exist
   - User is logged in
   - File permissions

### Test Coverage
The test script verifies:
1. Database connection
2. ProfitCalculator class initialization
3. Session status
4. Orders table accessibility
5. Invoices table accessibility
6. Products with buying prices
7. Daily report query functionality
8. Navigation configuration
9. Required JavaScript files
10. Reports page file existence

## Permissions

### Master Users Can:
- View all reports (daily, monthly, yearly)
- See profit, revenue, and cost metrics
- Export reports in all formats
- Access all historical data

### Regular Users Can:
- View all reports (daily, monthly, yearly)
- See sales data and order information
- Export reports in all formats
- Cannot see profit/cost data

## Browser Compatibility
- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support
- Mobile browsers: Responsive support

## Performance Considerations
- DataTables pagination limits display to 25 rows by default
- Database queries use indexed columns
- AJAX loading for better responsiveness
- Optimized SQL with proper JOINs

## Troubleshooting

### Issue: No data showing
**Solution**: 
- Check if orders exist for the selected period
- Verify date format in orders table
- Check database connection

### Issue: Profit showing as 0
**Solution**:
- Ensure products have buying_price set
- Check if you're logged in as Master user
- Verify ProfitCalculator class is loaded

### Issue: Export not working
**Solution**:
- Check if export libraries are loaded
- Verify JavaScript console for errors
- Ensure DataTables buttons are initialized

### Issue: Date filter not working
**Solution**:
- Check order_date format in database
- Verify STR_TO_DATE conversion
- Review error logs for SQL errors

## Future Enhancements (Potential)
- [ ] Graphical charts (bar, line, pie)
- [ ] Product-wise sales breakdown
- [ ] Category-wise analysis
- [ ] Customer payment history
- [ ] Comparative reports (period vs period)
- [ ] Scheduled automated reports
- [ ] Email report delivery
- [ ] Custom date range selection
- [ ] Profit margin analysis
- [ ] Top products by profit/revenue

## Support
For issues or questions:
1. Check the test script: `test-reports-connection.php`
2. Review error logs
3. Verify database structure
4. Check session and authentication

## Credits
- Built for Mini Price Hardware
- Uses Bootstrap 4 for UI
- DataTables for table functionality
- jQuery for JavaScript operations
- Font Awesome for icons

---

**Last Updated**: October 2025
**Version**: 1.0
**Status**: Production Ready ✅

