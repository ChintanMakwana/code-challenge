# Guest Posts Submission from front-end
This WordPress Plugin allows authors to create custom post type posts from the front end. It will find the post types that are registered in your WordPress install and let you create posts of that type.

> **Note:** It will allow only **Author** role to use the form and show the list of pending posts.

### The Plugin provides 2 shortcodes
- **[wpgp-form]** It will show the form that contains fields like Post title, Post type, Post content, Post excerpt, Feature image, etc.
- **[wpgp-pending-posts]** It will show the list of pending posts that are submitted by the user. It has one attribute for the post_type. If it is not passed then it will show all the post types.

The form will be submitted via **AJAX** but before that, it will be validated through [jquery validate](https://jqueryvalidation.org/). It will be validated server-side as well and everything goes fine then the post will be inserted into the database.

In the end, one email will be sent to the admin regarding the post submitted by the user to verify it.
