-- Header Menu Items SQL
-- This script inserts default header menu items into the general_options table
-- Run this after installing the dynamic menu system

-- Delete any existing header_menu_items entry first
DELETE FROM `general_options` WHERE `name` = 'header_menu_items';

-- Insert all default header menu items
INSERT INTO `general_options` (`name`, `value`) VALUES (
'header_menu_items',
'[
  {"id":1,"title":"Dashboard","url":"statistics","icon":"fe fe-bar-chart-2","roles":["user","admin","supporter"],"new_tab":0,"status":1,"sort_order":1},
  {"id":2,"title":"New Order","url":"order/add","icon":"fe fe-shopping-cart","roles":["user","admin","supporter"],"new_tab":0,"status":1,"sort_order":2},
  {"id":3,"title":"Orders","url":"order/log","icon":"fa fa-shopping-cart","roles":["user","admin","supporter"],"new_tab":0,"status":1,"sort_order":3},
  {"id":4,"title":"Refill","url":"refill/log","icon":"fa fa-recycle","roles":["user","admin","supporter"],"new_tab":0,"status":1,"sort_order":4},
  {"id":5,"title":"Category","url":"category","icon":"fa fa-table","roles":["admin","supporter"],"new_tab":0,"status":1,"sort_order":5},
  {"id":6,"title":"Services","url":"services","icon":"fe fe-list","roles":["everyone"],"new_tab":0,"status":1,"sort_order":6},
  {"id":7,"title":"Add Funds","url":"add_funds","icon":"fa fa-money","roles":["user","admin"],"new_tab":0,"status":1,"sort_order":7},
  {"id":8,"title":"API","url":"api/docs","icon":"fe fe-share-2","roles":["user","supporter"],"new_tab":0,"status":1,"sort_order":8},
  {"id":9,"title":"Tickets","url":"tickets","icon":"fa fa-comments-o","roles":["user","admin","supporter"],"new_tab":0,"status":1,"sort_order":9},
  {"id":10,"title":"Affiliate","url":"affiliate","icon":"fa fa-money","roles":["user","admin","supporter"],"new_tab":0,"status":1,"sort_order":10},
  {"id":11,"title":"Child Panel","url":"childpanel/add","icon":"fa fa-child","roles":["user","admin","supporter"],"new_tab":0,"status":1,"sort_order":11},
  {"id":12,"title":"Transaction Logs","url":"transactions","icon":"fe fe-calendar","roles":["user","admin","supporter"],"new_tab":0,"status":1,"sort_order":12},
  {"id":13,"title":"Balance Logs","url":"balance_logs","icon":"fe fe-activity","roles":["user","admin","supporter"],"new_tab":0,"status":1,"sort_order":13},
  {"id":14,"title":"Users","url":"users","icon":"fe fe-users","roles":["admin","supporter"],"new_tab":0,"status":1,"sort_order":14},
  {"id":15,"title":"Subscribers","url":"subscribers","icon":"fa fa-user-circle-o","roles":["admin","supporter"],"new_tab":0,"status":1,"sort_order":15},
  {"id":16,"title":"System Settings","url":"setting","icon":"fa fa-cog","roles":["admin","supporter"],"new_tab":0,"status":1,"sort_order":16},
  {"id":17,"title":"Currencies","url":"currencies","icon":"fa fa-usd","roles":["admin","supporter"],"new_tab":0,"status":1,"sort_order":17},
  {"id":18,"title":"WhatsApp Management","url":"whatsapp","icon":"fa fa-whatsapp","roles":["admin","supporter"],"new_tab":0,"status":1,"sort_order":18},
  {"id":19,"title":"Services Providers","url":"api_provider","icon":"fa fa-share-alt","roles":["admin","supporter"],"new_tab":0,"status":1,"sort_order":19},
  {"id":20,"title":"Payments","url":"payments","icon":"fa fa-credit-card","roles":["admin","supporter"],"new_tab":0,"status":1,"sort_order":20},
  {"id":21,"title":"Announcement","url":"news","icon":"fa fa-bell","roles":["admin"],"new_tab":0,"status":1,"sort_order":21},
  {"id":22,"title":"FAQs","url":"faqs","icon":"fa fa-book","roles":["admin"],"new_tab":0,"status":1,"sort_order":22},
  {"id":23,"title":"Language","url":"language","icon":"fa fa-language","roles":["admin"],"new_tab":0,"status":1,"sort_order":23},
  {"id":24,"title":"Account","url":"profile","icon":"fa fa-user","roles":["user","admin","supporter"],"new_tab":0,"status":1,"sort_order":24},
  {"id":25,"title":"Sign Out","url":"auth/logout","icon":"fa fa-power-off","roles":["user","admin","supporter"],"new_tab":0,"status":1,"sort_order":25}
]'
);
