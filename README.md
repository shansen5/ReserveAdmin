# OCOrders
Bread orders and bread baking management - PHP, MySQL

Ingredients - the component parts of the Items, the products that are delivered to customers
Items - The products
Customers - Those ordering and receiving the products
Locations - Pickup locations where customers can ask to pick up their products
Order - A customer's request for a certain quantity of a single Item.  This can be a 
subscription with the order repeated based on start and end dates, frequency (ONCE,
DAILY, WEEKLY, BI-WEEKLY, MONTHLY) and the day of the week.
WorkingOrder - Generated from the Order, this is the list of how many of each item must be
produced and delivered each day. The WorkingOrder is editable, so that a customer with a
subscription can change an individual day's order.  The WorkingOrder list also serves as
a record of items that have been delivered to aid in invoicing.
