# HTMLy comment system
A commenting system integrated in HTMLy, featuring:
* threaded comments (comments and replies)
* antispam (with no external dependencies, no CAPTCHA)
* notification system and thread subscription

## 2025-12-26
Some major fixes to comment system:
* added English strings in notification emails (needs translations in all other languages)
* improved antispam system
* added subscription verification system

### Antispam
Antispam work using a honeyspot and js/token verification

* honeyspot: field "website" is added as hidden - spambot usually fill it, all comments with this field not empty are discarded as SPAM
* js: javascript must be enabled to have a comment being considered not SPAM - all modern browser have js enabled
* token: a token with encrypted timestamp is generated and added to "company" hidden field - a comment have to be submitted between 3 and 600 seconds from token generation (this should prevent automated submissions (before 3 seconds) and luckily forged tokens (converting in a number, probably resulting in less than 3 or more than 600 seconds difference)

Both methods can be enabled/disabled from comment system configuration page.

## Subscriptions
Users can ask for email notification when a new comment is published in a subscribed post thread. A confirmation email is sent to the user email, and subscription must be confirmed clicking on a link. Only confirmed subscription users will receive notification emails.
Notification email are sent on comment publish (if validation is enabled) or comment insert (if moderation is disabled, not recommended).

**TODO**: limit comment insert by time from same IP address

**TODO**: reworking backend functions to use HTMLy basic functions and avoid code duplication
