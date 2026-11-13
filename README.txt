# Glassmart Sales, Stock & Remote Orders Monitor

## Run the prototype
1. Download and extract `glassmart_website.zip`.
2. Open `index.html` in a modern browser.
3. Try recording a sale, adjusting inventory, submitting a remote order, and viewing the simulated alert log.

## Included
- Dashboard with sales, stock, pending orders, and low-stock counts
- Staff sale recording and automatic stock deduction
- Inventory management and low-stock flags
- Customer order form and order-status management
- Simulated SMS alert log
- Browser localStorage persistence

## Important limitations
This is a front-end prototype for demonstration, not a production-ready online system. Data stays in the current browser and does not sync between users/devices. SMS alerts are simulated, not actually sent. For a live deployment, build a backend (for example PHP or Node.js), a MySQL database, login/role permissions, server-side validation, backups, HTTPS, and an SMS gateway integration. Never put SMS provider secret keys in browser JavaScript.
