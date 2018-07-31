## Integrations
This is an app to host all your custom integrations

#### Installation
Download or git clone or the repository into a sub-folder on the server
```bash
go to the root directory of your website
$ mkdir integrations
$ cd integrations
$ git clone git@bitbucket.org:pasok/integrations.git .
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
MAIL_TO_ADDRESS='example@example.com'

# woopra project id
WOOPRA_PROJECT=''
```

#### TODO
- ~~write improvely integration to show the app can be used for multiple integrations~~
- find a better way instead of using $_session to store temp data
- find better dependency injection as slim DI is restrictive (low priority)
