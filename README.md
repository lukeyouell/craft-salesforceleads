<img src="src/icon.svg" alt="icon" width="143" height="100">

# Salesforce Leads plugin for Craft CMS 3.x

Generate Salesforce leads from form submissions.

## Requirements

This plugin requires Craft CMS 3.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require lukeyouell/craft-salesforceleads

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Salesforce Leads.

## Usage

Your form template can look something like this:

```twig
<form action="" method="post">

  {{ csrfInput() }}
  <input type="hidden" name="action" value="salesforce-leads/post">
  <input type="hidden" name="redirect" value="{{ 'contact/thanks'|hash }}">
  <input type="hidden" name="lead_source" value="{{ 'Web'|hash }}">
  <input type="hidden" name="Campaign_ID" value="{{ 'FA4316257629E'|hash }}">

  <label>Name</label>
  <input type="text" name="name">

  <label>Email Address</label>
  <input type="email" name="email">
  
  <label>Message</label>
  <textarea name="message"></textarea>

  <input type="submit" value="Submit">

</form>
```

### Salesforce Parameters

The following Salesforce parameters are available **but must contain hashed values** to prevent tampering.

| Name          | Required | Default Value                |
| ------------- | -------- | ---------------------------- |
| `oid`         | No       | Value set in settings/config |
| `retURL`      | No       | Current site base url        |
| `lead_source` | No       | Null                         |
| `Campaign_ID` | No       | Null                         |

The above field names are **case-sensitive**.

### Redirecting After Submit

If you have a `redirect` hidden input, the user will be redirected to it upon successful submission. Again, this must be a hashed value.

If you **don't** have a `redirect` hidden input, the plugin will respond with json.

### Ajax Submissions

You can optionally post contact form submissions over Ajax if you’d like. Just send a POST request to your site with all of the same data that would normally be sent:

```javascript
$('#myForm').submit(function(ev) {
  // Prevent the form from actually submitting
  ev.preventDefault();

  // Send it to the server
  $.post({
    url: '/',
    dataType: 'json',
    data: $(this).serialize(),
    success: function(response) {
      if (response.success) {
        alert('Successful submission.');
      } else {
        alert('An error occurred. Please try again.');
      }
    }
  });
});
```

### Handling Responses

We are posting to Salesforce using a HTTP POST request, so we don't receive any meaningful feedback back. The request will only error if there is a problem with the request itself.

#### Successful

```json
{
   "success": true,
   "statusCode": 200,
   "reason": "OK",
   "body": "\r\n<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n<html>\n<head>\n<meta HTTP-EQUIV=\"PRAGMA\" CONTENT=\"NO-CACHE\">\n<meta http-equiv=\"Refresh\" content=\"0; URL=https://www.yoursite.com/\">\n</head>\n<script>if (this.SfdcApp && this.SfdcApp.projectOneNavigator) { SfdcApp.projectOneNavigator.handleRedirect('https://www.yoursite.com/'); }  else if (window.location.replace){ window.location.replace('https://www.yoursite.com/');} else {;window.location.href ='https://www.yoursite.com/';} </script></html>\n",
   "payload": {
      "Campaign_ID": "FA4316257629E",
      "name": "Joe Bloggs",
      "email": "joe.bloggs@email.com",
      "message": "Ut felis ipsum, pulvinar id elit in, tempor sagittis lacus. In lectus quam, consequat eu nibh vel, maximus lobortis sapien.",
      "oid": "936A151D88D8C",
      "retURL": "https://www.yoursite.com",
      "lead_source": "Web"
   }
}
```

#### Unsuccessfull

```json
{
   "success": false,
   "reason": "Error message will be shown here",
   "payload": {
      "Campaign_ID": "FA4316257629E",
      "name": "Joe Bloggs",
      "email": "joe.bloggs@email.com",
      "message": "Ut felis ipsum, pulvinar id elit in, tempor sagittis lacus. In lectus quam, consequat eu nibh vel, maximus lobortis sapien.",
      "oid": "936A151D88D8C",
      "retURL": "https://www.yoursite.com",
      "lead_source": "Web"
   }
}
```
