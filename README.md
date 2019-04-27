## Integrations
This is an app to host custom samcart integrations. events(like orders, refunds) received from samcart are processed and posted to analytics apps like woopra/improvely.
ref: https://intercom.help/samcart/integrations/other/samcart-notification-url

#### Installation
Download or git clone or the repository into a sub-folder locally on your laptop
```bash
$ git clone git@bitbucket.org:pasok/integrations.git integrations
```

Use [Composer](https://getcomposer.org/) to install dependencies
```bash
$ composer install
```

Create .env from .env.example
```bash
$ cp app/.env.example app/.env
```

Make sure to set these properties to desired values in app/.env
```bash
# integration report will be sent to this email
MAIL_TO_ADDRESS=''

# for woopra integration
WOOPRA_PROJECT=''

#for improvely integration
IMPROVELY_API_KEY=''
IMPROVELY_PROJECT=''
```

#### TODO
- ~~write improvely integration to show the app can be used for multiple integrations~~
- ~~find better dependency injection as slim DI is restrictive~~
- find a better way instead of using $_session to store temp data
