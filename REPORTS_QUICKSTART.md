# Reports Feature - Quick Start Guide

## 🚀 Getting Started in 3 Steps

### Step 1: Test the Connection
Before using the reports, verify everything is working:

```bash
# Open in your browser:
http://your-domain/test-reports-connection.php
```

✅ All tests should pass (show green checkmarks)

### Step 2: Access Reports
1. Log in to your IMS application
2. Click **"Reports"** in the top navigation bar
3. You'll see today's daily report by default

### Step 3: Generate Your First Report

#### For Daily Report:
- Click **"Daily"** button
- Select a date
- Click **"Generate Report"**

#### For Monthly Report:
- Click **"Monthly"** button
- Select month/year
- Click **"Generate Report"**

#### For Yearly Report:
- Click **"Yearly"** button
- Select year
- Click **"Generate Report"**

## 📊 What You'll See

### Summary Cards (Top of Page)
```
┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
│ Total       │ │ Total       │ │ Total       │ │ Outstanding │
│ Orders      │ │ Sales       │ │ Paid        │ │ Balance     │
└─────────────┘ └─────────────┘ └─────────────┘ └─────────────┘
```

### Detailed Table (Bottom)
- All orders for the selected period
- Invoice numbers, customers, amounts
- Payment status and methods
- Exportable to Excel/PDF/CSV

## 💡 Quick Tips

### Export Reports
1. Generate any report
2. Click export button at top of table:
   - 📊 **Export Excel** - For analysis
   - 📄 **Export PDF** - For printing
   - 📋 **Export CSV** - For data transfer
   - 🖨️ **Print** - Direct printing

### Master User Features
If you're logged in as **Master**:
- You'll see **Profit**, **Revenue**, and **Cost** cards
- These show financial insights for the period
- Based on products with buying prices set

### Regular User Features
If you're logged in as **User**:
- You'll see order counts and sales totals
- No profit/cost information visible
- Can still export all reports

## 🔍 Understanding the Data

### Color Codes
- 🟢 **Green** = Paid amounts (positive)
- 🔴 **Red** = Outstanding balance (unpaid)
- 🔵 **Blue** = General information
- 🟠 **Orange** = Warnings or notices

### Date Formats
- **Daily**: Specific date (Oct 09, 2025)
- **Monthly**: Month and year (October 2025)
- **Yearly**: Year only (2025)

## ❓ Common Questions

### Q: Why is profit showing as 0?
**A**: Make sure:
- You're logged in as Master user
- Products have buying_price set in database
- There are sales for the selected period

### Q: Can I select a custom date range?
**A**: Currently supports:
- Single day (Daily)
- Full month (Monthly)
- Full year (Yearly)

### Q: Where are exports saved?
**A**: Downloads go to your browser's default download folder

### Q: Can I schedule automated reports?
**A**: Not yet - this is a planned future feature

## 🛠️ Troubleshooting

### No Data Showing?
1. Check if orders exist for that period
2. Try a different date range
3. Verify you're logged in

### Export Not Working?
1. Check browser console for errors
2. Ensure pop-ups are allowed
3. Try a different browser

### Error Messages?
1. Run the test script: `test-reports-connection.php`
2. Check database connection
3. Review error logs

## 📞 Need Help?

1. **Test First**: Run `test-reports-connection.php`
2. **Check Docs**: See `REPORTS_FEATURE.md` for detailed info
3. **Review Logs**: Check server error logs
4. **Database**: Verify orders and invoices tables

## 🎯 Next Steps

After mastering basic reports:
1. Try different time periods
2. Export reports in various formats
3. Compare different months/years
4. Use data for business decisions

---

**Happy Reporting! 📈**

Need detailed documentation? See **REPORTS_FEATURE.md**


